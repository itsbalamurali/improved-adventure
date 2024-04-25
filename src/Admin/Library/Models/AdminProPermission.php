<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProPermission extends Model
{
    // protected $table  = "administrators";

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'permission_name', 'status', 'display_order', 'display_group_id',
    ];

    public function ScopeActive($q): void
    {
        $q->where('status', 'Active');
    }

    public function group()
    {
        return $this->belongsTo(AdminProPermissionDisplayGroup::class, 'display_group_id', 'id');
    }

    public function permission()
    {
        return $this->belongsTo(AdminProPermissionDisplayGroup::class);
    }

    public function roles()
    {
        return $this->belongsToMany(AdminGroup::class, 'admin_group_permission', 'permission_id', 'group_id');
    }
}
