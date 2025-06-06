<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Namu\WireChat\Traits\Chatable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use LogsActivity;
    use Chatable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $dates = [
        'birthday',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_number',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthday',
        'division_id',
        'email',
        'mobile_number',
        'address',
        'password',
        'avatar',
        'is_approved',
        'jo_id',
        'prod_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
        // return $this->hasVerifiedEmail() && $this->is_approved;
        if ($panel->getId() === 'executive') {
            return $this->hasRole('Executive') && $this->is_approved;
        }
        return $this->is_approved;
    }

    public function canCreateChats(): bool
    {
        // return $this->hasVerifiedEmail() && $this->is_approved;
        return  $this->is_approved;
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->is_approved = false;
        });
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'code');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

}
