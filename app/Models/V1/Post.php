<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'posts';
    protected $appends = ['like_count', 'comment_count'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'channel_name',
        'description',
        'image_url',
        'video_url',
        'attachment_urls',
        'author_id'
    ];

    /**
     * Returns the number of likes created on the post
     *
     * @return mixed
     */
    public function getLikeCountAttribute() {
        return Like::where('post_id', $this->id)->count();
    }

    /**
     * Returns the number of comments created on the post
     *
     * @return mixed
     */
    public function getCommentCountAttribute() {
        return Comment::where('post_id', $this->id)->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne('App\Models\V1\User', 'id', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\V1\Comment', 'post_id', 'id')->with(['user']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany('App\Models\V1\Like', 'post_id', 'id')->with(['user']);
    }
}
