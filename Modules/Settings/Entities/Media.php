<?php



namespace Modules\Settings\Entities;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $connection = 'mysql';
}
