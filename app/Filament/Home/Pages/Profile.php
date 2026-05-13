<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\Division;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Redirect;
use Filament\Forms\Components\DatePicker;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Profile extends EditProfile
{
    protected ?string $heading = 'Edit My Profile';

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
                        $this->getEmailFormComponent()->required(false)->unique(
                            table: 'users',
                            column: 'email',
                            ignoreRecord: true),
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
            ->required()
            ->unique(table: 'users',
                column: 'employee_number',
                ignoreRecord: true);
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
        return TextInput::make('suffix');
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
            ->searchable()
            ->required();
    }
    protected function getMobileNumberFormComponent(): Component
    {
        return TextInput::make('mobile_number')
            // ->length(10)
            ->required(true)
            ->prefix('+63')
            ->maxLength(10)
            ->unique(table: 'users',
                column: 'mobile_number',
                ignoreRecord: true);
                }

    protected function afterSave(): void
    {
        $this->redirect(Filament::getCurrentPanel()->getUrl());
    }
}
