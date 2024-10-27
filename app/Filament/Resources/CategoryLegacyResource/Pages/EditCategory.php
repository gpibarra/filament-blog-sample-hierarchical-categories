<?php

namespace App\Filament\Resources\CategoryLegacyResource\Pages;

use App\Filament\Resources\CategoryLegacyResource as CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['name']) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        return $data;
    }
}
