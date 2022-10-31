<?php

namespace App\Models;

use App\Models\Contracts\IFileMorphModel;
use App\Models\Traits\FileMorphTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\AffairsConversation
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property string|null $title
 * @property array $send_to
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\AffairsConversationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation newQuery()
 * @method static \Illuminate\Database\Query\Builder|AffairsConversation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereSendTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|AffairsConversation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AffairsConversation withoutTrashed()
 * @mixin \Eloquent
 * @property int $affairs_id
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereAffairsId($value)
 * @property mixed|null $attachments
 * @property string|null $send_at
 * @property-read \App\Models\Affairs $affairs
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereSendAt($value)
 * @property string|null $sent_at
 * @method static \Illuminate\Database\Eloquent\Builder|AffairsConversation whereSentAt($value)
 */
class AffairsConversation extends Model implements IFileMorphModel
{
    use HasFactory;
    use SoftDeletes;
    use FileMorphTrait;

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'type',
        'user_id',
        'affairs_id',
        'title',
        'send_to',
        'text',
        'attachments',
        'sent_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected function sendTo(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (string $value): array => json_decode($value, true),
            set: fn (array $value): string => json_encode($value, JSON_FORCE_OBJECT),
        );
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function affairs(): BelongsTo
    {
        return $this->belongsTo(Affairs::class, 'affairs_id', 'id');        
    }

    /**
     * @return boolean
     */
    public function markAsSent(): bool
    {
        return $this->forceFill([
            'sent_at' => $this->freshTimestamp(),
        ])->save();
    }
}
