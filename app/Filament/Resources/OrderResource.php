<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Courier;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderStatusService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Log;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Pesanan';
    
    protected static ?string $modelLabel = 'Pesanan';
    
    protected static ?string $pluralModelLabel = 'Pesanan';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pesanan')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Nomor Pesanan')
                            ->required()
                            ->disabled()
                            ->default(fn () => Order::generateUniqueTrxId()),

                        TextInput::make('created_at')
                            ->label('Tanggal Pesan')
                            ->disabled()
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d M Y H:i')),

                        TextInput::make('grand_total_amount')
                            ->label('Total')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled(),
                    ])->columns(3),


                Section::make('Informasi Pelanggan')
                    ->schema([

                        Select::make('user_id')
                        ->label('Pelanggan')
                        ->relationship('user', 'name') // Relasi dengan user
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            Log::info('Callback afterStateUpdated dipanggil dengan state: ' . ($state ?? 'null'));
                        
                            $user = User::find($state);
                        
                            if ($user) {
                                Log::info('User ditemukan: ' . $user->name);
                                Log::info('Nomor telepon: ' . $user->phone);
                            } else {
                                Log::info('User tidak ditemukan.');
                            }
                        
                            $set('user_phone', $user?->phone);
                        }),

                        TextInput::make('user_phone')
                            ->label('No Telepon')
                            ->disabled()
                            ->default(fn ($record) => $record?->user?->phone ?? 'No phone available'),

                        TextInput::make('post_code')
                            ->label('Kode Pos')
                            ->required(),

                        TextInput::make('city')
                            ->label('Kota')
                            ->required(),
                            
                        Textarea::make('address')
                            ->label('Alamat')
                            ->required(),
                    ])->columns(2),

                Section::make('Status Pesanan')
                    ->schema([

                        Select::make('payment_status')
                        ->label('Status Pembayaran')
                        ->options([
                            OrderStatusService::PAYMENT_UNPAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_UNPAID),
                            OrderStatusService::PAYMENT_PAID => OrderStatusService::getPaymentStatusLabel(OrderStatusService::PAYMENT_PAID),
                        ])
                        ->required()
                        ->live(),

                        Select::make('status')
                        ->label('Status Pesanan')
                        ->options([
                            OrderStatusService::STATUS_PENDING => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_PENDING),
                            OrderStatusService::STATUS_PROCESSING => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_PROCESSING),
                            OrderStatusService::STATUS_SHIPPED => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_SHIPPED),
                            OrderStatusService::STATUS_COMPLETED => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_COMPLETED),
                            OrderStatusService::STATUS_CANCELLED => OrderStatusService::getStatusLabel(OrderStatusService::STATUS_CANCELLED),
                        ])
                        ->required()
                        ->live(),
                ])->columns(2),

                Section::make('Informasi Pengiriman')
                    ->schema([
                        Select::make('courier_id')
                            ->label('Layanan Pengiriman')
                            ->relationship('courier', 'code', fn ($query) => $query->where('is_active', true)) // Filter hanya kurir aktif
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $courier = Courier::find($state);
                                    $courierCode = $courier?->code;
                                    
                                    $costs = [
                                        'J&T' => 20000,
                                        'JNE' => 30000,
                                        'SiCepat' => 15000,
                                    ];
                                    
                                    $set('shipping_cost', $costs[$courierCode] ?? 0);
                                } else {
                                    $set('shipping_cost', 0);
                                }
                            }),

                        TextInput::make('shipping_cost')
                            ->label('Biaya Pengiriman')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled()
                            ->required(),
                    ]),


                Section::make('Rincian Pembayaran')
                    ->schema([

                        Select::make('promo_code_id')
                        ->label('Promo Code')
                        ->relationship('promoCode', 'code')
                        ->searchable()
                        ->preload()
                        ->live(),

                        TextInput::make('discount_amount')
                            ->label('Diskon')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled()
                            ->default(0),

                        TextInput::make('sub_total_amount')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled(),

                        TextInput::make('grand_total_amount')
                            ->label('Total')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('grand_total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        OrderStatusService::STATUS_PENDING => 'warning',
                        OrderStatusService::STATUS_PROCESSING => 'info',
                        OrderStatusService::STATUS_SHIPPED => 'primary',
                        OrderStatusService::STATUS_COMPLETED => 'success',
                        OrderStatusService::STATUS_CANCELLED => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => OrderStatusService::getStatusLabel($state)),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        OrderStatusService::PAYMENT_UNPAID => 'danger',
                        OrderStatusService::PAYMENT_PAID => 'success',
                    })
                    ->formatStateUsing(fn ($state) => OrderStatusService::getPaymentStatusLabel($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
