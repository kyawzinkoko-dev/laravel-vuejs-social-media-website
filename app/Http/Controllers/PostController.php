<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Notifications\CommentCreated;
use App\Notifications\CommentDeleted;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use App\Notifications\ReactionAddedOnComment;
use App\Notifications\ReactionAddedOnPost;
use DOMDocument;
use DOMElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PostController extends Controller
{

    public function store(StorePostRequest $request)
    {

        $allFiles = [];
        $data = $request->validated();
        
        $user = request()->user();
        DB::beginTransaction();
        try {

            $post = Post::create($data);

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFiles[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }
            DB::commit();
            $group = $post->group;
            if ($group) {
                Notification::send($group->approvedUser->where('id', '!=', $user->id), new PostCreated($post, $user, $group));
            }
            $followers = $user->followers;
            Notification::send($followers, new PostCreated($post, $user, null));
        } catch (\Exception $e) {
            foreach ($allFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            DB::rollBack();
            throw $e;
        }

        return back();
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $user = request()->user();
        DB::beginTransaction();
        //dd($request->validated());
        try {

            $data =  $request->validated();
            $post->update($request->validated());
            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            $deleted_ids = $data['delete_file_ids'] ?? [];
            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();

            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFiles[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFiles as $path) {
                Storage::disk('public')->delete($path);
            }

            DB::rollBack();
            throw $e;
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $id = Auth::id();
        if ($post->group && $post->group->isAdmin($id) || $post->isOwner($id)) {
            // dd($id);
            $post->delete();
            if (!$post->isOwner($id)) {
                //  dd('ere');
                $post->user->notify(new PostDeleted($post->group));
            }
            return back();
        }
        return response(" You don't have permission to delete this post", 403);
    }

    public function downloadAttachments(PostAttachment $attachment)
    {
        return response()->download(Storage::disk('public')->path($attachment->path));
    }

    //reaction 
    public function postReaction(Post $post, Request $request)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);
        $userId = Auth::id();
        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->first();
        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);
            $user = $post->user;
            if (!$post->isOwner($userId)) {
                $user->notify(new ReactionAddedOnPost($post, Auth::user()));
            }
        }

        $reactionCount = Reaction::where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->count();
        return response([
            'current_user_has_reaction' => $hasReaction,
            'num_of_reactions' => $reactionCount,
            'reaction' => $reactionCount,
        ]);
    }
    public function createComment(Post $post, Request $request)
    {
        $data =  $request->validate([
            'comment' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);
        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'comment' =>  nl2br($data['comment']),
            'parent_id' => $data['parent_id']
        ]);
        $post = $comment->post;
        $post->user->notify(new CommentCreated($comment, $post));
        return response(new CommentResource($comment), 201);
    }
    public function deleteComment(Comment $comment)
    {
        $userId = Auth::id();
        $post = $comment->post;
        if ($comment->isOwner($userId) || $post->isOwner($userId)) {
            $comment->delete();
            if (!$comment->isOwner($userId)) {
                $comment->user->notify(new CommentDeleted($comment, $post));
            }
            return response('', 204);
        }
        return response("You don't have permission to delete this comment", 403);
    }
    public function updateComment(UpdateCommentRequest $request, Comment $comment)
    {

        $data = $request->validated();
        $comment->update([
            'comment' => nl2br($data['comment'])
        ]);
        return new CommentResource($comment);
    }


    //reaction on comment
    public function commentReaction(Comment $comment, Request $request)

    {
        $hasReaction = null;
        $data = $request->validate([
            'reaction' => Rule::enum(
                ReactionEnum::class
            )
        ]);
        $userId = Auth::id();
        $reaction = Reaction::query()
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class)
            ->where('user_id', $userId)
            ->first();
        //dd($reaction);
        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'user_id' => $userId,
                'object_id' => $comment->id,
                'object_type' => Comment::class,
                'type' => $data['reaction']
            ]);
            $user = $comment->user;
            if (!$comment->isOwner($userId)) {
                $user->notify(new ReactionAddedOnComment(Auth::user(), $comment, $comment->post));
            }
        }
        $reactionCount = Reaction::where('object_id', $comment->id)->where('object_type', Comment::class)->count();
        return response([
            'current_user_has_reaction' => $hasReaction,
            'num_of_reactions' => $reactionCount
        ]);
    }
    public function view(Post $post)
    {
        $post->loadCount('reactions');
        $post->load([
            'comments' => function ($query) {
                $query->withCount('reactions');
            }
        ]);
        return Inertia::render('Post/View', [
            'post' => new PostResource($post)
        ]);
    }

   public function fetchUrlPreview(Request $req){
    $data = $req->validate([
        'url'=>'url'
    ]);
    
    $url = $data['url'];
    $content = file_get_contents($url);

    $dom = new \DOMDocument();

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);

        // Load HTML content into the DOMDocument
        $dom->loadHTML($content);

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(false);

        $ogTags = [];
        $metaTags = $dom->getElementsByTagName('meta');
        foreach ($metaTags as $tag ) {
            $property = $tag->getAttribute('property');
            if (str_starts_with($property, 'og:')) {
                $ogTags[$property] = $tag->getAttribute('content');
            }
        }

        return  $ogTags;
   }

}
