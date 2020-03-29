<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Utils;

/**
 * Represents a Snowflake.
 * @property float       $timestamp  The timestamp of when this snowflake got generated. In seconds with microseconds.
 * @property int         $workerID   The ID of the worker which generated this snowflake.
 * @property int         $processID  The ID of the process which generated this snowflake.
 * @property int         $increment  The increment index of the snowflake.
 * @property string      $binary     The binary representation of this snowflake.
 * @property string|int  $value      The snowflake value.
 * @property \DateTime   $date       A DateTime instance of the timestamp.
 */
class Snowflake {
    /**
     * Time since UNIX epoch to Discord epoch.
     * @var int
     */
    const EPOCH = 1420070400;
    
    protected static $incrementIndex = 0;
    protected static $incrementTime = 0;
    
    protected $value;
    protected $timestamp;
    protected $workerID;
    protected $processID;
    protected $increment;
    protected $binary;
    
    /**
     * Constructor.
     * @param string|int  $snowflake
     * @throws \InvalidArgumentException
     */
    function __construct($snowflake) {
        if(\PHP_INT_SIZE === 4) {
            $this->value = $snowflake;
            $this->binary = \str_pad(\base_convert($snowflake, 10, 2), 64, 0, \STR_PAD_LEFT);
            
            $time = \base_convert(\substr($this->binary, 0, 42), 2, 10);
            
            $this->timestamp = (float) ((((int) \substr($time, 0, -3)) + static::EPOCH).'.'.\substr($time, -3));
            $this->workerID = (int) \base_convert(\substr($this->binary, 42, 5), 2, 10);
            $this->processID = (int) \base_convert(\substr($this->binary, 47, 5), 2, 10);
            $this->increment = (int) \base_convert(\substr($this->binary, 52, 12), 2, 10);
        } else {
            $snowflake = (int) $snowflake;
            $this->value = $snowflake;
            
            $this->binary = \str_pad(\decbin($snowflake), 64, 0, \STR_PAD_LEFT);
            
            $time = (string) ($snowflake >> 22);
            
            $this->timestamp = (float) ((((int) \substr($time, 0, -3)) + static::EPOCH).'.'.\substr($time, -3));
            $this->workerID = ($snowflake & 0x3E0000) >> 17;
            $this->processID = ($snowflake & 0x1F000) >> 12;
            $this->increment = ($snowflake & 0xFFF);
        }
        
        if($this->timestamp < static::EPOCH || $this->workerID < 0 || $this->workerID >= 32 || $this->processID < 0 || $this->processID >= 32 || $this->increment < 0 || $this->increment >= 4096) {
            throw new \InvalidArgumentException('Invalid snow in snowflake');
        }
    }
    
    /**
     * @return mixed
     * @throws \Exception
     * @internal
     */
    function __get($name) {
        switch($name) {
            case 'timestamp':
            case 'workerID':
            case 'processID':
            case 'increment':
            case 'binary':
                return $this->$name;
            break;
            case 'date':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime(((int) $this->timestamp));
            break;
        }
        
        throw new \Exception('Undefined property: '.\get_class($this).'::$'.$name);
    }
    
    /**
     * Deconstruct a snowflake.
     * @param string|int  $snowflake
     * @return Snowflake
     */
    static function deconstruct($snowflake) {
        return (new self($snowflake));
    }
    
    /**
     * Generates a new snowflake.
     * @param int  $workerID   Valid values are in the range of 0-31.
     * @param int  $processID  Valid values are in the range of 0-31.
     * @return string
     */
    static function generate(int $workerID = 1, int $processID = 0) {
        if($workerID > 31 || $workerID < 0) {
            throw new \InvalidArgumentException('Worker ID is out of range');
        }
        
        if($processID > 31 || $processID < 0) {
            throw new \InvalidArgumentException('Process ID is out of range');
        }
        
        $time = \microtime(true);
        
        if($time === static::$incrementTime) {
            static::$incrementIndex++;
            
            if(static::$incrementIndex >= 4095) {
                \usleep(1000);
                
                $time = \microtime(true);
                static::$incrementIndex = 0;
            }
        } else {
            static::$incrementIndex = 0;
            static::$incrementTime = $time;
        }
        
        $workerID = \str_pad(\decbin($workerID), 5, 0, \STR_PAD_LEFT);
        $processID = \str_pad(\decbin($processID), 5, 0, \STR_PAD_LEFT);
        
        $mtime = \explode('.', ((string) $time));
        if(\count($mtime) < 2) {
            $mtime[1] = '000';
        }
        
        $time = ((string) (((int) $mtime[0]) - static::EPOCH)).\substr($mtime[1], 0, 3);
        
        if(\PHP_INT_SIZE === 4) {
            $binary = \str_pad(\base_convert($time, 10, 2), 42, 0, \STR_PAD_LEFT).$workerID.$processID.\str_pad(\decbin(static::$incrementIndex), 12, 0, \STR_PAD_LEFT);
            return \base_convert($binary, 2, 10);
        } else {
            $binary = \str_pad(\decbin(((int) $time)), 42, 0, \STR_PAD_LEFT).$workerID.$processID.\str_pad(\decbin(static::$incrementIndex), 12, 0, \STR_PAD_LEFT);
            return ((string) \bindec($binary));
        }
    }
    
    /**
     * Compute the shard ID from the snowflake.
     * @param int  $shardCount
     * @return int
     */
    function getShardID(int $shardCount) {
        if(\PHP_INT_SIZE === 4) {
            $time = \base_convert(\substr($this->binary, 0, 42), 2, 10);
            $shard = (int) \bcmod($time, ((string) $shardCount));
            
            return $shard;
        } else {
            $time = $this->value >> 22;
            return ($time % $shardCount);
        }
    }
}
