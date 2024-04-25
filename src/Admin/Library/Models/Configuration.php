<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iSettingId';

    protected $table = 'configurations';

    protected $fillable = [
        'tDescription', 'vName', 'vValue', 'vOrder', 'eType', 'eStatus', 'tHelp', 'eInputType', 'tSelectVal', 'eAdminDisplay', 'eRequireField',
    ];
}
