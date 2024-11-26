<?php

namespace App\Models;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Group extends Model
{
    use HasFactory;
    use HasSlug;
    protected $fillable = ['name', 'user_id', 'about', 'cover_path', 'thumbnail_path',];

    function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
    function currentUserGroup(): HasOne
    {
        return $this->hasOne(GroupUser::class)->where('user_id', Auth::id());
    }
    function isAdmin($userId): bool
    {
        return GroupUser::where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('role', GroupUserRole::ADMIN->value)
            ->exists();
    }
    function isApprovedUser($userId): bool
    {
        return GroupUser::where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->exists();
    }

    function isOwner($userId){
        return $this->user_id ==$userId;
    }
    function adminUser():BelongsToMany
    {
        return $this->belongsToMany(User::class,'group_users')->wherePivot('role',GroupUserRole::ADMIN->value);
    }
    function approvedUser(){
        return $this->belongsToMany(User::class,'group_users')->wherePivot('status',GroupUserStatus::APPROVED->value);
    }
    function pendingUser(){
        return $this->belongsToMany(User::class,'group_users')->wherePivot('status',GroupUserStatus::PENDING->value);
    }
}
    