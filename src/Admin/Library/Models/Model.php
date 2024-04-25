<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public $timestamps = false;

    protected $primaryKey = 'iModelId';

    protected $table = 'model';

    protected $fillable = [
        'iModelId', 'iMakeId', 'vTitle', 'eStatus',
    ];

    public function make(): void
    {
        $this->hasOne(Country::class, 'iMakeId', 'iMakeId');
    }
}
