<?php

namespace App\Filament\Resources\PostNewResource\Pages;

use App\Filament\Resources\PostNewResource as PostResource;
// use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PostLegacyResource\Pages\EditPost as EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
}
