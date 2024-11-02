<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryNewResource\Pages;
use App\Filament\Resources\CategoryNewResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
// use Filament\Resources\Resource;
use App\Filament\Resources\CategoryLegacyResource as Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use App\Models\Category;

class CategoryNewResource extends Resource
{
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Categories New');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::formFields(),
                SelectTree::make('parent_id')
                    ->label(__('Parent'))
                    ->relationship(
                        relationship: 'parent',
                        titleAttribute: 'name',
                        parentAttribute: 'parent_id',
                    )
                    ->enableBranchNode()
                    ->searchable()
                    ->withCount()
                    ->independent(true)
                    ->expandSelected(true)
                    ->grouped(true)
                    ->clearable(true)
                    // ->live()
                    ->afterStateHydrated(function (string $operation, Forms\Set $set) {
                        if ($operation == 'create') {
                            $fillQueryString = request()->query('fill');
                            $id = (int) Arr::get($fillQueryString, 'parent_id');
                            if ($id) {
                                $set('parent_id', $id);
                            }
                        }
                    })
                    ->nullable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
