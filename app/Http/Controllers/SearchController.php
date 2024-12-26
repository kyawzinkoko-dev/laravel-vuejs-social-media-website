<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function search(Request $request, string $search = null)
    {

        //  $search = $request->get('keywords');
        if (!$search) {
            return redirect()->route('dashboard');
        }
        $users = User::query()->where('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%")
            ->get();
        $group = Group::query()->where('name', 'like', "%$search%")
            ->orWhere('about', 'like', "%$search%")
            ->get();
        $post = Post::postForTimeline(Auth::id())->where('body', 'like', "%$search%")->orderByDesc('created_at')->paginate(10);
        $post = PostResource::collection($post);
        if ($request->wantsJson()) {
            return $post;
        }
        return Inertia::render('Search', [
            'search' => $search,
            'users' => UserResource::collection($users),
            'posts' => $post,
            'groups' => GroupResource::collection($group),

        ]);
    }
}
