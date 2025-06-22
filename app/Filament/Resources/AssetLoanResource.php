<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetLoanResource\Pages\ListAssetLoans;
use App\Models\Asset;
use App\Models\AssetLoan;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class AssetLoanResource extends Resource
{
    protected static ?string $model = AssetLoan::class;
    protected static ?string $navigationGroup = 'Request & Loan';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';



    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('asset.name')->label('Asset'),
                TextColumn::make('code')->label('Code'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                TextColumn::make('is_returned')
                    ->label('Returned')
                    ->badge()
                    ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No')
                    ->color(fn(bool $state) => $state ? 'success' : 'danger'),
            ])
            ->actions([
                // Di bagian approve action
Action::make('Approve')
    ->visible(fn($record) => $record->status === 'pending')
    ->form([
        Select::make('asset_id')
            ->label('Pilih Asset')
            ->options(function ($record) {
                return \App\Models\Asset::where('status', 'available')
                    ->where('name', $record->asset->name)
                    ->pluck('code', 'id');
            })
            ->required(),
    ])
    ->requiresConfirmation()
    ->color('success')
        ->action(function ($data, $record) {
            $asset = Asset::find($data['asset_id']);

            $user = $record->user;
            $departmentName = $user->department ?? 'UMUM';
            $departmentCode = preg_replace('/[aeiouAEIOU]/', '', $departmentName);

            $lastLoan = AssetLoan::whereNotNull('code')
                ->orderBy('created_at', 'desc')
                ->first();

            $lastNumber = 0;
            if ($lastLoan) {
                $parts = explode('/', $lastLoan->code);
                if (count($parts) > 0) {
                    $lastNumber = (int) ltrim($parts[0], '0');
                }
            }

            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $kodePeminjaman = "{$newNumber}/{$departmentCode}/{$asset->code}";

            $record->update([
                'status' => 'approved',
                'asset_id' => $asset->id,
                'code' => $kodePeminjaman,
            ]);

            $asset->update(['status' => 'loaned']);

            Notification::make()
                ->title('Permintaan disetujui')
                ->success()
                ->send();
        }),

                // Di bagian return action
                Action::make('Return')
                    ->visible(fn($record) => $record->status === 'approved' && !$record->is_returned)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $asset = $record->asset;
                        $record->update(['is_returned' => true]);
                        $asset->update(['status' => 'available']);
                    }),

                Action::make('Reject')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'rejected'])),

                Action::make('Delete History')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn($record) => in_array($record->status, ['approved', 'rejected']))
                    ->action(function ($record) {
                        $record->delete();
                        Notification::make()
                            ->title('Berhasil Di hapus')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update(['status' => 'rejected']);
                                }
                            }

                            Notification::make()
                                ->title('Permintaan berhasil ditolak.')
                                ->danger()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('return')
                        ->label('Return Selected')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'approved' && !$record->is_returned) {
                                    $record->update(['is_returned' => true]);
                                    // Update status asset menjadi available saat return
                                    $record->asset->update(['status' => 'available']);
                                }
                            }

                            Notification::make()
                                ->title('Pengembalian berhasil diproses.')
                                ->success()
                                ->send();
                        }),


                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetLoans::route('/'),
        ];
    }
}
