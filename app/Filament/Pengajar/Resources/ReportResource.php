<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\ReportResource\Pages;
use App\Infrastructure\Persistence\Models\Report;
use App\Infrastructure\Persistence\Models\SantriProfile;
use Domain\Enums\ReportStatus;
use Domain\Enums\ReportType;
use Domain\Enums\SantriRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $navigationLabel = 'Buat Laporan';

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan Saya';

    protected static ?string $navigationGroup = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Buat Laporan Baru')
                    ->description('Isi form berikut untuk membuat laporan tentang santri')
                    ->schema([
                        Forms\Components\Select::make('jenis')
                            ->label('Jenis Laporan')
                            ->options(ReportType::getLabels())
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('laporan_text')
                            ->label('Isi Laporan')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull()
                            ->placeholder('Tuliskan laporan secara detail...'),
                    ]),

                Forms\Components\Section::make('Santri Terlibat')
                    ->schema([
                        Forms\Components\Repeater::make('entities')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('santri_id')
                                    ->label('Santri')
                                    ->options(SantriProfile::active()->pluck('nama_lengkap', 'id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('role')
                                    ->label('Peran')
                                    ->options(SantriRole::getLabels())
                                    ->default('terlibat')
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah Santri')
                            ->defaultItems(0),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Report::query()->where('pelapor_id', auth()->id()))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT)),

                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn (ReportType $state): string => $state->color()),

                Tables\Columns\TextColumn::make('laporan_text')
                    ->label('Isi Laporan')
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ReportStatus $state): string => $state->color()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}