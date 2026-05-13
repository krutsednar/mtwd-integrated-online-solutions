<div>
<x-filament-panels::page>

</x-filament-panels::page>
    <div x-data="{ tab: 'tab1' }">
        <x-filament::tabs label="Content tabs">
            <x-filament::tabs.item
            icon="fas-file-circle-check"
            @click="tab = 'tab1'" :alpine-active="'tab === \'tab1\''">
                <b>Job Orders Per Division</b>
            </x-filament::tabs.item>

            <x-filament::tabs.item
            icon="fas-file-circle-check"
            @click="tab = 'tab2'" :alpine-active="'tab === \'tab2\''">
                <b>Job Orders Per Category</b>
            </x-filament::tabs.item>

            <x-filament::tabs.item
            icon="fas-file-circle-check"
            @click="tab = 'tab3'" :alpine-active="'tab === \'tab3\''">
                <b>Job Orders Per Type</b>
            </x-filament::tabs.item>

        </x-filament::tabs>

        <div>
            <div x-show="tab === 'tab1'">
                @livewire('reports.jo-per-division')
            </div>

            <div x-show="tab === 'tab2'">
                @livewire('reports.jo-per-category')
            </div>

            <div x-show="tab === 'tab3'">
                @livewire('reports.jo-per-type')
            </div>
        </div>
    </div>
</div>
