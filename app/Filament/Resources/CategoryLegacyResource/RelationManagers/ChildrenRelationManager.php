<?php

namespace App\Filament\Resources\CategoryLegacyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// use App\Filament\Resources\CategoryLegacyResource as CategoryResource;
use App\Filament\Resources\CategoryNewResource as CategoryResource;
use App\Models\Category;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn (): string => CategoryResource::getUrl('create', ['fill' => ['parent_id' => $this->getOwnerRecord()->getKey()]])),
                Tables\Actions\AssociateAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Category $record): string => CategoryResource::getUrl('edit', ['record' => $record])),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DissociateBulkAction::make(),
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->inverseRelationship('parent');
    }
}
