<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iStateId';

    protected $table = 'state';

    protected $fillable = [
        'iCountryId', 'vStateCode', 'vState', 'eStatus',
    ];

    public function country(): void
    {
        $this->hasOne(Country::class, 'iCountryId', 'iCountryId');
    }

    public function cities(): void
    {
        $this->hasMany(City::class, 'iStateId', 'iStateId');
    }
}
