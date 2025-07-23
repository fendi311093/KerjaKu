<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormMultipleUploadResource\Pages;
use App\Filament\Resources\FormMultipleUploadResource\RelationManagers;
use App\Models\EmailAddress;
use App\Models\FormMultipleUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormMultipleUploadResource extends Resource
{
    protected static ?string $model = FormMultipleUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'E-Mail Uploads';
    protected static ?string $pluralLabel = 'List of E-Mail';

    protected static ?array $cachedEmailAddresses = null;

    public static function form(Form $form): Form
    {

        // Closure halaman Edit Email
        $isEditEmail = fn($livewire) => $livewire instanceof Pages\EditFormMultipleUpload;

        // Cache email addresses
        if (static::$cachedEmailAddresses === null) {
            static::$cachedEmailAddresses = EmailAddress::getOptionsEmail();
        }

        return $form
            ->schema([
                Section::make('Email Details')->schema([
                    Grid::make(2)->schema([
                        Select::make('to_email_address_id')
                            ->label('To Email Address')
                            ->options(static::$cachedEmailAddresses)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('cc_email_address_id')
                            ->label('CC Email Address')
                            ->options(static::$cachedEmailAddresses)
                            ->searchable()
                            ->preload(),
                        Select::make('status_sent')
                            ->options(function ($livewire) use ($isEditEmail) {
                                return $isEditEmail($livewire)
                                    ? [
                                        'Delivered' => 'Delivered',
                                        'Re-Send' => 'Re-Send',
                                    ]
                                    : [
                                        'Pending' => 'Pending',
                                        'Delivered' => 'Delivered',
                                        'Re-Send' => 'Re-Send',
                                    ];
                            })
                            ->default('Pending')
                            ->label('Status')
                            ->visible($isEditEmail)
                            ->required(),
                    ]),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Example: SKHU1234567 IN')
                        ->dehydrateStateUsing(fn($state) => strtoupper($state)),
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
                            ->previewable($isEditEmail),
                    ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('to')
                    ->label('To Email Address')
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary')
                    ->searchable(),
                TextColumn::make('cc')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject')
                    ->searchable(),
                ImageColumn::make('attachments')
                    ->label('Photos')
                    ->circular()
                    ->stacked()
                    ->ring(10)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_sent')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Delivered' => 'success',
                        'Re-Send' => 'danger',
                    }),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-')
                    ->formatStateUsing(fn($state) => $state ? $state->format('d M Y H:i') : '-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
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
            ])
            ->recordUrl(null)
            ->emptyStateHeading('No Email Uploads Found')
            ->emptyStateDescription('You have not uploaded any emails yet.')
            ->emptyStateIcon('heroicon-o-envelope');
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
