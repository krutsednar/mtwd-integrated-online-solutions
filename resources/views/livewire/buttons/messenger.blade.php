{{-- <x-filament::icon-button
    icon="heroicon-o-chat-bubble-left-right"
    href="{{ url('/messenger') }}"
    tag="a"
    label="MTWD Messenger {{ auth()->user()->getUnreadCount() }} "
/> --}}
<div>
<a
    href="{{ url('/messenger') }}"
    class="relative inline-flex items-center justify-center p-2 transition rounded-full hover:bg-gray-100"
    aria-label="MTWD Messenger"
    wire:poll.3s="updateCount"
>
    <x-filament::icon-button
        icon="heroicon-o-chat-bubble-left-right"
        class="w-6 h-6"
    />

    @if(auth()->user()->getUnreadCount() > 0)
        <span class="absolute inline-flex items-center w-10 h-5 font-bold text-red-500 bg-red-600 rounded-full justify-left text-s -top-1 -right-1 -left-2 dark:text-red-500">
            {{ auth()->user()->getUnreadCount() }}
        </span>
    @endif
</a>
{{-- <script>
    window.addEventListener('play-notification-sound', () => {
        const audio = new Audio('/sounds/notification.wav'); // Replace with your sound path
        audio.play();
    });
</script> --}}

<script>
    document.addEventListener("DOMContentLoaded", () => {
       @this.on('play-notification-sound', () => {
           new Audio("{{url('sounds/notification.mp3')}}").play();
       })
    });
</script>
</div>
