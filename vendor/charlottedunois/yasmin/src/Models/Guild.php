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
 * Represents a guild. It's recommended to see if a guild is available before performing operations or reading data from it.
 *
 * @property string                                                          $id                           The guild ID.
 * @property int                                                             $shardID                      On which shard this guild is.
 * @property bool                                                            $available                    Whether the guild is available.
 * @property string                                                          $name                         The guild name.
 * @property int                                                             $createdTimestamp             The timestamp when this guild was created.
 * @property string|null                                                     $icon                         The guild icon hash, or null.
 * @property string|null                                                     $splash                       The guild splash hash, or null.
 * @property string                                                          $ownerID                      The ID of the owner.
 * @property bool                                                            $large                        Whether the guild is considered large.
 * @property bool                                                            $lazy                         Whether this guild is run in lazy mode (on the Discord node).
 * @property int                                                             $memberCount                  How many members the guild has.
 * @property \CharlotteDunois\Yasmin\Interfaces\ChannelStorageInterface      $channels                     Holds a guild's channels, mapped by their ID.
 * @property \CharlotteDunois\Yasmin\Interfaces\EmojiStorageInterface        $emojis                       Holds a guild's emojis, mapped by their ID.
 * @property \CharlotteDunois\Yasmin\Interfaces\GuildMemberStorageInterface  $members                      Holds a guild's cached members, mapped by their ID.
 * @property \CharlotteDunois\Yasmin\Interfaces\RoleStorageInterface         $roles                        Holds a guild's roles, mapped by their ID.
 * @property \CharlotteDunois\Yasmin\Interfaces\PresenceStorageInterface     $presences                    Holds a guild's presences of members, mapped by user ID.
 * @property string                                                          $defaultMessageNotifications  The type of message that should notify you. ({@see Guild::DEFAULT_MESSAGE_NOTIFICATIONS})
 * @property string                                                          $explicitContentFilter        The explicit content filter level of the guild. ({@see Guild::EXPLICIT_CONTENT_FILTER})
 * @property string                                                          $region                       The region the guild is located in.
 * @property string                                                          $verificationLevel            The verification level of the guild. ({@see Guild::VERIFICATION_LEVEL})
 * @property string|null                                                     $systemChannelID              The ID of the system channel, or null.
 * @property string|null                                                     $afkChannelID                 The ID of the afk channel, or null.
 * @property int|null                                                        $afkTimeout                   The time in seconds before an user is counted as "away from keyboard".
 * @property string[]                                                        $features                     An array of guild features.
 * @property string                                                          $mfaLevel                     The required MFA level for the guild. ({@see Guild::MFA_LEVEL})
 * @property string|null                                                     $applicationID                Application ID of the guild creator, if it is bot-created.
 * @property bool                                                            $embedEnabled                 Whether the guild is embeddable or not (e.g. widget).
 * @property string|null                                                     $embedChannelID               The ID of the embed channel, or null.
 * @property bool                                                            $widgetEnabled                Whether the guild widget is enabled or not.
 * @property string|null                                                     $widgetChannelID              The ID of the widget channel, or null.
 * @property int|null                                                        $maxPresences                 The maximum amount of presences the guild can have, or null.
 * @property int|null                                                        $maxMembers                   The maximum amount of members the guild can have, or null.
 * @property string|null                                                     $vanityInviteCode             The vanity invite code, or null.
 * @property string|null                                                     $description                  Guild description used for Server Discovery, or null.
 * @property string|null                                                     $banner                       Guild banner hash used for Server Discovery, or null.
 *
 * @property \CharlotteDunois\Yasmin\Models\VoiceChannel|null                $afkChannel                   The guild's afk channel, or null.
 * @property \DateTime                                                       $createdAt                    The DateTime instance of createdTimestamp.
 * @property \CharlotteDunois\Yasmin\Models\Role                             $defaultRole                  The guild's default role.
 * @property \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface|null   $embedChannel                 The guild's embed channel, or null.
 * @property \CharlotteDunois\Yasmin\Models\GuildMember                      $me                           The guild member of the client user.
 * @property \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface|null   $systemChannel                The guild's system channel, or null.
 * @property bool                                                            $vanityURL                    Whether the guild has a vanity invite url.
 * @property bool                                                            $verified                     Whether the guild is verified.
 * @property \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface|null   $widgetChannel                The guild's widget channel, or null.
 */
class Guild extends ClientBase {
    /**
     * Guild default message notifications.
     * @var array
     * @source
     */
    const DEFAULT_MESSAGE_NOTIFICATIONS = array(
        0 => 'EVERYTHING',
        1 => 'ONLY_MENTIONS'
    );
    
    /**
     * Guild explicit content filter.
     * @var array
     * @source
     */
    const EXPLICIT_CONTENT_FILTER = array(
        0 => 'DISABLED',
        1 => 'MEMBERS_WITHOUT_ROLES',
        2 => 'ALL_MEMBERS'
    );
    
    /**
     * Guild MFA level.
     * @var array
     * @source
     */
    const MFA_LEVEL = array(
        0 => 'NONE',
        1 => 'ELEVATED'
    );
    
