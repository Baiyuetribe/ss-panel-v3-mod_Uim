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
 * URL Helper methods.
 */
class URLHelpers {
    /**
     * The default HTTP user agent.
     * @var string
     * @internal
     */
    const DEFAULT_USER_AGENT = 'Yasmin (https://github.com/CharlotteDunois/Yasmin)';
    
    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected static $loop;
    
    /**
     * @var \Clue\React\Buzz\Browser
     */
    protected static $http;
    
    /**
     * Sets the Event Loop.
     * @param \React\EventLoop\LoopInterface  $loop
     * @return void
     * @internal
     */
    static function setLoop(\React\EventLoop\LoopInterface $loop) {
        static::$loop = $loop;
        
        if(static::$http === null) {
            static::internalSetClient();
        }
    }
    
    /**
     * Set the HTTP client used in Yasmin (and in this utility).
     * Be aware that this method can be changed at any time.
     *
     * If you want to set the HTTP client, then you need to set it
     * before the utilities get initialized by the Client!
     *
     * The HTTP client is after setting **immutable**.
     * @return void
     * @throws \LogicException
     */
    static function setHTTPClient(\Clue\React\Buzz\Browser $client) {
        if(static::$http !== null) {
            throw new \LogicException('Client has already been set');
        }
        
        static::$http = $client;
    }
    
    /**
     * Sets the client.
     * @return void
     */
    protected static function internalSetClient() {
        static::$http = new \Clue\React\Buzz\Browser(static::$loop);
    }
    
    /**
     * Returns the client. This method may be changed at any time.
     * @return \Clue\React\Buzz\Browser
     */
    static function getHTTPClient() {
        if(!static::$http) {
            static::internalSetClient();
        }
        
        return static::$http;
    }
    
    /**
     * Makes an asynchronous request. Resolves with an instance of ResponseInterface.
     *
     * The following request options are supported:
     * ```
     * array(
     *     'http_errors' => bool, (whether the HTTP client should obey the HTTP success code)
     *     'multipart' => array, (multipart form data, an array of `[ 'name' => string, 'contents' => string|resource, 'filename' => string ]`)
     *     'json' => mixed, (any JSON serializable type to send with the request as body payload)
     *     'query' => string, (the URL query string to set to)
     *     'headers' => string[], (HTTP headers to set)
     *     'timeout' => float, (after how many seconds the request times out)
     * )
     * ```
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param array|null                          $requestOptions
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \Psr\Http\Message\ResponseInterface
     */
    static function makeRequest(\Psr\Http\Message\RequestInterface $request, ?array $requestOptions = null) {
        $client = static::getHTTPClient();
        
        if(!empty($requestOptions)) {
            if(isset($requestOptions['http_errors'])) {
                $client = $client->withOptions(array(
                    'obeySuccessCode' => !empty($requestOptions['http_errors'])
                ));
            }
            
            if(isset($requestOptions['timeout'])) {
                $client = $client->withOptions(array(
                    'timeout' => ((float) $requestOptions['timeout'])
                ));
            }
            
            try {
                $request = static::applyRequestOptions($request, $requestOptions);
            } catch (\RuntimeException $e) {
                return \React\Promise\reject($e);
            }
        }
        
        return $client->send($request);
    }
    
    /**
     * Asynchronously resolves a given URL to the response body. Resolves with a string.
     * @param string      $url
     * @param array|null  $requestHeaders
     * @return \React\Promise\ExtendedPromiseInterface
     */
    static function resolveURLToData(string $url, ?array $requestHeaders = null) {
        if($requestHeaders === null) {
            $requestHeaders = array();
        }
        
        foreach($requestHeaders as $key => $val) {
            unset($requestHeaders[$key]);
            $nkey = \ucwords($key, '-');
            $requestHeaders[$nkey] = $val;
        }
        
        if(empty($requestHeaders['User-Agent'])) {
            $requestHeaders['User-Agent'] = static::DEFAULT_USER_AGENT;
        }
        
        $request = new \RingCentral\Psr7\Request('GET', $url, $requestHeaders);
        
        return static::makeRequest($request)->then(function ($response) {
            $body = (string) $response->getBody();
            return $body;
        });
    }
    
    /**
     * Applies request options to the request.
     *
     * The following request options are supported:
     * ```
     * array(
     *     'multipart' => array, (multipart form data, an array of `[ 'name' => string, 'contents' => string|resource, 'filename' => string ]`)
     *     'json' => mixed, (any JSON serializable type to send with the request as body payload)
     *     'query' => string, (the URL query string to set to)
     *     'headers' => string[], (HTTP headers to set)
     * )
     * ```
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param array                               $requestOptions
     * @return \Psr\Http\Message\RequestInterface
     * @throws \RuntimeException
     */
    static function applyRequestOptions(\Psr\Http\Message\RequestInterface $request, array $requestOptions) {
        if(isset($requestOptions['multipart'])) {
            $multipart = new \RingCentral\Psr7\MultipartStream($requestOptions['multipart']);
            
            $request = $request->withBody($multipart)
                            ->withHeader('Content-Type', 'multipart/form-data; boundary="'.$multipart->getBoundary().'"');
        }
        
        if(isset($requestOptions['json'])) {
            $resource = \fopen('php://temp', 'r+');
            if($resource === false) {
                throw new \RuntimeException('Unable to create stream for JSON data');
            }
            
            $json = \json_encode($requestOptions['json']);
            if($json === false) {
                throw new \RuntimeException('Unable to encode json. Error: '.\json_last_error_msg());
            }
            
            \fwrite($resource, $json);
            \fseek($resource, 0);
            
            $stream = new \RingCentral\Psr7\Stream($resource, array('size' => \strlen($json)));
            $request = $request->withBody($stream);
            
            $request = $request->withHeader('Content-Type', 'application/json')
                            ->withHeader('Content-Length', \strlen($json));
        }
        
        if(isset($requestOptions['query'])) {
            $uri = $request->getUri()->withQuery($requestOptions['query']);
            $request = $request->withUri($uri);
        }
        
        if(isset($requestOptions['headers'])) {
            foreach($requestOptions['headers'] as $key => $val) {
                $request = $request->withHeader($key, $val);
            }
        }
        
        return $request;
    }
}
