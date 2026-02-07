<?php

namespace App\Filament\Bk\Resources;

use App\Filament\Bk\Resources\SantriResource\Pages;
use App\Infrastructure\Persistence\Models\SantriProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class SantriResource extends Resource
{
    protected static ?string $model = SantriProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Data Santri';

    protected static ?string $modelLabel = 'Santri';

    protected static ?string $pluralModelLabel = 'Santri';

    protected static ?string $navigationGroup = 'Data Santri';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pribadi')
                    ->schema([
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('nama_panggilan')
                            ->label('Nama Panggilan')
                            ->maxLength(100),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir'),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Pondok')
                    ->schema([
                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('kamar')
                            ->label('Kamar')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('tahun_masuk')
                            ->label('Tahun Masuk')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(date('Y')),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'non_aktif' => 'Non-Aktif',
                                'lulus' => 'Lulus',
                                'keluar' => 'Keluar',
                            ])
                            ->default('aktif')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No. HP Santri')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nama_wali')
                            ->label('Nama Wali'),

                        Forms\Components\TextInput::make('no_whatsapp_wali')
                            ->label('No. WhatsApp Wali')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Khusus')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'L' ? 'info' : 'danger'),

                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kamar')
                    ->label('Kamar')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('points.total_poin_pelanggaran')
                    ->label('Poin Pelanggaran')
                    ->numeric()
                    ->color(fn ($state) => $state > 50 ? 'danger' : ($state > 20 ? 'warning' : 'success'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('points.total_poin_apresiasi')
                    ->label('Poin Apresiasi')
                    ->numeric()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non_aktif' => 'warning',
                        'lulus' => 'info',
                        'keluar' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non_aktif' => 'Non-Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                    ]),

                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->label('Jenis Kelamin'),

                Tables\Filters\SelectFilter::make('kelas')
                    ->options(fn () => SantriProfile::distinct()->pluck('kelas', 'kelas')->toArray())
                    ->label('Kelas'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_lengkap', 'asc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Data Pribadi')
                    ->schema([
                        Infolists\Components\TextEntry::make('nisn')
                            ->label('NISN'),
                        Infolists\Components\TextEntry::make('nama_lengkap')
                            ->label('Nama Lengkap'),
                        Infolists\Components\TextEntry::make('nama_panggilan')
                            ->label('Nama Panggilan'),
                        Infolists\Components\TextEntry::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                        Infolists\Components\TextEntry::make('tempat_lahir')
                            ->label('Tempat Lahir'),
                        Infolists\Components\TextEntry::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->date('d M Y'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Informasi Pondok')
                    ->schema([
                        Infolists\Components\TextEntry::make('kelas')
                            ->label('Kelas'),
                        Infolists\Components\TextEntry::make('kamar')
                            ->label('Kamar'),
                        Infolists\Components\TextEntry::make('tahun_masuk')
                            ->label('Tahun Masuk'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'aktif' => 'success',
                                'non_aktif' => 'warning',
                                'lulus' => 'info',
                                'keluar' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make('Poin')
                    ->schema([
                        Infolists\Components\TextEntry::make('points.total_poin_pelanggaran')
                            ->label('Total Poin Pelanggaran')
                            ->color(fn ($state) => $state > 50 ? 'danger' : ($state > 20 ? 'warning' : 'success'))
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('points.total_poin_apresiasi')
                            ->label('Total Poin Apresiasi')
                            ->color('success')
                            ->size('lg'),
                        Infolists\Components\TextEntry::make('points.current_konsekuensi_kode')
                            ->label('Level Konsekuensi'),
                        Infolists\Components\TextEntry::make('points.current_reward_kode')
                            ->label('Level Reward'),
                    ])
                    ->columns(4),
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
            'index' => Pages\ListSantris::route('/'),
            'create' => Pages\CreateSantri::route('/create'),
            'view' => Pages\ViewSantri::route('/{record}'),
            'edit' => Pages\EditSantri::route('/{record}/edit'),
        ];
    }
}