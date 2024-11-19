<?php

namespace App\Http\Resources;

use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $comments = $this->comments;
        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->user),
            'group' => $this->group,
            'attachments' => PostAttachmentResource::collection($this->attachments),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::converCommentsIntoTree($comments),
        ];
    }


    /**
     * @param \App\Models\Comment[] $comments
     * @param $parentId
     * @return array
     *      
     * */
    private static function converCommentsIntoTree($comments, $parentId = null): array
    {
        $commentTree = [];
        foreach ($comments as $comment) {
            if ($comment->parent_id === $parentId) {
                //find all comments which have parent_id equal to $comment->id
                $children = self::converCommentsIntoTree($comments, $comment->id);
                $comment->numOfComments = collect($children)->sum('numOfComments') +count($children);
                $comment->childComments = $children;
                $commentTree[] = new CommentResource($comment);
            }
        }
        return $commentTree;
    }
}
