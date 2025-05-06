<div>
@php
    $account = $getRecord->account ?? null;

    $mapUrl = $account && $account->latitude && $account->longtitude
        ? 'https://www.google.com/maps/dir/17.6223543,121.7214678/' .
          $account->latitude . ',' . $account->longtitude .
          '/@' . $account->latitude . ',' . $account->longtitude . ',20z'
        : null;
@endphp

{{-- @if ($mapUrl) --}}
    <div class="flex items-center space-x-2">
        @if ( $account->latitude)
            <a href="{{ 'https://www.google.com/maps/dir/17.6223543,121.7214678/' .
            $account->latitude . ',' . $account->longtitude .
            '/@' . $account->latitude . ',' . $account->longtitude . ',20z'}}" target="_blank" title="Open map in new tab">
            <x-heroicon-o-map-pin class="w-5 h-5 text-primary-600 hover:text-primary-800" />
            </a>
        @else
            <a href="#" title="Open map in new tab">
            <x-heroicon-o-map-pin class="w-5 h-5 text-primary-600 hover:text-primary-800" />
            </a>
        @endif


        <button
            x-data
            x-on:click="navigator.clipboard.writeText('{{ $mapUrl }}'); $dispatch('notify', { message: 'Link copied!' })"
            title="Copy map link"
        >
            <x-heroicon-o-clipboard class="w-5 h-5 text-gray-600 hover:text-gray-800" />
        </button>
    </div>
{{-- @endif --}}
</div>
