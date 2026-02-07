<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\KbPelanggaranResource\Pages;
use App\Infrastructure\Persistence\Models\KbPelanggaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KbPelanggaranResource extends Resource
{
    protected static ?string $model = KbPelanggaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'KB Pelanggaran';

    protected static ?string $modelLabel = 'Pelanggaran';

    protected static ?string $navigationGroup = 'Knowledge Base';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('P001'),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Pelanggaran')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('poin')
                            ->label('Poin')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(200),

                        Forms\Components\Textarea::make('konsekuensi')
                            ->label('Konsekuensi')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->color('danger')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('poin')
                    ->label('Poin')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state >= 20 ? 'danger' : ($state >= 10 ? 'warning' : 'success')),

                Tables\Columns\TextColumn::make('konsekuensi')
                    ->label('Konsekuensi')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            ->defaultSort('kode');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKbPelanggarans::route('/'),
            'create' => Pages\CreateKbPelanggaran::route('/create'),
            'edit' => Pages\EditKbPelanggaran::route('/{record}/edit'),
        ];
    }
}