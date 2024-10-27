<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryLegacyResource\Pages;
use App\Filament\Resources\CategoryLegacyResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use App\Models\Category;

class CategoryLegacyResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('Categories Legacy');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::formFields(),
                Forms\Components\Select::make('parent_id')
                    ->label(__('Parent'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->nullable()
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'name',
                    )
                    ->afterStateHydrated(function (string $operation, Forms\Set $set) {
                        if ($operation == 'create') {
                            $fillQueryString = request()->query('fill');
                            $id = Arr::get($fillQueryString, 'parent_id');
                            if ($id) {
                                $set('parent_id', $id);
                            }
                        }
                    }),
            ]);
    }

    public static function formFields(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label(__('Name'))
                ->maxLength(255)
                ->required(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label(__('Parent'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PostsRelationManager::class,
            RelationManagers\ChildrenRelationManager::class,
        ];
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
