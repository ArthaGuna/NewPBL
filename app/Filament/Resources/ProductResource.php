<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Products';
    
    protected static ?string $pluralModelLabel = 'Produk';

    public static function getNavigationBadge(): ?string
    {
        return Product::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Fieldset::make('Details')
                ->schema( [
                    
                    TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $slug = Str::slug($state);
    
                        // Pastikan slug unik dengan menambahkan angka jika diperlukan
                        $slugCount = Product::where('slug', $slug)->count();
                        if ($slugCount > 0) {
                            $slug .= '-' . ($slugCount + 1); // Menambahkan angka di akhir slug
                        }
    
                        $set('slug', $slug);
                    }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->disabled(),
                    
                    FileUpload::make('thumbnail')
                    ->image()
                    ->required(),

                    Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
    
                ]),
                Fieldset::make('Additional')
                ->schema( [
                    Repeater::make('photos')
                    ->relationship('photos')
                    ->schema ([
                        FileUpload::make('photo')
                        ->required(),
                        ]),

                    Repeater::make('sizes')
                    ->relationship('sizes')
                    ->schema ([
                        TextInput::make('size')
                        ->required(),

                        TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('IDR'),
                    ]),
  
                ]),
                Fieldset::make('Additional')
                ->schema( [
                    
                    TextArea::make('about')
                    ->required()
                    ->maxLength(255),
                    
                    // Select::make('is_popular')  
                    // ->label('Is Popular')
                    // ->options ([
                    //     true => 'Popular',
                    //     false => 'Not Popular',
                    // ])
                    // ->required(),

                  

                    TextInput::make('stock')
                    ->required()    
                    ->numeric()
                    ->prefix('Qty'),
  
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('category.name'),

                ImageColumn::make('thumbnail'),

                // IconColumn::make('is_popular')
                // ->label('Is Popular')
                // ->boolean()
                // ->trueColor('success')
                // ->falseColor('danger')
                // ->trueIcon('heroicon-o-check-circle')
                // ->falseIcon('heroicon-o-x-circle')
                // ->label('Popular'),

                TextColumn::make('sizes')
                ->label('Available Sizes') // Label untuk kolom ukuran
                ->getStateUsing(function (Product $record) {
                    // Mengambil ukuran produk dan menggabungkannya dengan koma
                    return $record->sizes->pluck('size')->join(', ');
                })
            ])
            ->filters([
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
