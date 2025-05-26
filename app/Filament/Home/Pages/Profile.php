<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Profile extends EditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('avatars')
                            ->moveFiles()
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state) {
                                if (! $state instanceof TemporaryUploadedFile) return;

                                // Delete old avatar if exists
                                $oldAvatar = auth()->user()->avatar;
                                if ($oldAvatar && Storage::exists($oldAvatar)) {
                                    Storage::delete($oldAvatar);
                                }
                            }),

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
                    ])
                    ->columns(1),
            ]);
    }

    protected function getEmployeeNumberFormComponent(): Component
    {
        return TextInput::make('employee_number')
            ->label('Employee Number (format: xx-xxxx)')
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
            ->required(true)
            ->prefix('+63')
            ->maxLength(10);
    }
}
