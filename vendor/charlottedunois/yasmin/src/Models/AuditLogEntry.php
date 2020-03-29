<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Models;

/**
 * Represents a guild audit log entry.
 *
 * @property \CharlotteDunois\Yasmin\Models\AuditLog                                                    $log               The guild audit log which this entry belongs to.
 * @property string                                                                                     $id                The ID of the audit log.
 * @property array[]                                                                                    $changes           Specific property changes.
 * @property string                                                                                     $userID            The ID of the user which triggered the audit log.
 * @property string                                                                                     $actionType        Specific action type of this entry in its string presentation.
 * @property string|null                                                                                $reason            The specified reason, or null.
 * @property int                                                                                        $createdTimestamp  When this audit log entry was created.
 * @property mixed|null                                                                                 $extra             Any extra data from the entry, or null.
 * @property mixed|null                                                                                 $target            The target of this entry, or null.
 *
 * @property \DateTime                                                                                  $createdAt         The DateTime instance of createdTimestamp.
 * @property \CharlotteDunois\Yasmin\Models\User|null                                                   $user              The user which triggered the audit log.
 */
class AuditLogEntry extends ClientBase {
    /**
     * All available actions keyed under their names to their numeric values.
     * @var int[]
     * @source
     */
    const ACTION_TYPES = array(
        'ALL' => null,
        'GUILD_UPDATE' => 1,
        'CHANNEL_UPDATE' => 11,
        'CHANNEL_CREATE' => 10,
        'CHANNEL_OVERWRITE_CREATE' => 13,
        'CHANNEL_DELETE' => 12,
        'CHANNEL_OVERWRITE_UPDATE' => 14,
        'CHANNEL_OVERWRITE_DELETE' => 15,
        'MEMBER_KICK' => 20,
        'MEMBER_PRUNE' => 21,
        'MEMBER_BAN_ADD' => 22,
        'MEMBER_BAN_REMOVE' => 23,
        'MEMBER_UPDATE' => 24,
        'MEMBER_ROLE_UPDATE' => 25,
        'ROLE_CREATE' => 30,
        'ROLE_UPDATE' => 31,
        'ROLE_DELETE' => 32,
        'INVITE_CREATE' => 40,
        'INVITE_UPDATE' => 41,
        'INVITE_DELETE' => 42,
        'WEBHOOK_CREATE' => 50,
        'WEBHOOK_UPDATE' => 51,
        'WEBHOOK_DELETE' => 52,
        'EMOJI_CREATE' => 60,
        'EMOJI_UPDATE' => 61,
        'EMOJI_DELETE' => 62,
        'MESSAGE_DELETE' => 72
    );
    
    /**
     * The guild audit log which this entry belongs to.
     * @var \CharlotteDunois\Yasmin\Models\AuditLog
     */
    protected $log;
    
    /**
     * The ID of the audit log.
     * @var string
     */
    protected $id;
    
    /**
     * Specific property changes.
     * @var array[]
     */
    protected $changes;
    
    /**
     * The ID of the user which triggered the audit log.
     * @var string
     */
    protected $userID;
    
    /**
     * Specific action type of this entry in its string presentation.
     * @var string
     */
    protected $actionType;
    
    /**
     * The specified reason, or null.
     * @var string|null
     */
    protected $reason;
    
    /**
     * When this audit log entry was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * Any extra data from the entry, or null.
     * @var mixed|null
     */
    protected $extra;
    
