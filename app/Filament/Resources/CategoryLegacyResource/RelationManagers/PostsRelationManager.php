<?php

namespace App\Filament\Resources\CategoryLegacyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Resources\PostLegacyResource as PostResource;
use App\Filament\Resources\PostNewResource as PostResource;
use App\Models\Post;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'mainPosts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Título'))
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('Contenido'))
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Publicado'))
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn (): string => PostResource::getUrl('create', ['fill' => ['main_category_id' => $this->getOwnerRecord()->getKey()]])),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->inverseRelationship('mainCategory');
    }
}
