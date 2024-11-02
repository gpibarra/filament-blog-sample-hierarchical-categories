<?php

namespace App\Filament\Resources\PostNewResource\Pages;

use App\Filament\Resources\PostNewResource as PostResource;
// use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PostLegacyResource\Pages\CreatePost as CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
}
