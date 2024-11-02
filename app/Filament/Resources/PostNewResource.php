<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostNewResource\Pages;
use App\Filament\Resources\PostNewResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
// use Filament\Resources\Resource;
use App\Filament\Resources\PostLegacyResource as Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use App\Models\Post;

class PostNewResource extends Resource
{
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Posts New');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::formFields(),
                SelectTree::make('main_category_id')
                    ->columnStart(1)
                    ->label(__('Main Category'))
                    ->relationship(
                        relationship: 'mainCategory',
                        titleAttribute: 'name',
                        parentAttribute: 'parent_id',
                    )
                    ->searchable()
                    ->enableBranchNode()
                    ->withCount()
                    ->independent(true)
                    // ->expandSelected(true)
                    ->grouped(true)
                    ->clearable(true)
                    // ->live()
                    ->rules([
                        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (in_array($value, $get('categories'))) {
                                $fail('The Main Category and Categories must be different.');
                            }
                        },
                    ])
                    ->afterStateHydrated(function (string $operation, Forms\Set $set) {
                        if ($operation == 'create') {
                            $fillQueryString = request()->query('fill');
                            $id = (int) Arr::get($fillQueryString, 'main_category_id');
                            if ($id) {
                                $set('main_category_id', $id);
                            }
                        }
                    })
                    ->required(),
                SelectTree::make('categories')
                    ->label(__('Categories'))
                    ->relationship(
                        relationship: 'categories',
                        titleAttribute: 'name',
                        parentAttribute: 'parent_id',
                    )
                    // ->placeholder(__('Please select all categories'))
                    // ->emptyLabel(__('Oops, no results have been found!'))
                    ->searchable()
                    ->enableBranchNode()
                    ->withCount()
                    ->independent(true)
                    ->expandSelected(true)
                    ->grouped(false)
                    ->clearable(true),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
