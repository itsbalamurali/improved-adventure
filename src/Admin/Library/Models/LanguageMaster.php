<?php



namespace Kesk\Web\Admin\Library\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageMaster extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'iLanguageMasId';

    protected $table = 'language_master';

    protected $fillable = [
        'vTitle', 'vTitle_EN', 'vCode', 'vGMapLangCode', 'vLangCode', 'vCurrencyCode', 'vCurrencySymbol', 'iDispOrder', 'eStatus', 'eDefault', 'eDirectionCode',
    ];

    public function ScopeActive($query)
    {
        return $query->where('eStatus', 'Active');
    }

    public function labels()
    {
        return $this->hasMany(LanguageLabel::class, 'vCode', 'vCode');
    }
}
