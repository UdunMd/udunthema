<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $announcement_id
 * @property \Carbon\Carbon $read_at
 *
 * @property \Pterodactyl\Models\User $user
 * @property \Pterodactyl\Models\Announcement $announcement
 */
class AnnouncementRead extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcement_reads';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fields that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Return the user who read this announcement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the announcement that was read.
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }
}
