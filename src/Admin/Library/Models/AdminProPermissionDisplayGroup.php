<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProPermissionDisplayGroup extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'admin_pro_permission_display_groups';

    protected $fillable = [
        'name', 'status',
    ];

    public function sortByASC()
    {
        return Product::orderBy('display_order', 'ASC')->get();
    }

    public function subgroup()
    {
        return $this->hasMany(AdminProPermissionDisplaySubGroup::class, 'iGroupsId', 'id')->where('eStatus', 'Active')->orderBy('display_order');
    }

    public function permissions()
    {
        return $this->hasMany(AdminPermission::class, 'display_group_id', 'id');
    }
}
