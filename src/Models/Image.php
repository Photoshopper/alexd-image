<?php

namespace Alexd\Image\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * @var string
     */
    protected $table = 'images';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'filename',
        'alt',
        'title',
        'weight'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }

}
