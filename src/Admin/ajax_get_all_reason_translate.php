<?php



include_once '../common.php';

$englishText = $_POST['englishText'] ?? '';

    // fetch all lang from language_master table
$sql = "SELECT vCode,vLangCode FROM `language_master` where vCode!='".$default_lang."' ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

$sql = "SELECT vLangCode FROM language_master where eStatus='Active' AND eDefault = 'Yes'";
$data = $obj->MySQLSelect($sql);
$vGMapLangCode = $data[0]['vLangCode'] ?? 'en';

if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vCode = $db_master[$i]['vCode'];

        $vGmapCode = $db_master[$i]['vLangCode'];
        // $def_lang = strtolower($default_lang);

        $vValue = 'vTitle_'.$vCode;

        $url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishText).'&de=harshilmehta1982@gmail.com&langpair='.$vGMapLangCode.'|'.$vGmapCode;

        $result = file_get_contents($url);

        $finalResult = json_decode($result);

        $getText = $finalResult->responseData;

        $data['result'][] = [
            $vValue => $getText->translatedText,
        ];
    }
}

$output = [];
foreach ($data['result'] as $Result) {
    $output[key($Result)] = current($Result);
    if (null === $Result || '' === $Result) {
        $output[key($Result)] = $englishText;
    }
}

echo json_encode($output);

exit;
