<?php

namespace App\Filament\Resources\CategoryNewResource\Pages;

use App\Filament\Resources\CategoryNewResource as CategoryResource;
// use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CategoryLegacyResource\Pages\CreateCategory as CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
