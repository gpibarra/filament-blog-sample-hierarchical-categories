<?php

namespace App\Filament\Resources\CategoryLegacyResource\Pages;

use App\Filament\Resources\CategoryLegacyResource as CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['name']) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        return $data;
    }
}
