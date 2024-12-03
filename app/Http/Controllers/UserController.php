<?php

namespace App\Http\Controllers;

use App\Models\Followers;
use App\Models\User;
use App\Notifications\FollowUser;
//use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow(User $user,Request $request){
        $data = $request->validate([
            'follow'=>['boolean']
        ]);
        $message = '';
        if($data['follow']){
            Followers::create([
                'user_id'=>$user->id,
                'follower_id'=>Auth::id()
               ]);
               $message='Your follow user "'.$user->name.'"';
              
            }
        else{
            Followers::query()->where('user_id',$user->id)->where('follower_id',Auth::id())->delete();
            $message='Your unfollow user "'.$user->name.'"';
           
        }
       $user->notify(new FollowUser(Auth::user(),$data['follow']));
       return back()->with('success',$message);
    }
}
