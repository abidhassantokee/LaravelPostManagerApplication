<?php

namespace App\Services\V1;

use App\Models\V1\Like;

class LikeService
{
    /**
     * @var Like
     */
    private $like;

    /**
     * LikeService constructor.
     */
    public function __construct()
    {
        $this->like = new Like();
    }

    /**
     * Returns like list by post id
     *
     * @param $postId
     * @param string $sortBy
     * @param string $orderBy
     * @param bool $view
     * @return mixed
     */
    public function getLikesByPost($postId, $sortBy = 'created_at', $orderBy = 'desc', $view = false)
    {
        $post = Like::with(['user'])->where('post_id', $postId)->orderBy($sortBy, $orderBy);
        if ($view) {
            return $post->paginate($view);
        }
        return $post->get();
    }

    /**
     * Creates a like on a post
     *
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        $this->like = Like::where('post_id', $data['post_id'])->where('user_id', auth()->user()->id)->first();
        if ($this->like) {
            abort(422, 'Like already exist');
        }
        return Like::create([
            'post_id' => $data['post_id'],
            'user_id' => auth()->user()->id
        ]);
    }

    /**
     * Deletes a like
     *
     * @param $postId
     * @param $likeId
     */
    public function destroy($postId, $likeId)
    {
        $this->like = Like::where('id', $likeId)->first();
        if (!$this->like) {
            abort(404, 'Like not found.');
        }
        if ($this->like->user_id != auth()->user()->id) {
            abort(403, 'This action is unauthorized.');
        }
        if ($this->like->post_id != $postId) {
            abort(422, 'Like does not belong to this post.');
        }
        Like::find($this->like->id)->delete();
    }
}
