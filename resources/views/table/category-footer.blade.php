<x-filament-tables::row>
    <x-filament-tables::cell><strong>TOTAL/AVERAGE:</strong></x-filament-tables::cell>
    <x-filament-tables::cell>
        <strong>{{ number_format($totalJO) }}</strong>
    </x-filament-tables::cell>
    <x-filament-tables::cell>
        <strong>{{ number_format($totalAccomplished) }}</strong>
    </x-filament-tables::cell>
    <x-filament-tables::cell>
        <strong>{{ number_format($totalOngoing) }}</strong>
    </x-filament-tables::cell>
    <x-filament-tables::cell>
        <strong>{{ $avgTATReadable }}</strong>
    </x-filament-tables::cell>
</x-filament-tables::row>
