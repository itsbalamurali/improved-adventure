<?php
require_once(TPATH_CLASS . 'include_header.php');
if (!defined('ALLOWED_DOMAINS')) { exit; }

/**
 * Case-insensitive in_array() wrapper.
 *
 * @param  mixed $needle   Value to seek.
 * @param  array $haystack Array to seek in.
 *
 * @return bool
 */
function in_array_ci($needle, $haystack){
	return in_array(strtolower($needle), array_map('strtolower', $haystack));
}

function startsWithSGF($string, $startString) {
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function endsWithSGF($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function lengthCountSortSGF($a, $b) {
    return strlen($b) - strlen($a);
}

function isOnlyDigitsStrSGF($str){
	return preg_match('/^[0-9]+$/', $str);
}

function getTranslatedTextGT($originalText, $translation) {

    if (!empty($translation) && !empty($translation['text'])) {
        $translation['text'] = str_replace('<span class="notranslate">', "", $translation['text']);
        $translation['text'] = str_replace('</span>', "", $translation['text']);
        $translation['text'] = htmlspecialchars_decode(html_entity_decode($translation['text']), ENT_QUOTES);

        return $translation['text'];
    }

    return $originalText;
}

function getTextForTranslationGT($text_convert_en) {
    global $vProjectName;
    $replaceValuesArr = Array("#");

    $projectNamesArr = permuteGT("projectName");

    $replaceValuesArr = array_merge($replaceValuesArr, $projectNamesArr);

    if ($vProjectName != "") {
        $replaceValuesArr[] = $vProjectName;
    }

    $wordReplaceArr = array();
    foreach ($replaceValuesArr as $value_replaceChar) {

        $matches = array();
        preg_match_all("/(?<!\.w)" . $value_replaceChar . "\S*+/", $text_convert_en, $matches);
        // preg_match_all('/\#(.*?)\S#/', $text_convert_en , $matches);
        // preg_match_all("~#(.*?)#~", $text_convert_en , $matches);

        if (!empty($matches) && count($matches) > 0) {
            $dataArr = $matches[0];
            $subCount = 0;
            foreach ($dataArr as $value_tmpWord) {
                if (startsWithSGF($value_tmpWord, $value_replaceChar) && endsWithSGF($value_tmpWord, $value_replaceChar)) {
                    $subCount++;
                    continue;
                } else {
                    $str_tmp_1 = str_replace($value_replaceChar, "", $value_tmpWord);
                    if (!empty($str_tmp_1) && $str_tmp_1 != $value_replaceChar && $str_tmp_1 != $value_tmpWord) {
                        $value_tmpWord = str_replace($str_tmp_1, "", $value_tmpWord);
                    }

                    $dataArr[$subCount] = $value_tmpWord;
                    $subCount++;
                }
            }

            $dataArr_tmp = array();

            $wordReplaceSUBArrCount = 0;
            foreach ($dataArr as $match_words_value) {

                if (startsWithSGF($match_words_value, $value_replaceChar) && endsWithSGF($match_words_value, $value_replaceChar)) {
                    // check break characters

                    $wordReplaceArr_tmp = array();

                    preg_match_all("/#([^#]+)#/mis", $match_words_value, $wordReplaceArr_tmp);

                    if (!empty($wordReplaceArr_tmp) && count($wordReplaceArr_tmp) > 0) {
                        $IS_ADD_DEFAULT_VALUE = true;
                        foreach ($wordReplaceArr_tmp[0] as $match_words_value_tmp) {
                            if (empty($match_words_value_tmp)) {
                                continue;
                            }
                            $IS_ADD_DEFAULT_VALUE = false;
                            $dataArr_tmp[] = $match_words_value_tmp;
                        }
                        if ($IS_ADD_DEFAULT_VALUE) {
                            $dataArr_tmp[] = $match_words_value;
                        }
                    } else {
                        $dataArr_tmp[] = $match_words_value;
                    }

                    continue;
                } else {
                    $wordReplaceArr_tmp = array();

                    preg_match_all("~" . $value_replaceChar . "(.*?)" . $value_replaceChar . "~", $match_words_value, $wordReplaceArr_tmp);

                    if (!empty($wordReplaceArr_tmp) && count($wordReplaceArr_tmp) > 0) {
                        $IS_ADD_DEFAULT_VALUE = true;
                        foreach ($wordReplaceArr_tmp[0] as $match_words_value_tmp) {
                            if (empty($match_words_value_tmp)) {
                                continue;
                            }
                            $IS_ADD_DEFAULT_VALUE = false;
                            $dataArr_tmp[] = $match_words_value_tmp;
                        }
                        if ($IS_ADD_DEFAULT_VALUE) {
                            $dataArr_tmp[] = $match_words_value;
                        }
                    } else {
                        $dataArr_tmp[] = $match_words_value;
                    }
                }
            }


            //echo "<PRE>";
            
            usort($dataArr_tmp, 'lengthCountSortSGF');

            if(!empty($dataArr_tmp))
            {
                $wordReplaceArr[] = $dataArr_tmp;    
            }
        }
    }

    // echo "<pre>"; print_r($wordReplaceArr); exit;

    if (!empty($wordReplaceArr) && count($wordReplaceArr) > 0) {
        $isJsonStr = isJsonTextGT($text_convert_en);
        foreach ($wordReplaceArr as $wordReplaceSUBArr) {

            foreach ($wordReplaceSUBArr as $match_words_value) {

                $match_words_value_asciiStr = implode("", unpack("C*", $match_words_value));
                $text_convert_en = str_replace($match_words_value, $isJsonStr ? '<span class=\"notranslate\">@@' . $match_words_value_asciiStr . '@@</span>' : '<span class="notranslate">@@' . $match_words_value_asciiStr . '@@</span>', $text_convert_en);
                //$text_convert_en = str_replace($match_words_value, '<span class="notranslate">' . $match_words_value . '</span>', $text_convert_en);
            }

            foreach ($wordReplaceSUBArr as $match_words_value) {
                $match_words_value_asciiStr = implode("", unpack("C*", $match_words_value));
                $text_convert_en = str_replace('@@' . $match_words_value_asciiStr . '@@', $match_words_value, $text_convert_en);
            }
        }
    }

    return $text_convert_en;
}

function permuteGT($input) {
    $n = strlen($input);
    $permutations = array();
    // Number of permutations is 2^n 
    $max = 1 << $n;
    // Converting string to lower case 
    $input = strtolower($input);
    // Using all subsequences and permuting them 
    for ($i = 0; $i < $max; $i++) {
        $combination = $input;
        // If j-th bit is set, we convert  
        // it to upper case 
        for ($j = 0; $j < $n; $j++) {
            if ((($i >> $j) & 1) == 1)
                $combination[$j] = chr(ord($combination[$j]) - 32);
        }
        // Printing current combination 
        // echo $combination . " "; 
        $permutations[] = $combination;
    }
    return $permutations;
}

function isJsonTextGT($text_str) {
    return is_string($text_str) && is_array(json_decode($text_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function replaceWordGT($string, $searchStr, $replaceStr) {
    $string = preg_replace("/\b(?<!#)" . $searchStr . "\b/i", $replaceStr, $string);
    return $string;
}

function getProperTextGT($string) {
    preg_match_all("/# [a-zA-Z_]+ #/", $string, $matches);

    $replaceArr = array();
    foreach ($matches as $key => $value) {
        $string = str_replace($value, preg_replace('/\s+/', "", $value), $string);
    }

    return $string;
}

function isAssocArrGT(array $array) {
  return count(array_filter(array_keys($array), 'is_string')) > 0;
}

function generateDomainName() {
    // Function is being used to generate service domain
    if(strtoupper(basename($_SERVER["SCRIPT_FILENAME"])) == "INDEX.PHP"){        

        $file_path = TPATH_ROOT . '/webimages/script_files/domain_data.txt';
        
        $server_domain = API_SERVICE_DOMAIN;
        $DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        
        if(ip2long($HTTP_HOST)){
            echo "<h1> System can't run using an IP address. Kindly use domain name to browse website.";
            exit;
        }
        
        if(file_exists($file_path) == false){
            $domain_file = fopen($file_path, "w") or die("Unable to open file!");
            fwrite($domain_file, $server_domain.PHP_EOL);
            fclose($domain_file);
        }else{
            $contentOfFile = file_get_contents($file_path);
            
            $arr_contents = explode(PHP_EOL,$contentOfFile);
            $arr_contents = array_map('trim', $arr_contents);
            if(in_array($server_domain, $arr_contents) == false){
                $domain_file = fopen($file_path, "w") or die("Unable to open file!");
                fwrite($domain_file, $server_domain.PHP_EOL);
            }
            
            if(in_array($server_domain, $arr_contents) == false){
                $contentOfFile = "";
            }
            
            if(!empty($domain_file)) {
                if(!empty($contentOfFile)){
                    fwrite($domain_file, $contentOfFile);
                }
                fclose($domain_file);
            }
        }
    }
    return true;
}

function get_server_domain($host){
    $myhost = strtolower(trim($host));
    $count = substr_count($myhost, '.');
    
    if($count === 2){
        if(strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
    } else if($count > 2){
        $myhost = get_server_domain(explode('.', $myhost, 2)[1]);
    }
    
    return $myhost;
}

function securedEncryptGT($data) {    
    /* $str = pkcs5_pad_openssl_gt($data); 
    $str = $data; 
    $iv = $SECRET_IV_ENC;

    $encrypted = openssl_encrypt ($str, "AES-256-CBC", $SECRET_KEY_ENC, OPENSSL_ZERO_PADDING, $iv);
	
    $string = base64_encode($encrypted);
	
    return $string; */
	
    return base64_encode(openssl_encrypt($data . '', 'aes-256-cbc', ENC_KEY, OPENSSL_RAW_DATA, ENC_IV));
}

function pkcs5_pad_openssl_gt($text) {
    $size = openssl_cipher_iv_length('aes-256-cbc');
    $pad = $size - (strlen($text) % $size);
    return $text . str_repeat(chr($pad), $pad);
}
?>