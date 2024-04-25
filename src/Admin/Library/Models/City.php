<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iCityId';

    protected $table = 'city';

    protected $fillable = [
        'vCity', 'iCountryId', 'iStateId', 'eStatus',
    ];

    public function country(): void
    {
        $this->hasOne(Country::class, 'iCountryId', 'iCountryId');
    }

    public function state(): void
    {
        $this->hasOne(State::class, 'iStateId', 'iStateId');
    }
}
