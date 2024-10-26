<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['published']) {
            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }
        unset($data['published']);
        $data['user_id'] = auth()->id();

        return $data;
    }
}
