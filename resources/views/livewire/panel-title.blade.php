<div class="text-sm text-center">
    <div style="display: flex;  justify-content: center;">
        <img width="80px" class="items-center justify-center text-centercent" src="{{ asset('images/mtwdlogo.png') }}" />
    </div>

    @if($panelId == 'MOJO')
        <span class="font-bold size-xl">ONLINE JOB ORDER</span>
    @elseif($panelId == 'admin')
        <span class="font-bold font-xl">ADMIN PORTAL</span>
    @elseif($panelId == 'home')
        <span class="font-bold font-xl">HOME</span>
        @elseif($panelId == 'executive')
        <span class="font-bold size-xl">EXECUTIVE DASHBOARD</span>
        @elseif($panelId == 'PFIS')
        <span class="font-bold size-xl">PRODUCTION FACILITIES<br>INFORMATION SYSTEM</span>
        @elseif($panelId == 'HRIS')
        <span class="font-bold size-xl">HUMAN RESOURCES<br>INFORMATION SYSTEM</span>
        @elseif($panelId == 'MCIS')
        <span class="font-bold size-xl">CUSTOMER INFORMATION SYSTEM</span>
        @elseif($panelId == 'MOCA')
        <span class="font-bold size-xl">ONLINE CAREER APPLICATION</span>
    @endif
    <div style="margin-top: 10px;" class="text-sm text-center">Welcome, <b>{{ auth()->user()->name}}</b></div>
</div>
