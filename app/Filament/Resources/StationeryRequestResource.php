<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StationeryRequestResource\Pages\ListStationeryRequests;
use App\Models\StationeryRequest;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class StationeryRequestResource extends Resource
{
    protected static ?string $model = StationeryRequest::class;
    protected static ?string $navigationGroup = 'Request & Loan';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('User'),
                TextColumn::make('stationary.name')->label('Item')->searchable(),
                TextColumn::make('stationary.stock')
                    ->label('Stok')
                    ->formatStateUsing(fn ($record) => "{$record->stationary->stock} {$record->stationary->unit}"),
                TextColumn::make('quantity'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
            ])
            ->actions([
                Action::make('Approve')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        if ($record->stationary->stock >= $record->quantity) {
                            $record->update(['status' => 'approved']);
                            $record->stationary->decrement('stock', $record->quantity);
                            Notification::make()
                                ->title('Permintaan disetujui')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Stok tidak mencukupi')
                                ->danger()
                                ->send();
                        }
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
        ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])

        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => ListStationeryRequests::route('/'),
        ];
    }
}
