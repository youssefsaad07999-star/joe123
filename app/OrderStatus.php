<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Refunded = 'refunded';

    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Refunded => 'Refunded',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',  // Yellow
            self::Processing => 'info',     // Light Blue
            self::Shipped => 'primary',  // Accent Blue
            self::Delivered => 'success',  // Green
            self::Cancelled => 'danger',     // Muted Gray (or 'danger' for Red)
            self::Refunded => 'gray',   // Red
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-s-clock',             // Solid clock
            self::Processing => 'heroicon-s-arrow-path',        // Solid arrow path
            self::Shipped => 'heroicon-s-truck',             // Solid truck
            self::Delivered => 'heroicon-s-check-circle',      // Solid check circle
            self::Cancelled => 'heroicon-s-x-circle',          // Solid x circle
            self::Refunded => 'heroicon-s-arrow-uturn-left',  // Solid return arrow
        };
    }
}
