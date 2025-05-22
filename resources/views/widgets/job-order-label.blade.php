{{-- <div class="flex items-center space-x-2">
    @if($jobOrderIcon)
        <x-icon name="{{ $jobOrderIcon }}" class="w-5 h-5 text-primary-500" />
    @endif
    <span class="text-sm font-medium text-gray-700">
        {{ $jobOrderAccount }} ({{ $jobOrderDescription }})
    </span>
</div> --}}

JO Ref. No.: {{ $jobOrderCode }}
Account Number: {{ $jobOrderAccount }}
Job Order: {{ $jobOrderDescription }}
Status: {{ $jobOrderStatus }}
