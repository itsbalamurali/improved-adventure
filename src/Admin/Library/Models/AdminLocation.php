<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLocation extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'admin_locations';

    protected $fillable = [
        'admin_id', 'location_id',
    ];

    public function admins()
    {
        return $this->hasMany(Administrator::class, 'iAdminId', 'admin_id');
    }

    public function locations()
    {
        return $this->hasMany(LocationMaster::class, 'iLocationId', 'location_id')->adminLocations();
    }
}