    /**
     * Guild verification level.
     * @var array
     * @source
     */
    const VERIFICATION_LEVEL = array(
        0 => 'NONE',
        1 => 'LOW',
        2 => 'MEDIUM',
        3 => 'HIGH',
        4 => 'VERY_HIGH'
    );
    
    /**
     * Holds a guild's channels, mapped by their ID.
     * @var \CharlotteDunois\Yasmin\Interfaces\StorageInterface
     */
    protected $channels;
    
    /**
     * Holds a guild's emojis, mapped by their ID.
     * @var \CharlotteDunois\Yasmin\Interfaces\StorageInterface
     */
    protected $emojis;
    
    /**
     * Holds a guild's cached members, mapped by their ID.
     * @var \CharlotteDunois\Yasmin\Interfaces\StorageInterface
     */
    protected $members;
    
    /**
     * Holds a guild's presences of members, mapped by user ID.
     * @var \CharlotteDunois\Yasmin\Interfaces\StorageInterface
     */
    protected $presences;
    
    /**
     * Holds a guild's roles, mapped by their ID.
     * @var \CharlotteDunois\Yasmin\Interfaces\StorageInterface
     */
    protected $roles;
    
    /**
     * The guild ID.
     * @var string
     */
    protected $id;
    
    /**
     * On which shard this guild is.
     * @var int
     */
    protected $shardID;
    
    /**
     * Whether the guild is available.
     * @var bool
     */
    protected $available;
    
    /**
     * The guild name.
     * @var string
     */
    protected $name;
    
    /**
     * The guild icon hash, or null.
     * @var string|null
     */
    protected $icon;
    
    /**
     * The guild splash hash, or null.
     * @var string|null
     */
    protected $splash;
    
    /**
     * The ID of the owner.
     * @var string
     */
    protected $ownerID;
    
    /**
     * Whether the guild is considered large.
     * @var bool
     */
    protected $large;
    
    /**
     * Whether this guild is run in lazy mode (on the Discord node).
     * @var bool
     */
    protected $lazy;
    
    /**
     * How many members the guild has.
     * @var int
     */
    protected $memberCount = 0;
    
    /**
     * The type of message that should notify you.
     * @var string
     */
    protected $defaultMessageNotifications;
    
    /**
     * The explicit content filter level of the guild.
     * @var string
     */
    protected $explicitContentFilter;
    
    /**
     * The region the guild is located in.
     * @var string
     */
    protected $region;
    
    /**
     * The verification level of the guild.
     * @var string
     */
    protected $verificationLevel;
    
    /**
     * The ID of the system channel, or null.
     * @var string|null
     */
    protected $systemChannelID;
    
    /**
     * The ID of the afk channel, or null.
     * @var string|null
     */
    protected $afkChannelID;
    
    /**
     *
     * @var int|null
     */
    protected $afkTimeout;
    
    /**
     * Enabled features for this guild.
     * @var string[]
     */
    protected $features;
    
    /**
     * The required MFA level for the guild.
     * @var string
     */
    protected $mfaLevel;
    
    /**
     * The ID of the application which created this guild, or null.
     * @var string|null
     */
    protected $applicationID;
    
    /**
     * Whether the guild is embeddable or not (e.g. widget).
     * @var bool
     */
    protected $embedEnabled;
    
    /**
     * The ID of the embed channel, or null.
     * @var string|null
     */
    protected $embedChannelID;
    
    /**
     * Whether the widget is enabled.
     * @var bool
     */
    protected $widgetEnabled;
    
    /**
     * The ID of the widget channel, or null.
     * @var string|null
     */
    protected $widgetChannelID;
    
    /**
     * The maximum amount of presences the guild can have, or null.
     * @var int|null
     */
    protected $maxPresences;
    
    /**
     * The maximum amount of members the guild can have, or null.
     * @var int|null
     */
    protected $maxMembers;
    
    /**
     * The vanity invite code, or null.
     * @var string|null
     */
    protected $vanityInviteCode;
    
    /**
     * Guild description used for Server Discovery, or null.
     * @var string|null
     */
    protected $description;
    
    /**
     * Guild banner hash used for Server Discovery, or null.
     * @var string|null
     */
    protected $banner;
    
