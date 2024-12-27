<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *       class Post
 * 
 * @property Group $group
 */
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['body', 'user_id', 'group_id', 'preview', 'preview_url'];
    protected $casts = ['preview' => 'json'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class)->latest();
    }
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'object');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }
    public function latest5Comments()
    {
        return $this->hasMany(Comment::class)->latest()->limit(5);
    }

    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }
    public static function postForTimeline($userId, $getLatest = true): Builder
    {

        $query = Post::query()
            ->withCount('reactions')
            ->with([
                'user',
                'group',
                'attachments',
                'comments' => function ($query) {
                    $query->withCount('reactions');
                },
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ]);
        if ($getLatest) {
            $query->latest();
        }
        return $query;
    }
}
