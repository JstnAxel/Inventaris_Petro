<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StationaryResource\Pages;
use App\Models\Stationary;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class StationaryResource extends Resource
{
    protected static ?string $model = Stationary::class;
    protected static ?int $recordsPerPage = 5;
    protected static ?string $slug = 'inventory/stationery';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    protected static ?string $navigationGroup = 'Inventory';

    public static function getModelLabel(): string
    {
        return 'Stationery';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Stationery';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('code')->label('Code')->disabled()->dehydrated(false),
            TextInput::make('name')->required(),
            Select::make('category_id')
                ->relationship('category', 'name', fn($query) => $query->where('type', 'stationery'))
                ->label('Category')->required(),
            TextInput::make('stock')->numeric()->required(),
            TextInput::make('unit')->required(),
            Textarea::make('note'),
            TextInput::make('price')->numeric()->prefix('Rp')->suffix('.00'),
            FileUpload::make('image')->image()->disk('public')->directory('stationary'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\Action::make('exportExcel')
                    ->label('Export ke Excel')
                    ->icon('heroicon-o-document')
                    ->url(route('export.stationary'))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('filterPeriode')
                    ->label('Pilih Bulan & Tahun')
                    ->color('info')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->default(now()->format('m'))->live(),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options([
                                now()->subYear()->format('Y') => now()->subYear()->format('Y'),
                                now()->format('Y') => now()->format('Y'),
                                now()->addYear()->format('Y') => now()->addYear()->format('Y'),
                            ])
                            ->default(now()->format('Y'))->live(),
                    ])
                    ->action(function (array $data) {
                        session([
                            'filter_bulan' => $data['bulan'],
                            'filter_tahun' => $data['tahun'],
                        ]);
                    }),
                Tables\Actions\Action::make('resetPeriode')
                    ->label('Reset Periode')
                    ->color('danger')
                    ->action(function () {
                        session()->forget(['filter_bulan', 'filter_tahun']);
                    }),

            ])
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('category.name')->label('Kategori'),

                TextColumn::make('pemasukan')
                    ->label('Pemasukan')
                    ->getStateUsing(function ($record) {
                        $bulan = session('filter_bulan', now()->format('m'));
                        $tahun = session('filter_tahun', now()->format('Y'));

                        $jumlah = $record->stockHistories()
                            ->whereMonth('created_at', $bulan)
                            ->whereYear('created_at', $tahun)
                            ->sum('amount');

                        return "{$jumlah} unit";
                    }),

                TextColumn::make('pengeluaran')
                    ->label('Pengeluaran')
                    ->getStateUsing(function ($record) {
                        $bulan = session('filter_bulan', now()->format('m'));
                        $tahun = session('filter_tahun', now()->format('Y'));

                        $jumlah = $record->stationeryRequests()
                            ->where('status', 'approved')
                            ->whereMonth('created_at', $bulan)
                            ->whereYear('created_at', $tahun)
                            ->sum('quantity');

                        return "{$jumlah} unit";
                    }),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->formatStateUsing(fn($record) => "{$record->stock} {$record->unit}"),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) => !$record->trashed()),
                Tables\Actions\DeleteAction::make()->visible(fn($record) => !$record->trashed()),
                Tables\Actions\RestoreAction::make()->visible(fn($record) => $record->trashed()),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Delete Permanent')
                    ->visible(fn($record) => $record->trashed())
                    ->requiresConfirmation()
                    ->color('danger'),
                Tables\Actions\Action::make('tambah_stok')
                    ->label('Tambah Stok')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->form([
                        TextInput::make('jumlah')
                            ->label('Jumlah Stok Tambahan')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->action(function (Model $record, array $data): void {
                        $record->increment('stock', $data['jumlah']);

                        $record->stockHistories()->create([
                            'amount' => $data['jumlah'],
                        ]);

                        Notification::make()
                            ->title('Stok berhasil ditambahkan')
                            ->success()
                            ->body("Stok {$record->name} bertambah {$data['jumlah']} unit.")
                            ->send();
                    })
                    ->visible(fn($record) => !$record->trashed()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStationaries::route('/'),
            'create' => Pages\CreateStationary::route('/create'),
            'edit' => Pages\EditStationary::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);

        $bulan = session('filter_bulan', null);
        $tahun = session('filter_tahun', null);

        if (!is_null($bulan) && !is_null($tahun)) {
            $query->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun);
        }

        return $query;
    }
}
