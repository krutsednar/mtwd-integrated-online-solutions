<div class="items-center justify-center w-full p-2">


            <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="w-full p-4"
            >
                @csrf


                 <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-left-on-rectangle"
                    icon-alias="panels::widgets.account.logout-button"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                >
                    {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                </x-filament::button>
            </form>
</div>

