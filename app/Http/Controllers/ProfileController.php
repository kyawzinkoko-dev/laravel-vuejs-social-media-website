<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Followers;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;


class ProfileController extends Controller
{

    /**
     * 
     * 
     */
    public function index(Request $request,User $user)
    {
        $isCurrentUserFollower = false;
        if (!Auth::guest()) {
            $follow = Followers::where('user_id', $user->id)
                ->where('follower_id', Auth::id())
                ->exists();
            if ($follow) {
                $isCurrentUserFollower = true;
            }
        }
        $followerCount = Followers::query()->where('user_id',$user->id)->count();
        $posts=Post::postForTimeline($user->id)
        ->where('user_id',$user->id)
        ->paginate(2);
        $posts = PostResource::collection($posts);
        if($request->wantsJson()){
            return $posts;
        }
        $follower = User::query()
        ->select('users.*')
        ->join('followers','followers.follower_id','users.id')
        ->where('followers.user_id',$user->id)
        ->get();
        $following = User::query()
        ->select('users.*')
        ->join('followers','followers.user_id','users.id')
        ->where('followers.follower_id',$user->id)
        ->get();
       //dd($following);
        return Inertia::render('Profile/View', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'followerCount'=>$followerCount,
            'user' => new UserResource($user),
            'posts'=>$posts,
            'followers'=>UserResource::collection($follower),
            'followings'=>UserResource::collection($following)

        ]);
    }

    /**
     * 
     * update proiffile
     */
    public function updateImage(Request $request)
    {
        $data = ($request->validate([
            'cover' => ['nullable', 'image'],
            'avatar' => ['nullable', 'image']
        ]));
        $cover = $data['cover'] ?? null;
        /** @var \Illuminate\Http\UploadedFile  $cover*/
        $avatar = $data['avatar'] ?? null;
        $user = $request->user();
        $success = '';
        if ($cover) {
            if ($user->cover_path) {
                Storage::disk('public')->delete($user->cover_path);
            }
            $path = $cover->store('user-cover' . $user->id, 'public');
            $user->update(['cover_path' => $path]);
            $success = 'You cover photo was updated';
        }
        if ($avatar) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $avatar->store('user-avatar-' . $user->id, 'public');
            $user->update(['avatar_path' => $path]);
            $success = 'You avatar was updated';
        }
        return back()->with('success', $success);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
