<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminGroup extends Model
{
    // protected $table  = "administrators";

    public $timestamps = false;

    protected $primaryKey = 'iGroupId';

    protected $fillable = [
        'vGroup', 'eStatus',
    ];

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_group_permission', 'group_id', 'permission_id');
    }
}
