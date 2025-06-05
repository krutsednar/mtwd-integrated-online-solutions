<?php

namespace App\Filament\Home\Pages\Auth;
use App\Models\Division;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.home.pages.auth.register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmployeeNumberFormComponent(),
                        $this->getFirstNameFormComponent(),
                        $this->getMiddleNameFormComponent(),
                        $this->getLastNameFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getSuffixFormComponent(),
                        $this->getBirthdayFormComponent(),
                        $this->getDivisionFormComponent(),
                        $this->getMobileNumberFormComponent(),
                        $this->getEmailFormComponent()->unique(column: 'email'),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getIsApprovedFormComponent(),

                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmployeeNumberFormComponent(): Component
    {
        return TextInput::make('employee_number')
            ->label('Employee Number (format: xx-xxxx)')
            ->required()
            ->unique(column: 'employee_number');
    }

    protected function getFirstNameFormComponent(): Component
    {
        return TextInput::make('first_name')
            ->required();
    }
    protected function getMiddleNameFormComponent(): Component
    {
        return TextInput::make('middle_name')
            ->required();
    }
    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->required();
    }
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->required()
            ->label('Nickname (ex: ICTD-Kurt)');
    }
    protected function getSuffixFormComponent(): Component
    {
        return TextInput::make('suffix')
            ;
    }
    protected function getBirthdayFormComponent(): Component
    {
        return DatePicker::make('birthday')
            ->displayFormat('F d, Y')
            ->native(false)
            ->required();
    }
    protected function getDivisionFormComponent(): Component
    {
        return Select::make('division_id')
            ->label('Division')
            ->options(function () {
                return Division::orderBy('name')->pluck('name', 'code')->toArray();
            })
            ->required();
    }
    protected function getMobileNumberFormComponent(): Component
    {
        return TextInput::make('mobile_number')
            // ->length(10)
            ->required(true)
            ->prefix('+63')
            ->maxLength(10)
            ->unique(column: 'mobile_number');
    }
    protected function getIsApprovedFormComponent(): Component
    {
        return Toggle::make('is_approved')
            ->required()
            ->hidden();
    }
    // protected function getEmployeeNumberFormComponent(): Component
    // {
    //     return TextInput::make('employee_number')
    //         ->required();
    // }
}
