<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupUserResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\User;
use App\Notifications\GroupUserRemoved;
use App\Notifications\InvitationApproved;
use App\Notifications\InvitationInGroup;
use App\Notifications\RequestApproved;
use App\Notifications\RequestToJoinGroup;
use App\Notifications\RoleChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(Group $group,Request $request)
    {
        $userId = Auth::id();
        $group->load('currentUserGroup');

        if($group->isApprovedUser($userId)){
            $posts = Post::postForTimeline($userId)
        ->where('group_id',$group->id)
        ->paginate(2);
        }
        else{
            $posts = null;
        }
        
        if($request->wantsJson()){
            return PostResource::collection($posts);
        }

        $user = User::query()
        ->select(['users.*','gu.role','gu.status','gu.group_id'])
        ->join('group_users as gu','gu.user_id','users.id')
        ->where('gu.status',GroupUserStatus::APPROVED->value)
        ->where('group_id',$group->id)
        ->orderBy('users.name')
        ->get();
        //dd($user);
        $pendingUser = $group->pendingUser()->orderBy('name')->get();
        return Inertia::render("Group/View", [
            //'status'=>
            'posts'=>$posts ?  PostResource::collection($posts) : null,
            'group' => new GroupResource($group),
            'users' => GroupUserResource::collection($user),
            'pendingUsers' => UserResource::collection($pendingUser),
            'success' => session('success')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $group = Group::create($data);
        $groupUser = [
            'status' => GroupUserStatus::APPROVED->value,
            'role' => GroupUserRole::ADMIN->value,
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ];
        //dd($groupUser);
        GroupUser::create($groupUser);
        $group->status = $groupUser['status'];
        $group->role = $groupUser['role'];
        return response(new GroupResource($group), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        return redirect()->back()->with('success','Group updated successfully ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }

    public function updateImage(Request $request, Group $group)
    {
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'thumbnail' => ['nullable', 'image']
        ]);
        // dd($data['thumbnail']);
        $cover = $data['cover'] ?? null;
        /** @var \Illuminate\Http\UploadedFile  $cover*/
        $thumbnail = $data['thumbnail'] ?? null;
        $success = '';
        if ($cover) {
            if ($group->cover_path) {
                Storage::disk('public')->delete($group->cover_path);
            }
            $path = $cover->store('group-cover-' . $group->id, 'public');
            $group->update(['cover_path' => $path]);

            $success = 'You cover photo was updated';
        }
        if ($thumbnail) {
            if ($group->thumbnail_path) {
                Storage::disk('public')->delete($group->thumbnail_path);
            }
            $path = $thumbnail->store('group-thumbnail-' . $group->id, 'public');
            $group->update(['thumbnail_path' => $path]);
            $success = 'You thumbnail was updated';
        }

        return back()->with('success', $success);
    }

    //
    public function inviteUser(InviteUserRequest $request, Group $group)
    {

        $request->validated();
        $user = $request->user;
        $groupUser = $request->groupUser;
        if ($groupUser) {
            $groupUser->delete();
        }
        $token = Str::random(256);
        $hours = 24;
        GroupUser::create([
            'status' => GroupUserStatus::PENDING->value,
            'role' => GroupUserRole::USER->value,
            'token' => $token,
            'token_expire_date' => Carbon::now()->addHours($hours),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => Auth::id()
        ]);
        $user->notify(new InvitationInGroup($group, $hours, $token));
        return back()->with('success', 'User was invited');
    }
    public function approveInvitation(string $token)
    {
        $groupUser = GroupUser::query()
            ->where('token', $token)
            ->first();

        $errorTitle = '';
        if (!$groupUser) {
            $errorTitle = 'The link is not valid';
        } else if ($groupUser->token_used || $groupUser->status === GroupUserStatus::APPROVED->value) {
            $errorTitle = 'The link is already used';
        } else if ($groupUser->token_expire_date < Carbon::now()) {

            $errorTitle = 'The link is expired';
        }

        if ($errorTitle) {
            return inertia('Error', compact('errorTitle'));
        }
        $groupUser->status = GroupUserStatus::APPROVED->value;
        $groupUser->token_used = Carbon::now();
        $groupUser->save();

        $adminUser = $groupUser->adminUser;
        $adminUser->notify(new InvitationApproved($groupUser->group, $groupUser->user));
        return redirect(route('group.profile', $groupUser->group))->with('success', 'You have joined the group *' . $groupUser->group->name . '*');
    }

    ///join to gorup
    public function joinGroup(Group $group, Request $request)
    {
        $user = $request->user();
        $status = GroupUserStatus::APPROVED->value;
        $successMessage = 'You successfully join the group *' . $group->name . '*';
        if (!$group->auto_approve) {
            $status = GroupUserStatus::PENDING->value;
            //send email 
            // dd($group->adminuser);
            Notification::send($group->adminUser, new RequestToJoinGroup($user, $group));
            $successMessage = 'You request have been sent to admin. You will be notified when admin approved';
        }
        GroupUser::create([
            'status' => $status,
            'role' => GroupUserRole::USER->value,
            'user_id' => $user->id,
            'group_id' => $group->id,
            'created_by' => $user->id
        ]);
        return back()->with('success', $successMessage);
    }

    function approveRequest(Group $group, Request $request)
    {
        if (!$group->isAdmin(Auth::id())) {
            return response('You don\'t have permission to perform this action ',403);
        }
        $data = $request->validate([
            'user_id' => ['required'],
            'action' => ['required', 'string']
        ]);
        $groupUser = GroupUser::query()
            ->where('group_id', $group->id)
            ->where('user_id', $data['user_id'])
            ->where('status', GroupUserStatus::PENDING->value)
            ->first();
        if ($groupUser) {
            $approved = false;
            if ($data['action'] === 'approve') {
                $approved=true;
                $groupUser->status = GroupUserStatus::APPROVED->value;
            } else {
                $groupUser->status = GroupUserStatus::REJECT->value;
            }
            $groupUser->save();
            $user = $groupUser->user;
            $user->notify(new RequestApproved($groupUser->group, $user,$approved));
           return back()->with('success', 'User ' . $user->name . ' was ' .( $approved ? "approved" : "reject" ). '');
        
           
        }
        return back();
    }

    public function changeRole(Group $group,Request $request){
        if(!$group->isAdmin(Auth::id())){
            return response('You don\'t have permission to perform this action',403);
        }
        $data= $request->validate([
            'user_id'=>['required'],
            'role'=>['required',Rule::enum(GroupUserRole::class)]
        ]);
        $user_id=$data['user_id'];
        if(!$group->isOwner($user_id)){
            return response('You don\'t have the permission the chagne the role of the owner',403);
        }
        $groupUser = GroupUser::query()
            ->where('group_id',$group->id)
            ->where('user_id',$user_id)
            ->first();
        if($groupUser){
            $groupUser->role = $data['role'];
            $groupUser->save();
            $groupUser->user->notify(new RoleChanged($group,$data['role']));

            return back();
        }
        
       

    }
    public function deleteUser(Request $req,Group $group){

            if(!$group->isAdmin(Auth::id())){
                return response("You don't have permission to do this action");
            }
            $data = $req->validate([
                'user_id'=>'required'
            ]);
            
            $userId = $data['user_id'];
            if($group->isOwner($userId)){
                return  response('The owner of the group cannot be removed');
            }
            $gropUser  =GroupUser::where('user_id',$userId)
            ->where('group_id',$group->id)
            ->first();
            if($gropUser){
                $user = $gropUser->user;
                $user->delete();
                $user->notify(new GroupUserRemoved($group));
                return back();
            }
            

    }
}
