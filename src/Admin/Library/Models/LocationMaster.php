<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class LocationMaster extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iLocationId';

    protected $table = 'location_master';

    protected $fillable = [
        'iLocationId', 'iCountryId', 'vLocationName', 'tLatitude', 'tLongitude', 'eStatus', 'eFor',
    ];

    public function admins()
    {
        return $this->belongsToMany(Administrator::class, 'admin_locations', 'location_id', 'admin_id');
    }

    public function ScopeAdminLocations($q): void
    {
        $q->where('eFor', 'VehicleType')->active();
    }

    public function ScopeActive($q): void
    {
        $q->where('eStatus', 'Active');
    }
}
