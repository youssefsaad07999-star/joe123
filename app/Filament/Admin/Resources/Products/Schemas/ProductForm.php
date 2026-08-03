<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        $updateSkuClosure = function (Get $get, Set $set) {
            $colorId = $get('color_id');
            $sizeId = $get('size_id');

            // Extract 3-letter uppercase prefix for Color
            $colorPrefix = '';
            if ($colorId) {
                $color = Color::find($colorId);
                $colorPrefix = $color ? strtoupper(substr($color->name, 0, 3)) : '';
            }

            // Extract 3-letter uppercase prefix for Size
            $sizePrefix = '';
            if ($sizeId) {
                $size = Size::find($sizeId);
                $sizePrefix = $size ? strtoupper(substr($size->name, 0, 3)) : '';
            }

            // Only generate SKU if at least one attribute is selected
            if ($colorPrefix || $sizePrefix) {
                $set('sku', "SKU-{$colorPrefix}-{$sizePrefix}");
            }
        };

        return $schema
            ->components([

                // ── Left/Main Workspace (Fills 2/3 width) ──
                Group::make()
                    ->schema([
                        Tabs::make('Product Dynamic Control Workspace')
                            ->persistTabInQueryString() // Keeps the admin on the same tab on validation failure/refresh
                            ->tabs([

                                // TAB 1: Core Specifications
                                Tabs\Tab::make('General Identity')
                                    ->icon('heroicon-m-document-text')
                                    ->schema([
                                        Section::make('Core Specifications')
                                            ->description('Define basic storefront search indexes, tags, pricing, and tax hierarchies.')
                                            ->schema([
                                                Grid::make(2)
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->label('Product Name')
                                                            ->placeholder('e.g., Premium Cotton Hoodie')
                                                            ->required()
                                                            ->live(debounce: 500)
                                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),

                                                        TextInput::make('slug')
                                                            ->label('URL Slug Path')
                                                            ->placeholder('e.g., premium-cotton-hoodie')
                                                            ->required()
                                                            ->unique(ignoreRecord: true)
                                                            ->alphaDash()
                                                            ->prefix('/products/'),
                                                    ]),

                                                RichEditor::make('description')
                                                    ->label('Product Marketing Copy')
                                                    ->placeholder('Write compelling product copy for customer conversions...')
                                                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                                                    ->columnSpanFull(),

                                                Grid::make(3)
                                                    ->schema([
                                                        TextInput::make('base_price')
                                                            ->label('Base Retail Price')
                                                            ->numeric()
                                                            ->prefix('$')
                                                            ->placeholder('0.00')
                                                            ->required(),

                                                        Select::make('category_id')
                                                            ->label('Subcategory Assignment')
                                                            ->options(fn () => Category::where('depth', 'subcategory')
                                                                ->with('parent.parent')
                                                                ->get()
                                                                ->mapWithKeys(fn ($sub) => [
                                                                    $sub->id => "{$sub->parent->parent->name} / {$sub->parent->name} / {$sub->name}",
                                                                ])
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->required(),

                                                        Select::make('brand_id')
                                                            ->label('Brand Partner')
                                                            ->relationship('brand', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable(),
                                                    ]),

                                                Grid::make(2)
                                                    ->schema([
                                                        Select::make('fit_id')
                                                            ->label('Sizing Blueprint Fit')
                                                            ->relationship('fit', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->nullable()
                                                            ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name)),
                                                    ]),
                                            ]),
                                    ]),

                                // TAB 2: Active Inventory Variants
                                // Tabs\Tab::make('Inventory Matrix')
                                //     ->icon('heroicon-m-squares-plus')
                                //     ->badge(fn (?Product $record) => $record?->variants()->count())
                                //     ->schema([
                                //         Section::make('Active Stock Keeping Variants')
                                //             ->description('Generate individual combinations of color and size attributes to handle detached price modifiers and physical stock.')
                                //             ->schema([
                                //                 Repeater::make('variants')
                                //                     ->relationship()
                                //                     ->schema([

                                //                         Select::make('color_id')
                                //                             ->label('Color Attribute')
                                //                             ->relationship('color', 'name')
                                //                             ->required()
                                //                             ->live()
                                //                             ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name))
                                //                             ->afterStateUpdated($updateSkuClosure),

                                //                         Select::make('size_id')
                                //                             ->label('Size Attribute')
                                //                             ->relationship('size', 'name')
                                //                             ->required()
                                //                             ->live() // Essential: Tells Filament to instantly send changes back to the server
                                //                             ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name)) // Capitalizes size options too
                                //                             ->afterStateUpdated($updateSkuClosure),

                                //                         TextInput::make('sku')
                                //                             ->label('Unique SKU')
                                //                             ->placeholder('AUTO-GENERATED')
                                //                             ->required()
                                //                             ->unique(ignoreRecord: true),

                                //                         TextInput::make('stock_quantity')
                                //                             ->label('Physical Stock')
                                //                             ->numeric()
                                //                             ->minValue(0)
                                //                             ->default(0)
                                //                             ->required(),

                                //                         TextInput::make('price_override')
                                //                             ->label('Price Override')
                                //                             ->prefix('$')
                                //                             ->placeholder('Using Base')
                                //                             ->nullable(),

                                //                         Toggle::make('is_active')
                                //                             ->label('Live')
                                //                             ->default(true)
                                //                             ->inline(false),
                                //                     ])
                                //                     ->columns(3)
                                //                     ->reorderable(false)
                                //                     ->cloneable()
                                //                     ->addActionLabel('Add Variant Configuration Row')
                                //                     ->columnSpanFull(),
                                //             ]),
                                //     ]),

                                // TAB 3: Product Gallery & Image Processing
                                Tabs\Tab::make('Media Assets')
                                    ->icon('heroicon-m-photo')
                                    ->badge(fn (?Product $record) => $record?->images()->count())
                                    ->schema([
                                        Section::make('Product Gallery & Color Swapping Co-ordination')
                                            ->description('Upload media files. Assign variations to their respective colors to feed localized frontend media arrays.')
                                            ->schema([
                                                Repeater::make('images')
                                                    ->relationship('images')
                                                    ->schema([
                                                        FileUpload::make('image_path')
                                                            ->label('Source Asset')
                                                            ->disk('public')
                                                            ->directory('products/gallery')
                                                            ->image()
                                                            ->imageEditor()
                                                            ->visibility('public')
                                                            ->required()
                                                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                                                $manager = new ImageManager(new Driver);
                                                                $fileName = 'products/gallery/'.uniqid().'.webp';

                                                                $compressedImage = $manager->decode($file->getRealPath())
                                                                    ->scale(width: 1200)
                                                                    ->encodeUsingFileExtension('webp', quality: 90);

                                                                Storage::disk('public')->put($fileName, (string) $compressedImage);

                                                                return $fileName;
                                                            }),

                                                        Select::make('color_id')
                                                            ->label('Color Context Boundary')
                                                            ->allowHtml()
                                                            ->placeholder('Global Asset (All Colors)')
                                                            ->options(function ($livewire): array {
                                                                // 1. Retrieve the parent Product record from the Livewire page component
                                                                $product = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

                                                                // 2. Return empty if on the Create Product page or if product isn't saved yet
                                                                if (! $product instanceof Product) {
                                                                    return [];
                                                                }

                                                                // 3. Fetch unique colors assigned to this product's existing database variants
                                                                return $product->variants()
                                                                    ->whereNotNull('color_id')
                                                                    ->with('color')
                                                                    ->get()
                                                                    ->pluck('color')
                                                                    ->filter()
                                                                    ->unique('id')
                                                                    ->mapWithKeys(fn (Color $color) => [
                                                                        $color->id => '
                                                                            <div class="flex items-center gap-2">
                                                                                <span class="w-3.5 h-3.5 rounded-full border border-gray-300 dark:border-gray-600 shadow-sm" style="background-color: '.($color->hex_code ?? '#cccccc').'"></span>
                                                                                <span>'.ucfirst($color->name).'</span>
                                                                            </div>
                                                                        ',
                                                                    ])
                                                                    ->toArray();
                                                            })
                                                            ->searchable()
                                                            ->nullable(),

                                                        TextEntry::make('color_preview')
                                                            ->hiddenLabel()
                                                            ->state(function (Get $get) {
                                                                // Use $get to fetch the color_id specifically for this repeater row
                                                                $colorId = $get('color_id');

                                                                // Fetch color data
                                                                $color = $colorId ? Color::find($colorId) : null;

                                                                if (! $color) {
                                                                    return '';
                                                                }

                                                                $hex = $color?->hex_code ?? '#ccc';

                                                                return new HtmlString(
                                                                    "<div style='display: flex; justify-content: center; margin-top: 8px;'>"
                                                                    ."<div style='width: 28px; height: 28px; border-radius: 50%; background: {$hex}; border: 3px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.15);'></div>"
                                                                    .'</div>'
                                                                );
                                                            }),
                                                        Toggle::make('is_primary')
                                                            ->label('Primary Listing Thumbnail')
                                                            ->helperText('Enforce exclusively across entire gallery.')
                                                            ->live()
                                                            ->afterStateUpdated(function ($state, $component) {
                                                                if (! $state) {
                                                                    return;
                                                                }

                                                                $livewire = $component->getLivewire();
                                                                $rowStatePath = $component->getContainer()->getStatePath();

                                                                $pathSegments = explode('.', $rowStatePath);
                                                                $currentRowKey = array_pop($pathSegments);
                                                                $repeaterPath = implode('.', $pathSegments);

                                                                $repeaterState = data_get($livewire, $repeaterPath);

                                                                if (is_array($repeaterState)) {
                                                                    foreach ($repeaterState as $key => $item) {
                                                                        if ($key !== $currentRowKey) {
                                                                            $repeaterState[$key]['is_primary'] = false;
                                                                        }
                                                                    }
                                                                    data_set($livewire, $repeaterPath, $repeaterState);
                                                                }
                                                            }),
                                                    ])
                                                    ->grid(3)
                                                    ->defaultItems(0)
                                                    ->addActionLabel('Upload Gallery Image Component')
                                                    ->collapsible()
                                                    ->itemLabel(fn (array $state): ?string => (isset($state['color_id']) ? '🎨 Bound to Variant Color' : '🌐 General Collection Image').
                                                        (! empty($state['is_primary']) ? ' ★ [MAIN COVER]' : '')
                                                    ),
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpan(2),

                // ── Right Sidebar Metadata Panel (Fills 1/3 width) ──
                Group::make()
                    ->schema([
                        Section::make('Storefront Lifecycle')
                            ->icon('heroicon-m-bolt')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Visible on Live Storefront')
                                    ->helperText('Instantly toggle indexing and lookup visibilities on client applications.')
                                    ->default(true),
                            ]),

                        Section::make('Live Metrics Summary')
                            ->description('Aggregated system evaluation details for this database entry.')
                            ->icon('heroicon-m-chart-bar')
                            ->schema([
                                TextEntry::make('total_variants')
                                    ->label('Variant Options Configured')
                                    ->state(fn (?Product $record) => $record ? "{$record->variants->count()} choices" : '—'),

                                TextEntry::make('total_stock')
                                    ->label('Total Accumulated Units')
                                    ->state(fn (?Product $record) => $record ? number_format($record->variants->sum('stock_quantity')).' items' : '—'),
                            ])
                            ->hiddenOn('create'),
                    ])->columnSpan(1),

            ])->columns(3);
    }
}
