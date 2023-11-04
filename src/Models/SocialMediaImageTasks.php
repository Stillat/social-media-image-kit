<?php

namespace Stillat\SocialMediaImageKit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaImageTasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
    ];

    public function getTable()
    {
        return config('social-media-image-kit.queue.database.table', 'social_media_image_tasks');
    }
}
