<?php

namespace App\Livewire;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Loan;
use App\Models\Book;


class GeneralInfo extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('User Registered', User::count()),
            Stat::make('New Loans', Loan::count()),
            Stat::make('Book', Book::count()),
        ];
    }
}
