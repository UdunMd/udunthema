<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $type
 * @property int $priority
 * @property bool $is_active
 * @property array $target_display
 * @property \Carbon\Carbon|null $expires_at
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property \Pterodactyl\Models\User $author
 */
class Announcement extends Model
{
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const RESOURCE_NAME = 'announcement';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'announcements';

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
        'is_active' => 'boolean',
        'priority' => 'integer',
        'target_display' => 'array',
        'expires_at' => 'datetime',
    ];

    public static array $validationRules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'type' => 'required|string|in:info,warning,critical,promo',
        'priority' => 'required|integer|min:1|max:4',
        'is_active' => 'boolean',
        'target_display' => 'array',
        'expires_at' => 'nullable|date',
    ];

    /**
     * Return the user who created this announcement.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Return the read records for this announcement.
     */
    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }
}
