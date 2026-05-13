<x-filament-tables::row>
    <x-filament-tables::cell><strong>TOTAL/AVERAGE:</strong></x-filament-tables::cell>

    {{-- Received Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalReceivedToday) }}</strong>
    </x-filament-tables::cell>

    {{-- Accomplished Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalAccomplishedToday) }}</strong>
    </x-filament-tables::cell>

    {{-- Ongoing Today --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalOngoingToday) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Job Orders --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalJO) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Accomplished --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalAccomplished) }}</strong>
    </x-filament-tables::cell>

    {{-- Total Ongoing --}}
    <x-filament-tables::cell>
        <strong>{{ number_format($totalOngoing) }}</strong>
    </x-filament-tables::cell>

    {{-- Average TAT --}}
    <x-filament-tables::cell>
        <strong>{{ $avgTATReadable }}</strong>
    </x-filament-tables::cell>
</x-filament-tables::row>
