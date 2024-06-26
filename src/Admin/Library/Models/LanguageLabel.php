<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageLabel extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'LanguageLabelId';

    protected $table = 'language_label';

    /*public $timestamps = false;
    protected $dates = ['updated_at'];*/

    protected $fillable = [
        'lPage_id', 'vCode', 'vLabel', 'vValue', 'vScreen', 'eScript', 'eStatus', 'eDeviceType', 'eAppType', 'updated_at',
    ];

    public function ScopeOnlyUpdated($query, $last_sync_data)
    {
        $date = $last_sync_data->vValue;

        return $query->where('updated_at', '>', $date);
    }

    /*function ScopeNotSync($query, $default_lang){
        $default_count = LanguageLabel::where('vCode', $default_lang)->get()->count();
        $query->groupBy('vCode');
        $query->having(\DB::raw('count(vCode)'), '<', $default_count);
    }*/

    public function language()
    {
        return $this->belongsTo(LanguageMaster::class, 'vCode', 'vCode');
    }
}
