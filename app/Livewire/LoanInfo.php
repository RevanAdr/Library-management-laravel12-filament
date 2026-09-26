<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;

class LoanInfo extends ChartWidget
{
    protected ?string $heading = 'Loan Info';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
