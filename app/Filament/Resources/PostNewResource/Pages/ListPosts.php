<?php

namespace App\Filament\Resources\PostNewResource\Pages;

use App\Filament\Resources\PostNewResource as PostResource;
// use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PostLegacyResource\Pages\ListPosts as ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;
}
