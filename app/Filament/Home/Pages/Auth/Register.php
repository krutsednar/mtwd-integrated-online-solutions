<?php

namespace App\Filament\Home\Pages\Auth;
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
                        $this->getEmailFormComponent(),
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
            ->required();
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
            ->label('Username (ex: ICTD-Kurt)');
    }
    protected function getSuffixFormComponent(): Component
    {
        return TextInput::make('suffix')
            ;
    }
    protected function getBirthdayFormComponent(): Component
    {
        return DatePicker::make('birthday')
            ->format('d/m/Y')
            ->required();
    }
    protected function getDivisionFormComponent(): Component
    {
        return Select::make('division')
            ->options([
                'OGM'     => 'Office of the General Manager',
                'OBOD'     => 'Office of the Board of Directors',
                'OAGM-TSO'  => 'Office of the Assistant General Manager for Technical Services and Operations',
                // 'OAGM-FA'  => 'Office of the Assistant General Manager for Finance and Administration',
                'AFD' => 'Administration and Finance Department',
                'TSOD'        => 'Technical Services and Operations Department',
                'CPPAD'     => 'Corporate Planning and Public Affairs Division',
                'ICSD'     => 'Internal Control and System Development Division',
                'LD'   => 'Legal Division',
                'ICTD'    => 'Information and Communication Technology Division',
                'HRD'    => 'Human Resource Department',
                'GSD'  => 'General Service Division',
                'PMMD'  => 'Property and Material Management Division',
                'ACTD'    => 'Accounting Division',
                'CSD'     => 'Customer Service Division',
                'COMMD'    => 'Commercial Division',
                'ED'   => 'Engineering Division',
                'COD'     => 'Construction Division',
                'EWRD'    => 'Environment and Water Resources Division',
                'PROD'    => 'Production Division',
                'PAMD'     => 'Pipeline and Appurtenances Maintenance Division',
                // 'WQS'    => 'Water Quality Section',
                // 'TAB'     => 'Treasury and Budget Section',
                // 'BAC'       => 'Bids and Awards Committee',
                // 'WHS'       => 'Warehouse Section',
            ])
            ->required();
    }
    protected function getMobileNumberFormComponent(): Component
    {
        return TextInput::make('mobile_number')
            // ->length(10)
            ->minLength(10)
            ->maxLength(10)
            ->numeric()
            ->prefix('+63')
            ->required();
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
