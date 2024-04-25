<?php



namespace Kesk\Web\Common;

class StaticPage
{
    public function __construct() {}

    public function FetchStaticPage($id, $lang_code = 'EN')
    {
        global $obj, $vSystemDefaultLangCode, $cacheKeysArr, $LANG_OBJ;
        $data['meta_title'] = $data['meta_keyword'] = $data['meta_desc'] = $data['page_title'] = $data['page_desc'] = $data['vImage'] = $data['vImage1'] = $data['vImage2'] = '';
        if (\is_array($id)) {
            $implodeId = implode(',', $id);
            $data = $obj->MySQLSelect("SELECT * FROM pages WHERE iPageId IN ({$implodeId})");
            $staticDataArr = [];
            for ($g = 0; $g < \count($data); ++$g) {
                $pageData = [];
                $pageData['meta_title'] = $data[$g]['vTitle'];
                $pageData['vImage'] = $data[$g]['vImage'];
                $pageData['meta_keyword'] = $data[$g]['tMetaKeyword'];
                $pageData['meta_desc'] = $data[$g]['tMetaDescription'];
                $pageData['page_title'] = $data[$g]['vPageTitle_'.$lang_code];
                $pageData['page_desc'] = $data[$g]['tPageDesc_'.$lang_code];
                if (empty($pageData['page_title']) && empty($pageData['page_desc'])) {
                    if ('' !== $vSystemDefaultLangCode) {
                        $lang_code = $vSystemDefaultLangCode;
                    } else {
                        $lang_code = $LANG_OBJ->FetchDefaultLangData('vCode');
                    }
                    $pageData['page_title'] = $data[$g]['vPageTitle_'.$lang_code];
                    $pageData['page_desc'] = $data[$g]['tPageDesc_'.$lang_code];
                }
                $pageData['vImage1'] = $data[$g]['vImage1'];
                $pageData['vImage2'] = $data[$g]['vImage2'];
                $staticDataArr[$data[$g]['iPageId']] = $pageData;
            }

            return $staticDataArr;
        }
        if ('' !== $id) {
            $data = $this->getStaticPageData($id);
            if (\count($data) > 0) {
                $data['meta_title'] = $data[0]['vTitle'];
                $data['vImage'] = $data[0]['vImage'];
                $data['meta_keyword'] = $data[0]['tMetaKeyword'];
                $data['meta_desc'] = $data[0]['tMetaDescription'];
                $data['page_title'] = $data[0]['vPageTitle_'.$lang_code];
                $data['page_desc'] = $data[0]['tPageDesc_'.$lang_code];
                if (empty($data['page_title']) && empty($data['page_desc'])) {
                    if ('' !== $vSystemDefaultLangCode) {
                        $lang_code = $vSystemDefaultLangCode;
                    } else {
                        $lang_code = $LANG_OBJ->FetchDefaultLangData('vCode');
                    }
                    $data['page_title'] = $data[0]['vPageTitle_'.$lang_code];
                    $data['page_desc'] = $data[0]['tPageDesc_'.$lang_code];
                }
                $data['vImage1'] = $data[0]['vImage1'];
                $data['vImage2'] = $data[0]['vImage2'];
            }
        }

        return $data;
    }

    public function FetchSeoSetting($id)
    {
        global $obj;
        if ('' !== $id) {
            $q = 'SELECT * FROM seo_sections WHERE iId = '.$id;
            $data = $obj->MySQLSelect($q);
            if (\count($data) > 0) {
                $data['meta_title'] = $data[0]['vPagetitle'];
                $data['meta_keyword'] = $data[0]['vMetakeyword'];
                $data['meta_desc'] = $data[0]['tDescription'];
            }
        }

        return $data;
    }

    public function gethomeDataNew($vCode)
    {
        global $obj;
        if ('' !== $vCode) {
            $q = "SELECT * FROM homecontent WHERE vCode = '".$vCode."'";
            $data = $obj->MySQLSelect($q);
            if (\count($data) > 0) {
                $data['meta_title'] = $data[0]['vPagetitle'];
                $data['meta_keyword'] = '';
                $data['meta_desc'] = $data[0]['tDescription'];
            }
            if (empty($data)) {
                $q = "SELECT * FROM homecontent WHERE vCode = 'EN'";
                $data = $obj->MySQLSelect($q);
            }
        }

        return $data;
    }

    private function getStaticPageData($page_id)
    {
        global $obj, $oCache, $cacheKeysArr;
        $staticPageApcKey = md5($cacheKeysArr['pages']);
        $getStaticCacheData = $oCache->getData($staticPageApcKey);
        if (!empty($getStaticCacheData) && \count($getStaticCacheData) > 0) {
            $data = $getStaticCacheData;
        } else {
            $data = $obj->MySQLSelect('SELECT * FROM pages');
            $setPagesCacheData = $oCache->setData($staticPageApcKey, $data);
        }
        foreach ($data as $pageData) {
            if ($pageData['iPageId'] === $page_id) {
                $pageDataTmp[0] = $pageData;

                return $pageDataTmp;
            }
        }
    }
}
