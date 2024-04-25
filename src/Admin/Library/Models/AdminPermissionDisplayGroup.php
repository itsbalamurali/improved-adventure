<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPermissionDisplayGroup extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'admin_permission_display_groups';

    protected $fillable = [
        'name', 'status',
    ];

    public function permissions()
    {
        return $this->hasMany(AdminPermission::class, 'display_group_id', 'id');
    }
}
