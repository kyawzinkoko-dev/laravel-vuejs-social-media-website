<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Comment extends Model
{
    use HasFactory;
    public array $childComments  = [];
    public int $numOfComments = 0;
    protected $fillable = ['post_id', 'comment', 'user_id', 'parent_id'];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function formatDate()
    {

        if ($this->updated_at) {
            return $this->updated_at->diffForHumans();
        }
        return $this->created_at->diffForHumans();
    }
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'object');
    }
    //relation for parent comment 
    public function comments(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    //to delte all the child comment if a paremt comment get deleted 
    protected static function boot()
    {
        parent::boot();
        static::deleting(function (Comment $comment) {
            $comment->comments()->each(function ($child) {
                $child->delete();
            });
        });
    }
}
