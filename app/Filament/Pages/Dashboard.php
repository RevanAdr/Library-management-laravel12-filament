<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\EntireReport;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Livewire\GeneralInfo;

class Dashboard extends BaseDashboard
{
   protected static ?int $navigationSort = 1;

   public function getColumns(): int | array
    {
        return [
            'md' => 4,
            'xl' => 5,
        ];
    }
}