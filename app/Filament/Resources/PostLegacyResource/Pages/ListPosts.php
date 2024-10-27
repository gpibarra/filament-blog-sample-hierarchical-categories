<?php

namespace App\Filament\Resources\PostLegacyResource\Pages;

use App\Filament\Resources\PostLegacyResource as PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
