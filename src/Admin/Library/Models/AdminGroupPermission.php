<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class AdminGroupPermission extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $table = 'admin_group_permission';

    protected $fillable = [
        'group_id', 'permission_id',
    ];

    public function role()
    {
        return $this->belongsTo(AdminGroup::class, 'group_id', 'iGroupId');
    }

    public function permission()
    {
        return $this->belongsTo(AdminPermission::class, 'permission_id', 'id');
    }
}
