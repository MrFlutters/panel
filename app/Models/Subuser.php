<?php

namespace Pterodactyl\Models;

use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int $user_id
 * @property int $server_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property \Pterodactyl\Models\User $user
 * @property \Pterodactyl\Models\Server $server
 * @property \Pterodactyl\Models\Permission[]|\Illuminate\Support\Collection $permissions
 */
class Subuser extends Validable
{
    use Notifiable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'server_subuser';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subusers';

    /**
     * Fields that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'server_id' => 'integer',
    ];

    /**
     * @var array
     */
    public static $validationRules = [
        'user_id' => 'required|numeric|exists:users,id',
        'server_id' => 'required|numeric|exists:servers,id',
    ];

    /**
     * Return a hashid encoded string to represent the ID of the subuser.
     *
     * @return string
     */
    public function getHashidAttribute()
    {
        return app()->make('hashids')->encode($this->id);
    }

    /**
     * Gets the server associated with a subuser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Gets the user associated with a subuser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Gets the permissions associated with a subuser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Return the key that belongs to this subuser for the server.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function key()
    {
        return $this->hasOne(DaemonKey::class, 'server_id', 'server_id')->where('daemon_keys.user_id', '=', $this->user_id);
    }
}
