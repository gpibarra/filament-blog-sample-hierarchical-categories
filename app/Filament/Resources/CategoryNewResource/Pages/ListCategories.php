<?php

namespace App\Filament\Resources\CategoryNewResource\Pages;

use App\Filament\Resources\CategoryNewResource as CategoryResource;
// use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CategoryLegacyResource\Pages\ListCategories as ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;
}
