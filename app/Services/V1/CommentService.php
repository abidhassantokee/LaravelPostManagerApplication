<?php

namespace App\Services\V1;

use App\Models\V1\Comment;

class CommentService
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * CommentService constructor.
     */
    public function __construct()
    {
        $this->comment = new Comment();
    }

    /**
     * Returns comment list by post id
     *
     * @param $postId
     * @param string $sortBy
     * @param string $orderBy
     * @param bool $view
     * @return mixed
     */
    public function getCommentsByPost($postId, $sortBy = 'created_at', $orderBy = 'desc', $view = false)
    {
        $post = Comment::with(['user'])->where('post_id', $postId)->orderBy($sortBy, $orderBy);
        if ($view) {
            return $post->paginate($view);
        }
        return $post->get();
    }

    /**
     * Creates a comment on a post
     *
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        return Comment::create([
            'comment' => $data['comment'],
            'post_id' => $data['post_id'],
            'user_id' => auth()->user()->id
        ]);
    }

    /**
     * Deletes a comment
     *
     * @param $postId
     * @param $commentId
     */
    public function destroy($postId, $commentId)
    {
        $this->comment = Comment::where('id', $commentId)->first();
        if (!$this->comment) {
            abort(404, 'Comment not found.');
        }
        if ($this->comment->user_id != auth()->user()->id) {
            abort(403, 'This action is unauthorized.');
        }
        if ($this->comment->post_id != $postId) {
            abort(422, 'Comment does not belong to this post.');
        }
        Comment::find($this->comment->id)->delete();
    }
}