    /**
     * The timestamp when this guild was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $guild, ?int $shardID = null) {
        parent::__construct($client);
        
        $channels = $this->client->getOption('internal.storages.channels');
        $emojis = $this->client->getOption('internal.storages.emojis');
        $members = $this->client->getOption('internal.storages.members');
        $presences = $this->client->getOption('internal.storages.presences');
        $roles = $this->client->getOption('internal.storages.roles');
        
        $this->channels = new $channels($client);
        $this->emojis = new $emojis($client, $this);
        $this->members = new $members($client, $this);
        $this->presences = new $presences($client);
        $this->roles = new $roles($client, $this);
        
        $this->id = (string) $guild['id'];
        $snowflake = \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id);
        
        $this->shardID = ($shardID !== null ? $shardID : $snowflake->getShardID($this->client->getOption('shardCount')));
        $this->createdTimestamp = (int) $snowflake->timestamp;
        
        $this->available = (empty($guild['unavailable']));
        
        if($this->available) {
            $this->_patch($guild);
        }
        
        $this->client->guilds->set($this->id, $this);
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
        
        switch($name) {
            case 'afkChannel':
                return $this->channels->get($this->afkChannelID);
            break;
            case 'createdAt':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->createdTimestamp);
            break;
            case 'defaultRole':
                return $this->roles->get($this->id);
            break;
            case 'embedChannel':
                return $this->channels->get($this->embedChannelID);
            break;
            case 'me':
                return $this->members->get($this->client->user->id);
            break;
            case 'systemChannel':
                return $this->channels->get($this->systemChannelID);
            break;
            case 'vanityURL':
                return \in_array('VANITY_URL', $this->features);
            break;
            case 'verified':
                return \in_array('VERIFIED', $this->features);
            break;
            case 'widgetChannel':
                return $this->channels->get($this->widgetChannelID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Adds the given user to the guild using the OAuth Access Token. Requires the CREATE_INSTANT_INVITE permission. Resolves with $this.
     *
     * Options are as following (all fields are optional):
     *
     * ```
     * array(
     *   'nick' => string, (the nickname for the user, requires MANAGE_NICKNAMES permissions)
     *   'roles' => array|\CharlotteDunois\Collect\Collection, (array or Collection of Role instances or role IDs, requires MANAGE_ROLES permission)
     *   'mute' => bool, (whether the user is muted, requires MUTE_MEMBERS permission)
     *   'deaf' => bool, (whether the user is deafened, requires DEAFEN_MEMBERS permission)
     * )
     * ```
     *
     * @param \CharlotteDunois\Yasmin\Models\User|string  $user         A guild member or User instance, or the user ID.
     * @param string                                      $accessToken  The OAuth Access Token for the given user.
     * @param array                                       $options      Any options.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function addMember($user, string $accessToken, array $options = array()) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user, $accessToken, $options) {
            if($user instanceof \CharlotteDunois\Yasmin\Models\User) {
                $user = $user->id;
            }
            
            $opts = array(
                'access_token' => $accessToken
            );
            
            if(!empty($options['nick'])) {
                $opts['nick'] = $options['nick'];
            }
            
            if(!empty($options['roles'])) {
                if($options['roles'] instanceof \CharlotteDunois\Collect\Collection) {
                    $options['roles'] = $options['roles']->all();
                }
                
                $opts['roles'] = \array_values(\array_map(function ($role) {
                    if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
                        return $role->id;
                    }
                    
                    return $role;
                }, $options['roles']));
            }
            
            if(isset($options['mute'])) {
                $opts['mute'] = (bool) $options['mute'];
            }
            
            if(isset($options['deaf'])) {
                $opts['deaf'] = (bool) $options['deaf'];
            }
            
            $this->client->apimanager()->endpoints->guild->addGuildMember($this->id, $user, $opts)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Bans the given user. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\GuildMember|\CharlotteDunois\Yasmin\Models\User|string  $user     A guild member or User instance, or the user ID.
     * @param int                                                                                    $days     Number of days of messages to delete (0-7).
     * @param string                                                                                 $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function ban($user, int $days = 0, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user, $days, $reason) {
            if($user instanceof \CharlotteDunois\Yasmin\Models\User || $user instanceof \CharlotteDunois\Yasmin\Models\GuildMember) {
                $user = $user->id;
            }
            
            $this->client->apimanager()->endpoints->guild->createGuildBan($this->id, $user, $days, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Creates a new channel in the guild. Resolves with an instance of GuildChannelInterface.
     *
     * Options are as following (all fields except name are optional):
     *
     * ```
     * array(
     *   'name' => string,
     *   'type' => 'category'|'text'|'voice', (defaults to 'text')
     *   'topic' => string, (only for text channels)
     *   'position' => int,
     *   'bitrate' => int, (only for voice channels)
     *   'userLimit' => int, (only for voice channels, 0 = unlimited)
     *   'slowmode' => int, (only for text channels)
     *   'permissionOverwrites' => \CharlotteDunois\Collect\Collection|array, (an array or Collection of PermissionOverwrite instances or permission overwrite arrays*)
     *   'parent' => \CharlotteDunois\Yasmin\Models\CategoryChannel|string, (string = channel ID)
     *   'nsfw' => bool (only for text channels)
     * )
     *
     *   *  array(
     *   *      'id' => string, (an user/member or role ID)
     *   *      'type' => 'member'|'role',
     *   *      'allow' => \CharlotteDunois\Yasmin\Models\Permissions|int,
     *   *      'deny' => \CharlotteDunois\Yasmin\Models\Permissions|int
     *   *  )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface
     */
    function createChannel(array $options, string $reason = '') {
        if(empty($options['name'])) {
            throw new \InvalidArgumentException('Channel name can not be empty');
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($options, $reason) {
            $data = \CharlotteDunois\Yasmin\Utils\DataHelpers::applyOptions($options, array(
                'name' => array('type' => 'string'),
                'type' => array('type' => 'string', 'parse' => function ($val) {
                    return (\CharlotteDunois\Yasmin\Models\ChannelStorage::CHANNEL_TYPES[$val] ?? 0);
                }),
                'topic' => array('type' => 'string'),
                'position' => array('type' => 'int'),
                'bitrate' => array('type' => 'int'),
                'userLimit' => array('key' => 'user_limit', 'type' => 'int'),
                'slowmode' => array('key' => 'rate_limit_per_user', 'type' => 'int'),
                'permissionOverwrites' => array('key' => 'permission_overwrites', 'parse' => function ($val) {
                    if($val instanceof \CharlotteDunois\Collect\Collection) {
                        $val = $val->all();
                    }
                    
                    return \array_values($val);
                }),
                'parent' => array('key' => 'parent_id', 'parse' => function ($val) {
                    if($val instanceof \CharlotteDunois\Yasmin\Models\CategoryChannel) {
                        return $val->id;
                    }
                    
                    return $val;
                }),
                'nsfw' => array('type' => 'bool')
            ));
            
            $this->client->apimanager()->endpoints->guild->createGuildChannel($this->id, $data, $reason)->done(function ($data) use ($resolve) {
                $channel = $this->client->channels->factory($data, $this);
                $resolve($channel);
            }, $reject);
        }));
    }
    
    /**
     * Creates a new custom emoji in the guild. Resolves with an instance of Emoji.
     * @param string                                           $file   Filepath or URL, or file data.
     * @param string                                           $name
     * @param array|\CharlotteDunois\Collect\Collection        $roles  An array or Collection of Role instances or role IDs.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Emoji
     */
    function createEmoji(string $file, string $name, $roles = array(), string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($file, $name, $roles, $reason) {
            \CharlotteDunois\Yasmin\Utils\FileHelpers::resolveFileResolvable($file)->done(function ($file) use ($name, $roles, $reason, $resolve, $reject) {
                if($roles instanceof \CharlotteDunois\Collect\Collection) {
                    $roles = $roles->all();
                }
                
                $roles = \array_map(function ($role) {
                    if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
                        return $role->id;
                    }
                    
                    return $role;
                }, $roles);
                
                $options = array(
                    'name' => $name,
                    'image' => \CharlotteDunois\Yasmin\Utils\DataHelpers::makeBase64URI($file),
                    'roles' => $roles
                );
                
                $this->client->apimanager()->endpoints->emoji->createGuildEmoji($this->id, $options, $reason)->done(function ($data) use ($resolve) {
                    $emoji = $this->emojis->factory($data);
                    $resolve($emoji);
                }, $reject);
            }, $reject);
        }));
    }
    
    /**
     * Creates a new role in the guild. Resolves with an instance of Role.
     *
     * Options are as following (all are optional):
     *
     * ```
     * array(
     *   'name' => string,
     *   'permissions' => int|\CharlotteDunois\Yasmin\Models\Permissions,
     *   'color' => int|string,
     *   'hoist' => bool,
     *   'mentionable' => bool
     * )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Models\Role
     */
    function createRole(array $options, string $reason = '') {
        if(!empty($options['color'])) {
            $options['color'] = \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor($options['color']);
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($options, $reason) {
            $this->client->apimanager()->endpoints->guild->createGuildRole($this->id, $options, $reason)->done(function ($data) use ($resolve) {
                $role = $this->roles->factory($data);
                $resolve($role);
            }, $reject);
        }));
    }
    
    /**
     * Deletes the guild.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function delete() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->guild->deleteGuild($this->id)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Edits the guild. Resolves with $this.
     *
     * Options are as following (at least one is required):
     *
     * ```
     * array(
     *   'name' => string,
     *   'region' => string,
     *   'verificationLevel' => int,
     *   'explicitContentFilter' => int,
     *   'defaultMessageNotifications' => int,
     *   'afkChannel' => string|\CharlotteDunois\Yasmin\Models\VoiceChannel|null,
     *   'afkTimeout' => int|null,
     *   'systemChannel' => string|\CharlotteDunois\Yasmin\Models\TextChannel|null,
     *   'owner' => string|\CharlotteDunois\Yasmin\Models\GuildMember,
     *   'icon' => string, (file path or URL, or data)
     *   'splash' => string, (file path or URL, or data)
     *   'region' => string|\CharlotteDunois\Yasmin\Models\VoiceRegion
     * )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function edit(array $options, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($options, $reason) {
            $data = \CharlotteDunois\Yasmin\Utils\DataHelpers::applyOptions($options, array(
                'name' => array('type' => 'string'),
                'region' => array('type' => 'string', 'parse' => function ($val) {
                    return ($val instanceof \CharlotteDunois\Yasmin\Models\VoiceRegion ? $val->id : $val);
                }),
                'verificationLevel' => array('key' => 'verification_level', 'type' => 'int'),
                'explicitContentFilter' => array('key' => 'explicit_content_filter', 'type' => 'int'),
                'defaultMessageNotifications' => array('key' => 'default_message_notifications', 'type' => 'int'),
                'afkChannel' => array('key' => 'afk_channel_id', 'parse' => function ($val) {
                    return ($val instanceof \CharlotteDunois\Yasmin\Models\VoiceChannel ? $val->id : $val);
                }),
                'afkTimeout' => array('key' => 'afk_timeout', 'type' => 'int'),
                'systemChannel' => array('key' => 'system_channel_id', 'parse' => function ($val) {
                    return ($val instanceof \CharlotteDunois\Yasmin\Models\TextChannel ? $val->id : $val);
                }),
                'owner' => array('key' => 'owner_id', 'parse' => function ($val) {
                    return ($val instanceof \CharlotteDunois\Yasmin\Models\GuildMember ? $val->id : $val);
                })
            ));
            
            $handleImg = function ($img) {
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeBase64URI($img);
            };
            
            $files = array(
                (isset($options['icon']) ? \CharlotteDunois\Yasmin\Utils\FileHelpers::resolveFileResolvable($options['icon'])->then($handleImg) : \React\Promise\resolve(null)),
                (isset($options['splash']) ? \CharlotteDunois\Yasmin\Utils\FileHelpers::resolveFileResolvable($options['splash'])->then($handleImg) : \React\Promise\resolve(null))
            );
            
            \React\Promise\all($files)->done(function ($files) use (&$data, $reason, $resolve, $reject) {
                if(\is_string($files[0])) {
                    $data['icon'] = $files[0];
                }
                
                if(\is_string($files[1])) {
                    $data['splash'] = $files[1];
                }
                
                $this->client->apimanager()->endpoints->guild->modifyGuild($this->id, $data, $reason)->done(function () use ($resolve) {
                    $resolve($this);
                }, $reject);
            }, $reject);
        }));
    }
    
    /**
     * Fetch audit log for the guild. Resolves with an instance of AuditLog.
     *
     * Options are as following (all are optional):
     *
     * ```
     * array(
     *   'before' => string|\CharlotteDunois\Yasmin\Models\AuditLogEntry, (string = Audit Log Entry ID)
     *   'after' => string|\CharlotteDunois\Yasmin\Models\AuditLogEntry, (string = Audit Log Entry ID)
     *   'limit' => int,
     *   'user' => string|\CharlotteDunois\Yasmin\Models\User,
     *   'type' => string|int
     * )
     * ```
     *
     * @param array  $options
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\AuditLog
     */
    function fetchAuditLog(array $options = array()) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($options) {
            if(!empty($options['user'])) {
                $options['user'] = ($options['user'] instanceof \CharlotteDunois\Yasmin\Models\User ? $options['user']->id : $options['user']);
            }
            
            $this->client->apimanager()->endpoints->guild->getGuildAuditLog($this->id, $options)->done(function ($data) use ($resolve) {
                $audit = new \CharlotteDunois\Yasmin\Models\AuditLog($this->client, $this, $data);
                $resolve($audit);
            }, $reject);
        }));
    }
    
    /**
     * Fetches a specific ban for a user. Resolves with an instance of GuildBan.
     * @param \CharlotteDunois\Yasmin\Models\User|string  $user     An User instance or the user ID.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\GuildBan
     */
    function fetchBan($user) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user) {
            if($user instanceof \CharlotteDunois\Yasmin\Models\User) {
                $user = $user->id;
            }
            
            $this->client->apimanager()->endpoints->guild->getGuildBan($this->id, $user)->done(function ($data) use ($resolve) {
                $user = $this->client->users->patch($data['user']);
                $ban = new \CharlotteDunois\Yasmin\Models\GuildBan($this->client, $this, $user, ($data['reason'] ?? null));
                
                $resolve($ban);
            }, $reject);
        }));
    }
    
    /**
     * Fetch all bans of the guild. Resolves with a Collection of GuildBan instances, mapped by the user ID.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\GuildBan
     */
    function fetchBans() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->guild->getGuildBans($this->id)->done(function ($data) use ($resolve) {
                $collect = new \CharlotteDunois\Collect\Collection();
                
                foreach($data as $ban) {
                    $user = $this->client->users->patch($ban['user']);
                    $gban = new \CharlotteDunois\Yasmin\Models\GuildBan($this->client, $this, $user, ($ban['reason'] ?? null));
                    
                    $collect->set($user->id, $gban);
                }
                
                $resolve($collect);
            }, $reject);
        }));
    }
    
    /**
     * Fetches all invites of the guild. Resolves with a Collection of Invite instances, mapped by their code.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function fetchInvites() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->guild->getGuildInvites($this->id)->done(function ($data) use ($resolve) {
                $collect = new \CharlotteDunois\Collect\Collection();
                
                foreach($data as $inv) {
                    $invite = new \CharlotteDunois\Yasmin\Models\Invite($this->client, $inv);
                    $collect->set($invite->code, $invite);
                }
                
                $resolve($collect);
            }, $reject);
        }));
    }
    
    /**
     * Fetches a specific guild member. Resolves with an instance of GuildMember.
     * @param string  $userid  The ID of the guild member.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\GuildMember
     */
    function fetchMember(string $userid) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($userid) {
            if($this->members->has($userid) && ($this->members->get($userid) instanceof \CharlotteDunois\Yasmin\Models\GuildMember)) {
                return $resolve($this->members->get($userid));
            }
            
            $this->client->apimanager()->endpoints->guild->getGuildMember($this->id, $userid)->done(function ($data) use ($resolve) {
                $resolve($this->_addMember($data, true));
            }, $reject);
        }));
    }
    
    /**
     * Fetches all guild members. If `$query` is used, `$limit` must be set to a non-zero integer. Resolves with $this.
     * @param string  $query  Limit fetch to members with similar usernames.
     * @param int     $limit  Maximum number of members to request.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function fetchMembers(string $query = '', int $limit = 0) {
        if(!empty($query) && $limit <= 0) {
            throw new \InvalidArgumentException('Invalid arguments given - if query is given, limit must be supplied as well');
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($query, $limit) {
            if($this->members->count() >= $this->memberCount) {
                $resolve($this);
                return;
            }
            
            $received = 0;
            $timers = array();
            
            $listener = function ($guild, $members) use (&$listener, $query, $limit, &$received, &$timers, $resolve) {
                if($guild->id !== $this->id) {
                    return;
                }
                
                $received += $members->count();
                
                if((!empty($query) && $members->count() < 1000) || ($limit > 0 && $received >= $limit) || $this->members->count() >= $this->memberCount) {
                    if(!empty($timers)) {
                        foreach($timers as $timer) {
                            $this->client->cancelTimer($timer);
                        }
                    }
                    
                    $this->client->removeListener('guildMembersChunk', $listener);
                    $resolve($this);
                }
            };
            
            if(!empty($query)) {
                $timers[] = $this->client->addTimer(110, function (&$listener, &$timers, $resolve) {
                    foreach($timers as $timer) {
                        $this->client->cancelTimer($timer);
                    }
                    
                    $this->client->removeListener('guildMembersChunk', $listener);
                    $resolve($this);
                });
            }
            
            $this->client->on('guildMembersChunk', $listener);
            
            $this->client->shards->get($this->shardID)->ws->send(array(
                'op' => \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['REQUEST_GUILD_MEMBERS'],
                'd' => array(
                    'guild_id' => $this->id,
                    'query' => $query,
                    'limit' => $limit
                )
            ));
            
            $timers[] = $this->client->addTimer(120, function () use (&$listener, &$timers, $reject, $resolve) {
                foreach($timers as $timer) {
                    $this->client->cancelTimer($timer);
                }
                
                if($this->members->count() < $this->memberCount) {
                    $this->client->removeListener('guildMembersChunk', $listener);
                    $reject(new \Exception('Members did not arrive in time'));
                    return;
                }
                
                $resolve($this);
            });
        }));
    }
    
    /**
     * Fetches the amount of members from the guild based on how long they have been inactive which would be pruned. Resolves with an integer.
     * @param int     $days
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function fetchPruneMembers(int $days) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($days) {
            $this->client->apimanager()->endpoints->guild->getGuildPruneCount($this->id, $days)->done(function ($data) use ($resolve) {
                $resolve($data['pruned']);
            }, $reject);
        }));
    }
    
    /**
     * Returns the vanity invite. The guild must be partnered, i.e. have 'VANITY_URL' in guild features. Resolves with an instance of Invite.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function fetchVanityInvite() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            if($this->vanityInviteCode !== null) {
                return $this->client->apimanager()->endpoints->invite->getInvite($this->vanityInviteCode)->done(function ($data) use ($resolve) {
                    $invite = new \CharlotteDunois\Yasmin\Models\Invite($this->client, $data);
                    $resolve($invite);
                }, $reject);
            }
            
            $this->client->apimanager()->endpoints->guild->getGuildVanityURL($this->id)->then(function ($data) {
                return $this->client->apimanager()->endpoints->invite->getInvite($data['code']);
            })->done(function ($data) use ($resolve) {
                $invite = new \CharlotteDunois\Yasmin\Models\Invite($this->client, $data);
                $resolve($invite);
            }, $reject);
        }));
    }
    
    /**
     * Fetches the guild voice regions. Resolves with a Collection of Voice Region instances, mapped by their ID.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\VoiceRegion
     */
    function fetchVoiceRegions() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->guild->getGuildVoiceRegions($this->id)->done(function ($data) use ($resolve) {
                $collect = new \CharlotteDunois\Collect\Collection();
                
                foreach($data as $region) {
                    $voice = new \CharlotteDunois\Yasmin\Models\VoiceRegion($this->client, $region);
                    $collect->set($voice->id, $voice);
                }
                
                $resolve($collect);
            }, $reject);
        }));
    }
    
    /**
     * Fetches the guild's webhooks. Resolves with a Collection of Webhook instances, mapped by their ID.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Webhook
     */
    function fetchWebhooks() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->webhook->getGuildsWebhooks($this->id)->done(function ($data) use ($resolve) {
                $collect = new \CharlotteDunois\Collect\Collection();
                
                foreach($data as $web) {
                    $hook = new \CharlotteDunois\Yasmin\Models\Webhook($this->client, $web);
                    $collect->set($hook->id, $hook);
                }
                
                $resolve($collect);
            }, $reject);
        }));
    }
    
    /**
     * Returns the guild's banner URL, or null.
     * @param int|null  $size    One of 128, 256, 512, 1024 or 2048.
     * @param string    $format  One of png, jpg or webp.
     * @return string|null
     * @throws \InvalidArgumentException Thrown if $size is not a power of 2
     */
    function getBannerURL(?int $size = null, string $format = 'png') {
        if(!\CharlotteDunois\Yasmin\Utils\ImageHelpers::isPowerOfTwo($size)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        if($this->banner !== null) {
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['guildbanners'], $this->id, $this->banner, $format).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
    
    /**
     * Returns the guild's icon URL, or null.
     * @param int|null  $size    One of 128, 256, 512, 1024 or 2048.
     * @param string    $format  One of png, jpg or webp.
     * @return string|null
     * @throws \InvalidArgumentException Thrown if $size is not a power of 2
     */
    function getIconURL(?int $size = null, string $format = '') {
        if(!\CharlotteDunois\Yasmin\Utils\ImageHelpers::isPowerOfTwo($size)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        if($this->icon === null) {
            return null;
        }
        
        if(empty($format)) {
            $format = \CharlotteDunois\Yasmin\Utils\ImageHelpers::getImageExtension($this->icon);
        }
        
        return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['icons'], $this->id, $this->icon, $format).(!empty($size) ? '?size='.$size : '');
    }
    
    /**
     * Returns the guild's name acronym.
     * @return string
     */
    function getNameAcronym() {
        \preg_match_all('/\w+/iu', $this->name, $matches);
        
        $name = '';
        foreach($matches[0] as $word) {
            $name .= $word[0];
        }
        
        return \mb_strtoupper($name);
    }
    
    /**
     * Returns the guild's splash URL, or null.
     * @param int|null  $size    One of 128, 256, 512, 1024 or 2048.
     * @param string    $format  One of png, jpg or webp.
     * @return string|null
     */
    function getSplashURL(?int $size = null, string $format = 'png') {
        if($size & ($size - 1)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        if($this->splash !== null) {
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['splashes'], $this->id, $this->splash, $format).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
    
    /**
     * Leaves the guild.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function leave() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->user->leaveUserGuild($this->id)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Prunes members from the guild based on how long they have been inactive. Resolves with an integer or null.
     * @param int     $days
     * @param bool    $withCount  Whether the amount of pruned members is returned, discouraged for large guilds.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function pruneMembers(int $days, bool $withCount = false, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($days, $withCount, $reason) {
            $this->client->apimanager()->endpoints->guild->beginGuildPrune($this->id, $days, $withCount, $reason)->done(function ($data) use ($resolve) {
                $resolve(($data['pruned'] ?? null));
            }, $reject);
        }));
    }
    
    /**
     * Edits the AFK channel of the guild. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\VoiceChannel|null  $channel
     * @param string                                                   $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setAFKChannel($channel, string $reason = '') {
        return $this->edit(array('afkChannel' => $channel), $reason);
    }
    
    /**
     * Edits the AFK timeout of the guild. Resolves with $this.
     * @param int|null $timeout
     * @param string   $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setAFKTimeout($timeout, string $reason = '') {
        return $this->edit(array('afkTimeout' => $timeout), $reason);
    }
    
    /**
     * Batch-updates the guild's channels positions. Channels is an array of `channel ID (string)|GuildChannelInterface => position (int)` pairs. Resolves with $this.
     * @param array   $channels
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setChannelPositions(array $channels, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($channels, $reason) {
            $options = array();
            
            foreach($channels as $chan => $position) {
                if($chan instanceof \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface) {
                    $chan = $chan->getId();
                }
                
                $options[] = array('id' => $chan, 'position' => (int) $position);
            }
            
            $this->client->apimanager()->endpoints->guild->modifyGuildChannelPositions($this->id, $options, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Batch-updates the guild's roles positions. Roles is an array of `role ID (string)|Role => position (int)` pairs. Resolves with $this.
     * @param array   $roles
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setRolePositions(array $roles, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($roles, $reason) {
            $options = array();
            
            foreach($roles as $role => $position) {
                if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
                    $role = $role->id;
                }
                
                $options[] = array('id' => $role, 'position' => (int) $position);
            }
            
            $this->client->apimanager()->endpoints->guild->modifyGuildRolePositions($this->id, $options, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Edits the level of the explicit content filter. Resolves with $this.
     * @param int     $filter
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setExplicitContentFilter(int $filter, string $reason = '') {
        return $this->edit(array('explicitContentFilter' => $filter), $reason);
    }
    
    /**
     * Updates the guild icon. Resolves with $this.
     * @param string  $icon    A filepath or URL, or data.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setIcon(string $icon, string $reason = '') {
        return $this->edit(array('icon' => $icon), $reason);
    }
    
    /**
     * Edits the name of the guild. Resolves with $this.
     * @param string  $name
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setName(string $name, string $reason = '') {
        return $this->edit(array('name' => $name), $reason);
    }
    
    /**
     * Sets a new owner for the guild. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\GuildMember  $owner
     * @param string                                             $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setOwner($owner, string $reason = '') {
        return $this->edit(array('owner' => $owner), $reason);
    }
    
    /**
     * Edits the region of the guild. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\VoiceRegion  $region
     * @param string                                             $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setRegion($region, string $reason = '') {
        return $this->edit(array('region' => $region), $reason);
    }
    
    /**
     * Updates the guild splash. Resolves with $this.
     * @param string  $splash  A filepath or URL, or data.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setSplash(string $splash, string $reason = '') {
        return $this->edit(array('splash' => $splash), $reason);
    }
    
    /**
     * Edits the system channel of the guild. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\TextChannel|null  $channel
     * @param string                                                  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setSystemChannel($channel, string $reason = '') {
        return $this->edit(array('systemChannel' => $channel), $reason);
    }
    
    /**
     * Edits the verification level of the guild. Resolves with $this.
     * @param int     $level
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setVerificationLevel(int $level, string $reason = '') {
        return $this->edit(array('verificationLevel' => $level), $reason);
    }
    
    /**
     * Unbans the given user. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\User|string  $user     An User instance or the user ID.
     * @param string                                      $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function unban($user, string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user, $reason) {
            if($user instanceof \CharlotteDunois\Yasmin\Models\User) {
                $user = $user->id;
            }
            
            $this->client->apimanager()->endpoints->guild->removeGuildBan($this->id, $user, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * @return \CharlotteDunois\Yasmin\Models\GuildMember
     * @internal
     */
    function _addMember(array $member, bool $initial = false) {
        $guildmember = $this->members->factory($member);
        
        if(!$initial) {
            $this->memberCount++;
        }
        
        return $guildmember;
    }
    
    /**
     * @return \CharlotteDunois\Yasmin\Models\GuildMember|null
     * @internal
     */
    function _removeMember(string $userid) {
        if($this->members->has($userid)) {
            $member = $this->members->get($userid);
            $this->members->delete($userid);
            
            if($member->voiceChannel) {
                $member->voiceChannel->members->delete($userid);
            }
            
            $this->memberCount--;
            return $member;
        }
        
        return null;
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $guild) {
        $this->available = (empty($guild['unavailable']));
        
        if(!$this->available) {
            return;
        }
        
        $this->name = (string) ($guild['name'] ?? $this->name);
        $this->icon = $guild['icon'] ?? $this->icon;
        $this->splash = $guild['splash'] ?? $this->splash;
        $this->ownerID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['owner_id'] ?? $this->ownerID), 'string');
        $this->large = (bool) ($guild['large'] ?? $this->large);
        $this->lazy = !empty($guild['lazy']);
        $this->memberCount = (int) ($guild['member_count']  ?? $this->memberCount);
        
        $this->defaultMessageNotifications = (isset($guild['default_message_notifications']) ? (self::DEFAULT_MESSAGE_NOTIFICATIONS[$guild['default_message_notifications']] ?? $this->defaultMessageNotifications) : $this->defaultMessageNotifications);
        $this->explicitContentFilter = (isset($guild['explicit_content_filter']) ? (self::EXPLICIT_CONTENT_FILTER[$guild['explicit_content_filter']] ?? $this->explicitContentFilter) : $this->explicitContentFilter);
        $this->region = $guild['region'] ?? $this->region;
        $this->verificationLevel = (isset($guild['verification_level']) ? (self::VERIFICATION_LEVEL[$guild['verification_level']] ?? $this->verificationLevel) : $this->verificationLevel);
        $this->systemChannelID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['system_channel_id'] ?? $this->systemChannelID), 'string');
        
        $this->afkChannelID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['afk_channel_id'] ?? $this->afkChannelID), 'string');
        $this->afkTimeout = $guild['afk_timeout'] ?? $this->afkTimeout;
        $this->features = $guild['features'] ?? $this->features;
        $this->mfaLevel = (isset($guild['mfa_level']) ? (self::MFA_LEVEL[$guild['mfa_level']] ?? $this->mfaLevel) : $this->mfaLevel);
        $this->applicationID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['application_id'] ?? $this->applicationID), 'string');
        
        $this->embedEnabled = (bool) ($guild['embed_enabled'] ?? $this->embedEnabled);
        $this->embedChannelID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['embed_channel_id'] ?? $this->embedChannelID), 'string');
        $this->widgetEnabled = (bool) ($guild['widget_enabled'] ?? $this->widgetEnabled);
        $this->widgetChannelID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['widget_channel_id'] ?? $this->widgetChannelID), 'string');
        
        $this->maxPresences = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['max_presences'] ?? $this->maxPresences), 'int');
        $this->maxMembers = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['max_members'] ?? $this->maxMembers), 'int');
        $this->vanityInviteCode = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['vanity_url_code'] ?? $this->vanityInviteCode), 'string');
        $this->description = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['description'] ?? $this->description), 'string');
        $this->banner = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($guild['banner'] ?? $this->banner), 'string');
        
        if(isset($guild['roles'])) {
            $this->roles->clear();
            foreach($guild['roles'] as $role) {
                $this->roles->factory($role);
            }
        }
        
        if(isset($guild['emojis'])) {
            $this->emojis->clear();
            foreach($guild['emojis'] as $emoji) {
                $this->emojis->factory($emoji);
            }
        }
        
        if(isset($guild['channels'])) {
            $this->channels->clear();
            foreach($guild['channels'] as $channel) {
                $this->channels->factory($channel, $this);
            }
        }
        
        if(!empty($guild['members'])) {
            foreach($guild['members'] as $member) {
                $this->_addMember($member, true);
            }
        }
        
        if(!empty($guild['presences'])) {
            foreach($guild['presences'] as $presence) {
                $this->presences->factory($presence);
            }
        }
        
        if(!empty($guild['voice_states'])) {
            foreach($guild['voice_states'] as $state) {
                $member = $this->members->get($state['user_id']);
                if($member) {
                    $member->_setVoiceState($state);
                    
                    if($member->voiceChannel !== null) {
                        $member->voiceChannel->members->set($member->id, $member);
                    }
                }
            }
        }
    }
}
