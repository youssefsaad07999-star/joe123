<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use App\Models\Order;
use App\OrderStatus;
use App\PaymentStatus;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order')
                    ->prefix('#')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Order ID copied'),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->weight('medium')
                    ->description(fn (Order $record) => $record->user?->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Items')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment.status')
                    ->label('Payment')
                    ->badge()
                    ->sortable()
                    ->default('No Pay. Record'),

                TextColumn::make('total_price')
                    ->money('USD')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('payment.method')
                    ->label('Pay. Method')
                    // ->badge()
                    ->formatStateUsing(fn ($state): string => match (strtolower($state ?? '')) {
                        'cod', 'cash on delivery' => 'Cash on D.',
                        'card' => 'Credit Card',
                        default => $state ?? 'N/A',
                    })
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'cod', 'cash on delivery' => 'warning',
                        'card' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'card' => 'heroicon-m-credit-card',
                        'cod', 'cash on delivery' => 'heroicon-m-banknotes',
                        default => 'heroicon-m-receipt-percent',
                    })
                    ->default('No Record'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->description(fn (Order $record) => $record->created_at?->diffForHumans())
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
            ])
            // ->poll('10s')
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class)
                    ->native(false),

                Filter::make('created_at')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('from')->label('From Date'),
                            DatePicker::make('to')->label('To Date'),
                        ]),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['to'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'From '.Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['to'] ?? null) {
                            $indicators[] = 'To '.Carbon::parse($data['to'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View Details'),

                Action::make('updateStatus')
                    ->icon('heroicon-o-arrow-path')
                    ->iconButton()
                    ->tooltip('Update Status')
                    ->color('warning')
                    ->modalWidth('md')
                    ->form([
                        Select::make('status')
                            ->options(OrderStatus::class)
                            ->native(false)
                            ->required()
                            ->live(),

                        Textarea::make('refund_reason')
                            ->label('Reason for Cancellation')
                            ->placeholder('e.g., Out of stock, customer requested cancellation')
                            ->rows(3)
                            ->required(fn (Get $get) => $get('status') === OrderStatus::Cancelled)
                            ->visible(fn (Get $get) => $get('status') === OrderStatus::Cancelled),
                    ])
                    ->action(function (Order $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $updateData = ['status' => $data['status']];

                            // If the order is being cancelled, save the reason and optionally restore inventory
                            if ($data['status'] === OrderStatus::Cancelled) {
                                $updateData['refund_reason'] = $data['refund_reason'] ?? null;

                                if (! in_array($record->status, [OrderStatus::Cancelled, OrderStatus::Refunded, OrderStatus::Pending])) {
                                    foreach ($record->variants as $variant) {
                                        $variant->increment('stock_quantity', $variant->pivot->quantity);
                                    }
                                }
                            }

                            $record->update($updateData);
                        });
                    })
                    ->disabled(fn (Order $record) => in_array($record->status, [OrderStatus::Cancelled, OrderStatus::Refunded])),

                Action::make('refundOrder')
                    ->label('Refund Order')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconButton()
                    ->tooltip('Refund Order')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Order $record) => 'Refund Order #'.$record->id)
                    ->modalIcon('heroicon-o-arrow-uturn-left')
                    ->modalDescription('Restores item inventory, marks payment as refunded, and updates order status.')
                    ->form([
                        Textarea::make('refund_reason')
                            ->label('Reason for Refund')
                            ->placeholder('e.g., Damaged item, customer canceled after delivery')
                            ->rows(3)
                            ->required(),
                    ])
                    ->disabled(fn (Order $record) => $record->status !== OrderStatus::Delivered)
                    ->action(function (Order $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            foreach ($record->variants as $variant) {
                                $variant->increment('stock_quantity', $variant->pivot->quantity);
                            }

                            if (in_array(strtolower($record->method ?? ''), ['cash on delivery', 'cod'])) {
                                $record->payment->updateOrCreate(
                                    ['order_id' => $record->id],
                                    [
                                        'status' => PaymentStatus::Refunded,
                                        'notes' => 'COD cash refunded to customer: '.$data['refund_reason'],
                                    ]
                                );
                            } else {
                                $record->payment?->update(['status' => PaymentStatus::Refunded]);
                            }

                            $record->update([
                                'status' => OrderStatus::Refunded,
                                'refund_reason' => $data['refund_reason'],
                                'refunded_at' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Order #'.$record->id.' successfully refunded')
                            ->body('Inventory has been restored and payment status updated.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('export_csv')
                        ->label('Export Selected to CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="orders-export-'.now()->format('Y-m-d').'.csv"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');
                                fputcsv($file, ['Order ID', 'Customer', 'Email', 'Items', 'Status', 'Total Price', 'Created At']);

                                foreach ($records as $order) {
                                    fputcsv($file, [
                                        $order->id,
                                        $order->user->name,
                                        $order->user->email,
                                        $order->variants->count(),
                                        $order->status->value ?? $order->status,
                                        $order->total_price,
                                        $order->created_at,
                                    ]);
                                }
                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                ]),
            ]);
    }
}
