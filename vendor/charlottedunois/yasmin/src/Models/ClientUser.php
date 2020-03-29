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
 * Represents the Client User.
 */
class ClientUser extends User {
    /**
     * The client's presence.
     * @var array
     * @internal
     */
    protected $clientPresence;
    
    /**
     * @param \CharlotteDunois\Yasmin\Client $client
     * @param array                          $user
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, $user) {
        parent::__construct($client, $user);
        
        $presence = $this->client->getOption('ws.presence', array());
        $this->clientPresence = array(
            'afk' => (isset($presence['afk']) ? ((bool) $presence['afk']) : false),
            'since' => (isset($presence['since']) ? $presence['since'] : null),
            'status' => (!empty($presence['status']) ? $presence['status'] : 'online'),
            'game' => (!empty($presence['game']) ? $presence['game'] : null)
        );
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @throws \RuntimeException
     * @internal
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        return parent::__get($name);
    }
    
    /**
     * @return mixed
     * @internal
     */
    function __debugInfo() {
        $vars = parent::__debugInfo();
        unset($vars['clientPresence'], $vars['firstPresence'], $vars['firstPresencePromise'], $vars['firstPresenceCount'], $vars['firstPresenceTime']);
        return $vars;
    }
    
    /**
     * Set your avatar. Resolves with $this.
     * @param string|null  $avatar  An URL or the filepath or the data. Null resets your avatar.
     * @return \React\Promise\ExtendedPromiseInterface
     * @example ../../examples/docs-examples.php 15 4
     */
    function setAvatar(?string $avatar) {
        if($avatar === null) {
            return $this->client->apimanager()->endpoints->user->modifyCurrentUser(array('avatar' => null))->then(function () {
                return $this;
            });
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($avatar) {
            \CharlotteDunois\Yasmin\Utils\FileHelpers::resolveFileResolvable($avatar)->done(function ($data) use ($resolve, $reject) {
                $image = \CharlotteDunois\Yasmin\Utils\DataHelpers::makeBase64URI($data);
                
                $this->client->apimanager()->endpoints->user->modifyCurrentUser(array('avatar' => $image))->done(function () use ($resolve) {
                    $resolve($this);
                }, $reject);
            }, $reject);
        }));
    }
    
    /**
     * Set your status. Resolves with $this.
     * @param string  $status  Valid values are: `online`, `idle`, `dnd` and `invisible`.
     * @return \React\Promise\ExtendedPromiseInterface
     * @example ../../examples/docs-examples.php 25 2
     */
    function setStatus(string $status) {
        $presence = array(
            'status' => $status
        );
        
        return $this->setPresence($presence);
    }
    
    /**
     * Set your activity. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Activity|string|null  $name     The activity name.
     * @param int                                                  $type     Optional if first argument is an Activity. The type of your activity. Should be listening (2) or watching (3). For playing/streaming use ClientUser::setGame.
     * @param int|null                                             $shardID  Unless explicitely given, all presences will be fanned out to all shards.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setActivity($name, int $type = 0, ?int $shardID = null) {
        if($name === null) {
            return $this->setPresence(array(
                'game' => null
            ), $shardID);
        } elseif($name instanceof \CharlotteDunois\Yasmin\Models\Activity) {
            return $this->setPresence(array(
                'game' => $name->jsonSerialize()
            ), $shardID);
        }
        
        $presence = array(
            'game' => array(
                'name' => $name,
                'type' => $type,
                'url' => null
            )
        );
        
        return $this->setPresence($presence, $shardID);
    }
    
    /**
     * Set your playing game. Resolves with $this.
     * @param string|null  $name     The game name.
     * @param string       $url      If you're streaming, this is the url to the stream.
     * @param int|null     $shardID  Unless explicitely given, all presences will be fanned out to all shards.
     * @return \React\Promise\ExtendedPromiseInterface
     * @example ../../examples/docs-examples.php 21 2
     */
    function setGame(?string $name, string $url = '', ?int $shardID = null) {
        if($name === null) {
            return $this->setPresence(array(
                'game' => null
            ), $shardID);
        }
        
        $presence = array(
            'game' => array(
                'name' => $name,
                'type' => 0,
                'url' => null
            )
        );
        
        if(!empty($url)) {
            $presence['game']['type'] = 1;
            $presence['game']['url'] = $url;
        }
        
        return $this->setPresence($presence, $shardID);
    }
    
    /**
     * Set your presence. Ratelimit is 5/60s, the gateway drops all further presence updates. Resolves with $this.
     *
     * ```
     * array(
     *     'afk' => bool,
     *     'since' => int|null,
     *     'status' => string,
     *     'game' => array(
     *         'name' => string,
     *         'type' => int,
     *         'url' => string|null
     *     )|null
     * )
     * ```
     *
     *  Any field in the first dimension is optional and will be automatically filled with the last known value.
     *
     * @param array     $presence
     * @param int|null  $shardID   Unless explicitely given, all presences will be fanned out to all shards.
     * @return \React\Promise\ExtendedPromiseInterface
     * @example ../../examples/docs-examples.php 29 10
     */
    function setPresence(array $presence, ?int $shardID = null) {
        if(empty($presence)) {
            return \React\Promise\reject(new \InvalidArgumentException('Presence argument can not be empty'));
        }
        
        $packet = array(
            'op' => \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['STATUS_UPDATE'],
            'd' => array(
                'afk' => (\array_key_exists('afk', $presence) ? ((bool) $presence['afk']) : $this->clientPresence['afk']),
                'since' => (\array_key_exists('since', $presence) ? $presence['since'] : $this->clientPresence['since']),
                'status' => (\array_key_exists('status', $presence) ? $presence['status'] : $this->clientPresence['status']),
                'game' => (\array_key_exists('game', $presence) ? $presence['game'] : $this->clientPresence['game'])
            )
        );
        
        $this->clientPresence = $packet['d'];
        
        $presence = $this->getPresence();
        if($presence) {
            $presence->_patch($this->clientPresence);
        }
        
        if($shardID === null) {
            $prms = array();
            foreach($this->client->shards as $shard) {
                $prms[] = $shard->ws->send($packet);
            }
            
            return \React\Promise\all($prms)->then(function () {
                return $this;
            });
        }
        
        return $this->client->shards->get($shardID)->ws->send($packet)->then(function () {
            return $this;
        });
    }
    
    /**
     * Set your username. Resolves with $this.
     * @param string $username
     * @return \React\Promise\ExtendedPromiseInterface
     * @example ../../examples/docs-examples.php 41 2
     */
    function setUsername(string $username) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($username) {
            $this->client->apimanager()->endpoints->user->modifyCurrentUser(array('username' => $username))->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Creates a new Group DM with the owner of the access tokens. Resolves with an instance of GroupDMChannel. The structure of the array is as following:
     *
     * ```
     * array(
     *    accessToken => \CharlotteDunois\Yasmin\Models\User|string (user ID)
     * )
     * ```
     *
     * The nicks array is an associative array of userID => nick. The nick defaults to the username.
     *
     * @param array  $userWithAccessTokens
     * @param array  $nicks
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\GroupDMChannel
     */
    function createGroupDM(array $userWithAccessTokens, array $nicks = array()) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($nicks, $userWithAccessTokens) {
            $tokens = array();
            $users = array();
            
            foreach($userWithAccessTokens as $token => $user) {
                $user = $this->client->users->resolve($user);
                
                $tokens[] = $token;
                $users[$user->id] = (!empty($nicks[$user->id]) ? $nicks[$user->id] : $user->username);
            }
            
            $this->client->apimanager()->endpoints->user->createGroupDM($tokens, $users)->done(function ($data) use ($resolve) {
                $channel = $this->client->channels->factory($data);
                $resolve($channel);
            }, $reject);
        }));
    }
    
    /**
     * Making these methods throw if someone tries to use them. They also get hidden due to the Sami Renderer removing them.
    */
    
    /**
     * @return void
     * @throws \RuntimeException
     * @internal
     */
    function createDM() {
        throw new \RuntimeException('Can not use this method in ClientUser');
    }
    
    /**
     * @return void
     * @throws \RuntimeException
     * @internal
     */
    function deleteDM() {
        throw new \RuntimeException('Can not use this method in ClientUser');
    }
    
    /**
     * @return void
     * @throws \RuntimeException
     * @internal
     */
    function fetchUserConnections(string $accessToken) {
        throw new \RuntimeException('Can not use this method in ClientUser');
    }
}
