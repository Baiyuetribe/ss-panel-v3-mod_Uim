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
 * Holds message mentions.
 *
 * @property \CharlotteDunois\Yasmin\Models\Message  $message   The message these mentions belongs to.
 * @property \CharlotteDunois\Collect\Collection     $channels  The collection which holds all channel mentions, mapped by their ID.
 * @property bool                                    $everyone  Whether the message mentions @everyone or @here.
 * @property \CharlotteDunois\Collect\Collection     $members   The collection which holds all members mentions (only in guild channels), mapped by their ID. Only cached members can be put into this Collection.
 * @property \CharlotteDunois\Collect\Collection     $roles     The collection which holds all roles mentions, mapped by their ID.
 * @property \CharlotteDunois\Collect\Collection     $users     The collection which holds all users mentions, mapped by their ID.
 */
class MessageMentions extends ClientBase {
    /**
     * RegEx pattern to match channel mentions.
     * @var string
     * @source
     */
     const PATTERN_CHANNELS = '/<#(\d+)>/';
     
    /**
     * RegEx pattern to match custom emoji mentions.
     * @var string
     * @source
     */
    const PATTERN_EMOJIS = '/<a?:(?:.*?):(\d+)>/';
    
    /**
     * RegEx pattern to match role mentions.
     * @var string
     * @source
     */
    const PATTERN_ROLES = '/<@&(\d+)>/';
    
    /**
     * RegEx pattern to match user mentions.
     * @var string
     * @source
     */
    const PATTERN_USERS = '/<@!?(\d+)>/';
    
    /**
     * The message these mentions belongs to.
     * @var \CharlotteDunois\Yasmin\Models\Message
     */
    protected $message;
    
    /**
     * The collection which holds all channel mentions, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $channels;
    
    /**
     * Whether the message mentions @everyone or @here.
     * @var bool
     */
    protected $everyone;
    
    /**
     * The collection which holds all members mentions (only in guild channels), mapped by their ID. Only cached members can be put into this Collection.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $members;
    
    /**
     * The collection which holds all roles mentions, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $roles;
    
    /**
     * The collection which holds all users mentions, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $users;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Message $message, array $msg) {
        parent::__construct($client);
        $this->message = $message;
        
        $this->channels = new \CharlotteDunois\Collect\Collection();
        $this->members = new \CharlotteDunois\Collect\Collection();
        $this->roles = new \CharlotteDunois\Collect\Collection();
        $this->users = new \CharlotteDunois\Collect\Collection();
        
        $this->everyone = !empty($msg['mention_everyone']);
        
        \preg_match_all(self::PATTERN_CHANNELS, $message->content, $matches);
        if(!empty($matches[1])) {
            foreach($matches[1] as $match) {
                $channel = $this->client->channels->get($match);
                if($channel) {
                    $this->channels->set($channel->getId(), $channel);
                }
            }
        }
        
        if(!empty($msg['mentions'])) {
            foreach($msg['mentions'] as $mention) {
                $user = $this->client->users->patch($mention);
                if($user) {
                    $member = null;
                    
                    $this->users->set($user->id, $user);
                    if($message->guild) {
                        $member = $message->guild->members->get($user->id);
                        if($member) {
                            $this->members->set($member->id, $member);
                        }
                    }
                }
            }
        }
        
        if($message->channel instanceof \CharlotteDunois\Yasmin\Models\TextChannel && !empty($msg['mention_roles'])) {
            foreach($msg['mention_roles'] as $id) {
                $role = $message->channel->guild->roles->get($id);
                if($role) {
                    $this->roles->set($role->id, $role);
                }
            }
        }
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
}
