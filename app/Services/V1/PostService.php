<?php

namespace App\Services\V1;

use App\Models\V1\Comment;
use App\Models\V1\Like;
use App\Models\V1\Post;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PostService
{
    /**
     * @var Post
     */
    private $post;

    /**
     * Upload files to S3
     *
     * @param $files
     * @return array
     */
    private function uploadFilesToS3($files)
    {
        $uploadedFiles = [];
        foreach ($files as $file) {
            $filePath = 'post-files/' . auth()->user()->id .
                '/' .
                Hash::make(str_random(20)) .
                '.' .
                $file->getClientOriginalExtension();
            if (!App::environment('testing')) {
                Storage::disk('s3')->put($filePath, file_get_contents($file));
            }

            $uploadedFiles[] = $filePath;
        }
        return $uploadedFiles;
    }

    /**
     * PostService constructor.
     */
    public function __construct()
    {
        $this->post = new Post();
    }

    /**
     * Returns post list
     *
     * @param string $sortBy
     * @param string $orderBy
     * @param bool $view
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function showAll($sortBy = 'created_at', $orderBy = 'desc', $view = false)
    {
        $post = Post::with(['author'])->orderBy($sortBy, $orderBy);
        if ($view) {
            return $post->paginate($view);
        }
        return $post->get();
    }

    /**
     * Returns a post details
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $post = Post::where('id', $id)
            ->with([
                'author',
                'comments',
                'likes'
            ])
            ->first();
        if (!$post) {
            abort(404, 'Post not found.');
        }
        return $post;
    }

    /**
     * Creates a post
     *
     * @param $data
     * @return mixed
     */
    public function store($data)
    {
        $uploadedImage = null;
        if (isset($data['upload_image'])) {
            $uploadedImage = $this->uploadFilesToS3($data['upload_image'])[0];
        }

        $uploadedVideo = null;
        if (isset($data['upload_video'])) {
            $uploadedVideo = $this->uploadFilesToS3($data['upload_video'])[0];
        }

        $uploadedFiles = [];
        if (!empty($data['upload_files'])) {
            $uploadedFiles = $this->uploadFilesToS3($data['upload_files']);
        }

        return Post::create([
            'title' => $data['title'],
            'channel_name' => $data['channel_name'],
            'description' => $data['description'],
            'image_url' => $uploadedImage,
            'video_url' => $uploadedVideo,
            'attachment_urls' => !empty($uploadedFiles) ? json_encode($uploadedFiles) : null,
            'author_id' => auth()->user()->id
        ]);
    }

    /**
     * Updates a post
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id)
    {
        $editablePost = Post::where('id', $id)
            ->where('author_id', auth()->user()->id)
            ->first();

        if (!$editablePost) {
            abort('404', "There is no post registered by this id.");
        }

        $uploadedImage = isset($data['image_url']) ? $data['image_url'] : $editablePost->image_url;
        if (isset($data['upload_image'])) {
            $uploadedImage = $this->uploadFilesToS3($data['upload_image'])[0];
        }

        $uploadedVideo = isset($data['video_url']) ? $data['video_url'] : $editablePost->video_url;
        if (isset($data['upload_video'])) {
            $uploadedVideo = $this->uploadFilesToS3($data['upload_video'])[0];
        }

        $uploadedFiles = [];
        if (!empty($data['upload_files'])) {
            $uploadedFiles = $this->uploadFilesToS3($data['upload_files']);
        }

        if (!empty($data['attachment_urls'])) {
            if (!empty(json_decode($editablePost->attachment_urls))) {
                $deletableFiles = array_diff(json_decode($editablePost->attachment_urls), $data['attachment_urls']);

                foreach ($deletableFiles as $fileToDelete) {
                    Storage::disk('s3')->delete($fileToDelete);
                }
            }
            $uploadedFiles = array_merge($uploadedFiles, $data['attachment_urls']);
        }

        return $this->post->where('id', $id)->update([
            'title' => $data['title'],
            'channel_name' => $data['channel_name'],
            'description' => $data['description'],
            'image_url' => $uploadedImage,
            'video_url' => $uploadedVideo,
            'attachment_urls' => $uploadedFiles,
            'author_id' => auth()->user()->id
        ]);
    }

    /**
     * Deletes a post details
     *
     * @param $id
     */
    public function destroy($id)
    {
        $this->post = Post::where('id', $id)->first();
        if (!$this->post) {
            abort(404, 'Post not found.');
        }
        if ($this->post->author_id != auth()->user()->id) {
            abort(403, 'This action is unauthorized.');
        }

        DB::beginTransaction();
        Post::find($this->post->id)->delete();
        Comment::where('post_id', $this->post->id)->delete();
        Like::where('post_id', $this->post->id)->delete();
        DB::commit();
    }
}
