<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormMultipleUploadResource\Pages;
use App\Filament\Resources\FormMultipleUploadResource\RelationManagers;
use App\Models\FormMultipleUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormMultipleUploadResource extends Resource
{
    protected static ?string $model = FormMultipleUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Email Details')->schema([
                    Grid::make(2)->schema([
                        TextInput::make('to')
                        ->required()
                        ->default('fendi.toro@gmail.com')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('cc')
                        ->maxLength(255)
                        ->email()
                        ->placeholder('Optional CC email address'),
                    ]),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Example: SKHU1234567 IN'),
                ]),
                Section::make()
                    ->description('Maximum 40 files, each up to 10 MB in size.')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('primary')
                    ->schema([
                        FileUpload::make('attachments')
                        ->multiple()
                        ->disk('public')
                        ->directory('attachments')
                        ->maxFiles(40)
                        ->maxSize(10240) // 10 MB
                        ->required()
                        ->image()
                        ->previewable(false),
                    ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('to')
                    ->searchable(),
                TextColumn::make('cc')
                    ->searchable(),
                    TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListFormMultipleUploads::route('/'),
            'create' => Pages\CreateFormMultipleUpload::route('/create'),
            'edit' => Pages\EditFormMultipleUpload::route('/{record}/edit'),
        ];
    }
}
