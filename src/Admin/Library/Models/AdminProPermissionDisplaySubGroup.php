<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProPermissionDisplaySubGroup extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'admin_pro_permission_display_sub_groups';

    protected $fillable = [
        'name', 'status',
    ];

    public function permission()
    {
        return $this->hasMany(AdminProPermission::class, 'display_sub_group_id', 'id')->where('status', 'Active')->orderBy('display_order');
    }
}
