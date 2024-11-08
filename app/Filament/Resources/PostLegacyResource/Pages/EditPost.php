<?php

namespace App\Filament\Resources\PostLegacyResource\Pages;

use App\Filament\Resources\PostLegacyResource as PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['published']) {
            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }
        unset($data['published']);

        return $data;
    }
}