    /**
     * The target of this entry, or null.
     * @var mixed|null
     */
    protected $target;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\AuditLog $log, array $entry) {
        parent::__construct($client);
        $this->log = $log;
        
        $this->id = (string) $entry['id'];
        $this->changes = $entry['changes'] ?? array();
        $this->userID = (string) $entry['user_id'];
        $this->actionType = (\array_search($entry['action_type'], self::ACTION_TYPES, true) ?: '');
        $this->reason = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($entry['reason'] ?? null), 'string');
        
        $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        
        if(!empty($entry['options'])) {
            if($this->actionType === self::ACTION_TYPES['MEMBER_PRUNE']) {
                $this->extra = array(
                    'removed' => $entry['options']['members_removed'],
                    'days' => $entry['options']['delete_member_days']
                );
            } elseif($this->actionType === self::ACTION_TYPES['MESSAGE_DELETE']) {
                $this->extra = array(
                    'count' => $entry['options']['count'],
                    'channel' => $this->client->channels->get($entry['options']['channel_id'])
                );
            } elseif(!empty($entry['options']['type'])) {
                switch($entry['options']['type']) {
                    case 'member':
                        $this->extra = $this->log->guild->members->get($entry['options']['id']);
                        if($this->extra === null) {
                            $this->extra = array('id' => $entry['options']['id']);
                        }
                    break;
                    case 'role':
                        $this->extra = $this->log->guild->roles->get($entry['options']['id']);
                        if($this->extra === null) {
                            $this->extra = array('id' => $entry['options']['id']);
                        }
                    break;
                }
            }
        }
        
        $targetType = self::getTargetType($entry['action_type']);
        
        if($targetType === 'UNKNOWN') {
            $this->target = \array_reduce($this->changes, function ($carry,  $el) {
                $carry[$el['key']] = $el['new'] ?? $el['old'] ?? null;
                return $carry;
            }, array());
            $this->target['id'] = $entry['target_id'] ?? null;
        } elseif($targetType === 'USER' || $targetType === 'GUILD') {
            $method = \strtolower($targetType).'s';
            $this->target = $this->client->$method->get($entry['target_id']);
        } elseif($targetType === 'WEBHOOK') {
            $this->target = $this->log->webhooks->get($entry['target_id']);
        } elseif($targetType === 'INVITE') {
            if($this->log->guild->me->permissions->has(\CharlotteDunois\Yasmin\Models\Permissions::PERMISSIONS['MANAGE_GUILD'])) {
                $change = null;
                
                foreach($this->changes as $change) {
                    if($change['key'] === 'code') {
                        $change = $change['new'] ?? $change['old'] ?? null;
                        break;
                    }
                }
                
                if($change !== null) {
                    $this->target = $this->log->guild->fetchInvites()->then(function ($invites) use ($change) {
                        return $invites->first(function ($invite) use ($change) {
                            return ($invite->code === $change);
                        });
                    });
                }
            } else {
                $this->target = \array_reduce($this->changes, function ($el, $carry) {
                    $carry[$el['key']] = $el['new'] ?? $el['old'] ?? null;
                    return $carry;
                }, array());
            }
        } elseif($targetType === 'MESSAGE') {
            $this->target = $this->client->users->get($entry['target_id']);
        } else {
            $method = \strtolower($targetType).'s';
            $this->target = $this->log->guild->$method->get($entry['target_id']);
        }
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @internal
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'createdAt':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->createdTimestamp);
            break;
            case 'user':
                return $this->client->users->get($this->userID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Finds the action type from the entry action.
     * @param int  $actionType
     * @return string
     */
    static function getActionType(int $actionType) {
        if(\in_array($actionType, array(
            self::ACTION_TYPES['CHANNEL_CREATE'],
            self::ACTION_TYPES['CHANNEL_OVERWRITE_CREATE'],
            self::ACTION_TYPES['EMOJI_CREATE'],
            self::ACTION_TYPES['INVITE_CREATE'],
            self::ACTION_TYPES['MEMBER_BAN_REMOVE'],
            self::ACTION_TYPES['ROLE_CREATE'],
            self::ACTION_TYPES['WEBHOOK_CREATE']
        ))) {
            return 'CREATE';
        }
        
        if(\in_array($actionType, array(
            self::ACTION_TYPES['CHANNEL_DELETE'],
            self::ACTION_TYPES['CHANNEL_OVERWRITE_DELETE'],
            self::ACTION_TYPES['EMOJI_DELETE'],
            self::ACTION_TYPES['INVITE_DELETE'],
            self::ACTION_TYPES['MEMBER_BAN_ADD'],
            self::ACTION_TYPES['MEMBER_KICK'],
            self::ACTION_TYPES['MEMBER_PRUNE'],
            self::ACTION_TYPES['MESSAGE_DELETE'],
            self::ACTION_TYPES['ROLE_DELETE'],
            self::ACTION_TYPES['WEBHOOK_DELETE']
        ))) {
            return 'DELETE';
        }
        
        if(\in_array($actionType, array(
            self::ACTION_TYPES['CHANNEL_UPDATE'],
            self::ACTION_TYPES['CHANNEL_OVERWRITE_UPDATE'],
            self::ACTION_TYPES['EMOJI_UPDATE'],
            self::ACTION_TYPES['GUILD_UPDATE'],
            self::ACTION_TYPES['INVITE_UPDATE'],
            self::ACTION_TYPES['MEMBER_UPDATE'],
            self::ACTION_TYPES['MEMBER_ROLE_UPDATE'],
            self::ACTION_TYPES['ROLE_UPDATE'],
            self::ACTION_TYPES['WEBHOOK_UPDATE']
        ))) {
            return 'UPDATE';
        }

        return 'ALL';
    }
    
    /**
     * Finds the target type from the entry action.
     * @param int $target
     * @return string
     * @see \CharlotteDunois\Yasmin\Models\AuditLogEntry::TARGET_TYPES
     */
    static function getTargetType(int $target) {
        if($target < 10) {
            return 'GUILD';
        }
        if($target < 20) {
            return 'CHANNEL';
        }
        if($target < 30) {
            return 'USER';
        }
        if($target < 40) {
            return 'ROLE';
        }
        if($target < 50) {
            return 'INVITE';
        }
        if($target < 60) {
            return 'WEBHOOK';
        }
        if($target < 70) {
            return 'EMOJI';
        }
        if($target < 80) {
            return 'MESSAGE';
        }
        
        return 'UNKNOWN';
    }
    
    /**
     * @return mixed
     * @internal
     */
    function jsonSerialize() {
        return $this->id;
    }
}
