<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use App\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('Posts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...static::formFields(),
                Forms\Components\Select::make('main_category_id')
                    ->columnStart(1)
                    ->label(__('Main Category'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->relationship('mainCategory', 'name')
                    ->afterStateHydrated(function (string $operation, Forms\Set $set) {
                        if ($operation == 'create') {
                            $fillQueryString = request()->query('fill');
                            $id = Arr::get($fillQueryString, 'main_category_id');
                            if ($id) {
                                $set('main_category_id', $id);
                            }
                        }
                    })
                    ->rules([
                        fn (Forms\Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (in_array($value, $get('categories'))) {
                                $fail('The Main Category and Categories must be different.');
                            }
                        },
                    ])
                    ->required(),
                Forms\Components\Select::make('categories')
                    ->label(__('Categories'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->relationship('categories', 'name')
                    ->required(),
            ]);
    }

    public static function formFields(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label(__('Title'))
                ->columnSpanFull()
                ->required(),
            Forms\Components\MarkdownEditor::make('content')
                ->label(__('Content'))
                ->columnSpanFull()
                ->required(),
            Forms\Components\Checkbox::make('published')
                ->label(__('Published'))
                // ->columnSpanFull()
                ->default(false)
                ->afterStateHydrated(
                    fn (Forms\Components\Checkbox $component, ?Post $record) => $component->state($record?->published_at !== null)
                )
                ->hintIcon(
                    icon: fn (?Post $record) => ($record?->published_at) ? 'heroicon-m-calendar' : null,
                    tooltip: fn (?Post $record) => $record?->published_at?->format('Y-m-d H:i:s')
                ),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('Content'))
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Published'))
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mainCategory.name')
                    ->label(__('Main Category'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('Categories'))
                    ->badge(),
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
            //
        ];
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
