<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\KbRuleResource\Pages;
use App\Infrastructure\Persistence\Models\KbRule;
use App\Infrastructure\Persistence\Models\KbDiagnosis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KbRuleResource extends Resource
{
    protected static ?string $model = KbRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?string $navigationLabel = 'KB Rules';

    protected static ?string $modelLabel = 'Rule';

    protected static ?string $navigationGroup = 'Knowledge Base';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rule Definition')
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode Rule')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('RA-01, RB-01, RC-01'),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Rule')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'korban' => 'Korban (RA)',
                                'pelaku' => 'Pelaku (RB)',
                                'internal' => 'Internal (RC)',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('diagnosis_kode')
                            ->label('Diagnosis')
                            ->options(KbDiagnosis::active()->pluck('nama', 'kode'))
                            ->required()
                            ->searchable()
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Conditions (IF)')
                    ->description('Kode kondisi yang harus terpenuhi (P001, G001, dll)')
                    ->schema([
                        Forms\Components\TagsInput::make('conditions')
                            ->label('Kode Kondisi')
                            ->placeholder('Masukkan kode dan tekan Enter')
                            ->required()
                            ->separator(',')
                            ->helperText('Masukkan kode seperti P001, G001, dll'),

                        Forms\Components\Select::make('operator')
                            ->label('Operator')
                            ->options([
                                'AND' => 'AND (Semua harus terpenuhi)',
                                'OR' => 'OR (Salah satu terpenuhi)',
                            ])
                            ->default('AND')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('min_match')
                            ->label('Minimum Match')
                            ->numeric()
                            ->default(0)
                            ->helperText('0 = semua harus match (untuk AND)'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('prioritas')
                            ->label('Prioritas')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('1 = tertinggi, 10 = terendah'),

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
                    ->color(fn ($record) => match ($record->kategori) {
                        'korban' => 'info',
                        'pelaku' => 'danger',
                        'internal' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('conditions')
                    ->label('Kondisi')
                    ->formatStateUsing(fn ($state) => implode(' & ', $state ?? []))
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('diagnosis.nama')
                    ->label('Diagnosis')
                    ->limit(25),

                Tables\Columns\TextColumn::make('prioritas')
                    ->label('Prioritas')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'korban' => 'Korban',
                        'pelaku' => 'Pelaku',
                        'internal' => 'Internal',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active'),
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
            'index' => Pages\ListKbRules::route('/'),
            'create' => Pages\CreateKbRule::route('/create'),
            'edit' => Pages\EditKbRule::route('/{record}/edit'),
        ];
    }
}