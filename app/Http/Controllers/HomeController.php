<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $posts = Post::postForTimeline($userId)
            ->select('posts.*')
            ->leftJoin('followers AS f', function ($join) use ($userId) {
                /** @var \Illuminate\Database\Query\Builder $join */
                $join->on('posts.user_id', '=', 'f.user_id')
                    ->where('f.follower_id', '=', $userId);
            })
            ->leftJoin('group_users AS gu', function ($join) use ($userId) {
                $join->on('gu.group_id', '=', 'posts.group_id')
                    ->where('gu.user_id', '=', $userId)
                    ->where('gu.role', '=', GroupUserStatus::APPROVED->value);
            })
            ->where(function ($query) use ($userId) {
                /** @var \Illuminate\Database\Query\Builder $query */
                $query->whereNotNull('f.follower_id')
                    ->orWhereNotNull('gu.group_id');
            })
            ->whereNot('posts.user_id', $userId)
            ->paginate(2);
        //dd($posts);
        $group = Group::query()
            ->with('currentUserGroup')
            //->select(['groups.*'])
            ->join('group_users as gu', 'gu.group_id', 'groups.id')
            ->where('gu.user_id', Auth::id())
            ->where('status', GroupUserStatus::APPROVED->value)
            ->orderBy('gu.role')
            ->orderBy('name', 'desc')
            ->get();
        $posts = PostResource::collection($posts);
        $followings =Auth::user()->followings;

        if (request()->wantsJson()) {
            return $posts;
        }
        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => $group ? GroupResource::collection($group)->toArray(request()) : [],
            'followings' => $followings ? UserResource::collection($followings) : []
        ]);
    }
}
