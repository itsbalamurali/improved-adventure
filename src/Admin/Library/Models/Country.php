<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iCountryId';

    protected $table = 'country';

    protected $fillable = [
        'vCountry', 'vCountryCode', 'vCountryCodeISO_3', 'vPhoneCode', 'vTimeZone', 'vAlterTimeZone', 'vEmergencycode', 'eStatus', 'eUnit', 'fTax1', 'fTax2', 'vCurrency', 'eEnableToll',
    ];

    public function states(): void
    {
        $this->hasMany(State::class, 'iCountryId', 'iCountryId');
    }

    public function cities(): void
    {
        $this->hasMany(City::class, 'iCountryId', 'iCountryId');
    }
}
