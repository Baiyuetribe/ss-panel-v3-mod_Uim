<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Handlers;

/**
 * WS Event handler
 * @internal
 */
class Dispatch implements \CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface {
    private $wsevents = array();
    protected $wshandler;
    
    function __construct(\CharlotteDunois\Yasmin\WebSocket\WSHandler $wshandler) {
        $this->wshandler = $wshandler;
        
        $allEvents = array(
            'RESUMED' => \CharlotteDunois\Yasmin\WebSocket\Events\Resumed::class,
            'READY' => \CharlotteDunois\Yasmin\WebSocket\Events\Ready::class,
            'CHANNEL_CREATE' => \CharlotteDunois\Yasmin\WebSocket\Events\ChannelCreate::class,
            'CHANNEL_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\ChannelUpdate::class,
            'CHANNEL_DELETE' => \CharlotteDunois\Yasmin\WebSocket\Events\ChannelDelete::class,
            'CHANNEL_PINS_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\ChannelPinsUpdate::class,
            'GUILD_CREATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildCreate::class,
            'GUILD_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildUpdate::class,
            'GUILD_DELETE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildDelete::class,
            'GUILD_BAN_ADD' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildBanAdd::class,
            'GUILD_BAN_REMOVE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildBanRemove::class,
            'GUILD_EMOJIS_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildEmojisUpdate::class,
            'GUILD_INTEGRATIONS_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildIntegrationsUpdate::class,
            'GUILD_MEMBER_ADD' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildMemberAdd::class,
            'GUILD_MEMBER_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildMemberUpdate::class,
            'GUILD_MEMBER_REMOVE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildMemberRemove::class,
            'GUILD_MEMBERS_CHUNK' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildMembersChunk::class,
            'GUILD_ROLE_CREATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildRoleCreate::class,
            'GUILD_ROLE_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildRoleUpdate::class,
            'GUILD_ROLE_DELETE' => \CharlotteDunois\Yasmin\WebSocket\Events\GuildRoleDelete::class,
            'MESSAGE_CREATE' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageCreate::class,
            'MESSAGE_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageUpdate::class,
            'MESSAGE_DELETE' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageDelete::class,
            'MESSAGE_DELETE_BULK' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageDeleteBulk::class,
            'MESSAGE_REACTION_ADD' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageReactionAdd::class,
            'MESSAGE_REACTION_REMOVE' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageReactionRemove::class,
            'MESSAGE_REACTION_REMOVE_ALL' => \CharlotteDunois\Yasmin\WebSocket\Events\MessageReactionRemoveAll::class,
            'PRESENCE_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\PresenceUpdate::class,
            'TYPING_START' => \CharlotteDunois\Yasmin\WebSocket\Events\TypingStart::class,
            'USER_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\UserUpdate::class,
            'VOICE_STATE_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\VoiceStateUpdate::class,
            'VOICE_SERVER_UPDATE' => \CharlotteDunois\Yasmin\WebSocket\Events\VoiceServerUpdate::class
        );
        
        $events = \array_diff_key($allEvents, \array_flip((array) $this->wshandler->wsmanager->client->getOption('ws.disabledEvents', array())));
        foreach($events as $name => $class) {
            $this->register($name, $class);
        }
    }
    
    /**
     * Returns a WS event.
     * @return \CharlotteDunois\Yasmin\Interfaces\WSEventInterface
     */
    function getEvent(string $name) {
        if(isset($this->wsevents[$name])) {
            return $this->wsevents[$name];
        }
        
        throw new \Exception('Unable to find WS event');
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $packet): void {
        if(isset($this->wsevents[$packet['t']])) {
            $this->wshandler->wsmanager->emit('debug', 'Shard '.$ws->shardID.' handling WS event '.$packet['t']);
            $this->wsevents[$packet['t']]->handle($ws, $packet['d']);
        } else {
            $this->wshandler->wsmanager->emit('debug', 'Shard '.$ws->shardID.' received WS event '.$packet['t']);
        }
    }
    
    /**
     * Registers an event.
     * @return void
     * @throws \RuntimeException
     */
    function register(string $name, string $class) {
        if(!\in_array('CharlotteDunois\Yasmin\Interfaces\WSEventInterface', \class_implements($class))) {
            throw new \RuntimeException('Specified event class does not implement interface');
        }
        
        $this->wsevents[$name] = new $class($this->wshandler->wsmanager->client, $this->wshandler->wsmanager);
    }
}
