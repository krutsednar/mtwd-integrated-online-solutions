<div class="relative overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    TIMESTAMPS
                </th>
                <th scope="col" class="px-6 py-3">
                    TRANSACTION DETAILS
                </th>
            </tr>
        </thead>
        <tbody>
            @if($this->record->date_verified && $this->record->verified_by)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_verified)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was validated and verified by {{ $this->verifiedByName ?? '' }}
                </td>
            </tr>
            @endif

             @if($this->record->date_returned)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_returned)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order is for verification by PACD.

                </td>
            </tr>
            @endif
            @if($this->record->date_accomplished)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_accomplished)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was accomplished by {{ $this->dispatchedByName ?? '' }} and was assigned to {{ $this->accomplishedByNames }}

                </td>
            </tr>
            @endif
            @if($this->record->date_dispatched)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_dispatched)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was dispatched by {{ $this->dispatchedByName ?? '' }} and was assigned to {{ $this->dispatchedByNames }}

                </td>
            </tr>
            @endif
             @if($this->record->date_received && $this->record->received_by)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_received)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was received by {{ $this->receivedByName ?? '' }}
                </td>
            </tr>
            @endif
            @if($this->record->date_forwarded && $this->record->forwarded_by)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_forwarded)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order forwarded to {{ $this->record->jobOrderCode?->division?->name ?? 'N/A' }} by {{ $this->forwardedByName ?? '' }}
                </td>
            </tr>
            @endif
            <tr class="bg-white dark:bg-gray-800">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->created_at)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                    Report was requested by {{ $this->record->requested_by }} and was processed by {{ $this->processedByName ?? '' }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
