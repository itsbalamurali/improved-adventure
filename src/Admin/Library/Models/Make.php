<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iMakeId';

    protected $table = 'make';

    protected $fillable = [
        'vMake', 'eStatus',
    ];

    public function models(): void
    {
        $this->hasMany(Country::class, 'iMakeId', 'iMakeId');
    }
}
