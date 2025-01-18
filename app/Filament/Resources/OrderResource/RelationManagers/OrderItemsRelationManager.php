<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product;
use App\Models\ProductSize;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderItems';

    protected static ?string $title = 'Item Pesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $productSizes = $product->sizes->pluck('size', 'id')->toArray();
                                $set('product_sizes', $productSizes);
                                $set('price', 0);
                                $set('subtotal', 0);
                            }
                        }
                    }),

                Forms\Components\Select::make('product_size_id')
                    ->label('Ukuran')
                    ->options(function (callable $get) {
                        return $get('product_sizes') ?? [];
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        if ($state) {
                            $productSize = ProductSize::find($state);
                            if ($productSize) {
                                $price = $productSize->price;
                                $quantity = $get('quantity') ?? 1;
                                $subtotal = $price * $quantity;
                                
                                $set('price', $price);
                                $set('subtotal', $subtotal);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $price = $get('price') ?? 0;
                        $subtotal = $price * $state;
                        $set('subtotal', $subtotal);
                    }),

                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),

                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('productPhoto.photo')
                    ->label('Foto Produk'),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk'),
                
                Tables\Columns\TextColumn::make('productSize.size')
                    ->label('Ukuran'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Total')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($data, $record) {
                        $this->hitungUlangTotal($record->order);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($data, $record) {
                        $this->hitungUlangTotal($record->order);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($data, $record) {
                        $this->hitungUlangTotal($record->order);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function hitungUlangTotal($order)
    {
        $subtotal = $order->orderItems->sum('subtotal');
        $order->update([
            'sub_total_amount' => $subtotal,
            'grand_total_amount' => $subtotal - ($order->discount_amount ?? 0) + $order->shipping_cost
        ]);
    }
}