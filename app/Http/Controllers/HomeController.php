<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(){
        $posts =PostResource::collection( Post::all());
      
         $group = Group::query()
                ->with('currentUserGroup')
                 //->select(['groups.*'])
                 ->join('group_users as gu','gu.group_id','groups.id')
                 ->where('gu.user_id',Auth::id())
                 ->where('status',GroupUserStatus::APPROVED->value)
                 ->orderBy('gu.role')
                 ->orderBy('name','desc')
                 ->get();
               
        return Inertia::render('Home',[
            'posts'=>$posts,
            'groups'=>$group ? GroupResource::collection($group)->toArray(request()) :[]
        ]);
    }
}
