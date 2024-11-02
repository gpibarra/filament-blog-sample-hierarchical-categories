<?php

namespace App\Filament\Resources\CategoryNewResource\Pages;

use App\Filament\Resources\CategoryNewResource as CategoryResource;
// use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategoryLegacyResource\Pages\EditCategory as EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
}
