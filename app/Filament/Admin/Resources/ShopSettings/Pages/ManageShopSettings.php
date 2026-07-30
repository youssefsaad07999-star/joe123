<?php

namespace App\Filament\Admin\Resources\ShopSettings\Pages;

use App\Filament\Admin\Resources\ShopSettings\ShopSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageShopSettings extends ManageRecords
{
    protected static string $resource = ShopSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
