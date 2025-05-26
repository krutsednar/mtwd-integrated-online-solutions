<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rupadana\ApiService\Contracts\HasAllowedSorts;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;

class OnlineJobOrder extends Model implements HasAllowedFilters

{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'date_forwarded',
        'date_received',
        'date_dispatched',
        'date_accomplished',
        'date_verified',
    ];

    protected $fillable = [
        'jo_number',
        'date_requested',
        'account_number',
        'registered_name',
        'meter_number',
        'job_order_code',
        'address',
        'town',
        'barangay',
        'requested_by',
        'contact_number',
        'email',
        'mode_received',
        'remarks',
        'processed_by',
        'status',
        'is_online',
        'lat',
        'lng',
        'location',
        'division_concerned',
        'date_forwarded',
        'forwarded_by',
        'date_received',
        'received_by',
        'dispatched_by',
        'division_received_by',
        'date_dispatched',
        'date_accomplished',
        'actions_taken',
        'accomplishment_processed_by',
        'recommendations',
        'field_findings',
        'acknowledge_by',
        'verified_by',
        'date_verified',
    ];

    protected $appends = [
        'location',
    ];


    public function jocode()
    {
        return $this->belongsTo(JobOrderCode::class, 'job_order_code', 'code');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'accmasterlist', 'account_number');
    }

    public static function getAllowedFilters(): array
    {
        return [
            'created_at',
            'jo_number',
            'is_online',
        ];
    }

    // public function accounts()
    // {
    //     return $this->hasMany(Account::class, 'accmasterlist', 'account_number');

    // }

    /**
     * ADD THE FOLLOWING METHODS TO YOUR Account MODEL
     *
     * The 'latitude' and 'longtitude' attributes should exist as fields in your table schema,
     * holding standard decimal latitude and longitude coordinates.
     *
     * The 'location' attribute should NOT exist in your table schema, rather it is a computed attribute,
     * which you will use as the field name for your Filament Google Maps form fields and table columns.
     *
     * You may of course strip all comments, if you don't feel verbose.
     */

    /**
    * Returns the 'latitude' and 'longtitude' attributes as the computed 'location' attribute,
    * as a standard Google Maps style Point array with 'lat' and 'lng' attributes.
    *
    * Used by the Filament Google Maps package.
    *
    * Requires the 'location' attribute be included in this model's $fillable array.
    *
    * @return array
    */

    public function getLocationAttribute(): array
    {
        return [
            "lat" => (float)$this->lat,
            "lng" => (float)$this->lng,
        ];
    }

    /**
    * Takes a Google style Point array of 'lat' and 'lng' values and assigns them to the
    * 'latitude' and 'longtitude' attributes on this model.
    *
    * Used by the Filament Google Maps package.
    *
    * Requires the 'location' attribute be included in this model's $fillable array.
    *
    * @param ?array $location
    * @return void
    */
    public function setLocationAttribute(?array $location): void
    {
        if (is_array($location))
        {
            $this->attributes['lat'] = $location['lat'];
            $this->attributes['lng'] = $location['lng'];
            unset($this->attributes['location']);
        }
    }

    /**
     * Get the lat and lng attribute/field names used on this table
     *
     * Used by the Filament Google Maps package.
     *
     * @return string[]
     */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'lng',
        ];
    }

   /**
    * Get the name of the computed location attribute
    *
    * Used by the Filament Google Maps package.
    *
    * @return string
    */
    public static function getComputedLocation(): string
    {
        return 'location';
    }

}
