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
                   Job Order was validated and verified by by {{ $this->user->where('jo_id', $record->verified_by)->value('first_name').' '.$this->user->where('jo_id', $record->dispatched_by)->value('last_name') }}
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
                   Job Order was accomplished by {{ $this->user->where('jo_id', $record->dispatched_by)->value('first_name').' '.$this->user->where('jo_id', $record->dispatched_by)->value('last_name') }} and was assigned to {{ implode(', ', $this->user->whereIn('employee_number', \App\Models\JoAccomplishment::where('jo_number', $record->jo_number)->pluck('jo_user'))->get()->pluck('full_name')->toArray()) }}

                </td>
            </tr>
            @endif
            @if($this->record->date_dispatched)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_dispatched)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was dispatched by {{ $this->user->where('jo_id', $record->dispatched_by)->value('first_name').' '.$this->user->where('jo_id', $record->dispatched_by)->value('last_name') }} and was assigned to {{ implode(', ', $this->user->whereIn('employee_number', \App\Models\JoDispatch::where('jo_number', $record->jo_number)->pluck('jo_user'))->get()->pluck('full_name')->toArray()) }}

                </td>
            </tr>
            @endif
             @if($this->record->date_received && $record->received_by)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_recieved)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order was received by {{ $this->user->where('jo_id', $record->received_by)->value('first_name').' '.$this->user->where('jo_id', $record->received_by)->value('last_name') }}
                </td>
            </tr>
            @endif
            @if($this->record->date_forwarded &&  $record->forwarded_by)
            <tr class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->date_forwarded)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                   Job Order forwarded to {{ $this->record->jocode->division->name}} by {{ $this->user->where('jo_id', $record->forwarded_by)->value('first_name').' '.$this->user->where('jo_id', $record->forwarded_by)->value('last_name') }}
                </td>
            </tr>
            @endif
            <tr class="bg-white dark:bg-gray-800">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                     {{ \Carbon\Carbon::parse($this->record->created_at)->format('F d, Y H:i:s') }}
                </th>
                <td class="px-6 py-4">
                    Report was requested by {{ $this->record->requested_by }} and was processed by {{ $this->user->where('jo_id', $record->processed_by)->value('first_name') ?? $this->user->where('employee_number', $record->processed_by)->value('first_name').' '.$this->user->where('jo_id', $record->processed_by)->value('last_name') ?? $this->user->where('employee_number', $record->processed_by)->value('last_name') }}
                </td>
            </tr>
        </tbody>
    </table>
</div>
