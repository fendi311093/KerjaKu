<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailAddressResource\Pages;
use App\Filament\Resources\EmailAddressResource\RelationManagers;
use App\Models\EmailAddress;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailAddressResource extends Resource
{
    protected static ?string $model = EmailAddress::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';
    protected static ?string $navigationLabel = 'Email Addresses';
    protected static ?string $pluralLabel = 'List of Email';
    protected static ?string $navigationGroup = 'MASTER DATA';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Address')
                    ->rules(fn($record) => EmailAddress::validationRules($record)['email'])
                    ->validationMessages(EmailAddress::getValidationMesages()['email']),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->onIcon('heroicon-o-check-badge')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('email')
                    ->label('Email Address')
                    ->icon('heroicon-o-at-symbol')
                    ->iconColor('success')
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Is Active')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->emptyStateHeading('No Email Addresses Found')
            ->emptyStateDescription('You have not added any email addresses yet.')
            ->emptyStateIcon('heroicon-o-at-symbol');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEmailAddresses::route('/'),
        ];
    }
}
