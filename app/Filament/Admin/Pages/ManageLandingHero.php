<?php

namespace App\Filament\Admin\Pages;

use App\Models\ShopSetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class ManageLandingHero extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static UnitEnum|string|null $navigationGroup = 'Site Content';

    protected static ?string $title = 'Landing Page Hero';

    protected string $view = 'filament.admin.pages.manage-landing-hero';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = ShopSetting::where('key', 'landing_hero_image')->first();

        $this->form->fill([
            'landing_hero_image' => $setting?->value,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('landing_hero_image')
                    ->label('Hero Banner Asset')
                    ->disk('public')
                    ->directory('settings/landing')
                    ->image()
                    ->imageEditor()
                    ->visibility('public')
                    ->required()
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                        $manager = new ImageManager(new Driver);
                        $fileName = 'settings/landing/'.uniqid().'.webp';

                        $compressedImage = $manager->decode($file->getRealPath())
                            ->scale(width: 1200)
                            ->encodeUsingFileExtension('webp', quality: 90);

                        Storage::disk('public')->put($fileName, (string) $compressedImage);

                        return $fileName;
                    }),
            ])
            ->statePath('data');

    }

    public function save(): void
    {
        $data = $this->form->getState();

        ShopSetting::updateOrCreate(
            ['key' => 'landing_hero_image'],
            [
                'value' => $data['landing_hero_image'],
                'type' => 'string',
            ]
        );

        Notification::make()
            ->success()
            ->title('Hero Banner Updated Successfully')
            ->send();
    }
}
