<x-filament-tables::row>
    <x-filament-tables::cell><strong>TOTAL/AVERAGE:</strong></x-filament-tables::cell>

    {{-- Received Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalReceivedToday']) }}</strong>
    </x-filament-tables::cell>

    {{-- Accomplished Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalAccomplishedToday']) }}</strong>
    </x-filament-tables::cell>

    {{-- Ongoing Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalOngoingToday']) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Job Orders --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalJO']) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Accomplished --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalAccomplished']) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Ongoing --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totals['totalOngoing']) }}</strong>
    </x-filament-tables::cell>

    {{-- Average TAT --}}
    <x-filament-tables::cell>
        <strong>{{ $totals['avgTATReadable'] }}</strong>
    </x-filament-tables::cell>
</x-filament-tables::row>
