<div class="w-full">

            <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="w-full"
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

