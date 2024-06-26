<?php



    define('CKFINDER_LICENCE_NAME', 'ckfinder_licence');
    define('CKFINDER_LICENCE_KEY', '*D?F-*1**-C**R-*C**-*5**-Q*V*-2**H');

// Added By HJ On 06-11-2019 For Solved Issue 442 Of Sheet Start
    $basePath = $baseUrl = '';
    if (isset($_SERVER['SCRIPT_FILENAME'])) {
        $explode = explode('/', $_SERVER['SCRIPT_FILENAME']);
        for ($g = 0; $g < count($explode); ++$g) {
            if ('assets' === $explode[$g] || 'admin' === $explode[$g]) {
                break;
            }
            $basePath .= $explode[$g] . '/';
        }
    }
    if (isset($_SERVER['SCRIPT_URI'])) {
        $explodeUrl = explode('/', $_SERVER['SCRIPT_URI']);
        for ($u = 0; $u < count($explodeUrl); ++$u) {
            if ('assets' === $explodeUrl[$u] || 'admin' === $explodeUrl[$u]) {
                break;
            }
            $baseUrl .= $explodeUrl[$u] . '/';
        }
    }
    if (isset($_SERVER['HTTP_REFERER']) && '' !== $_SERVER['HTTP_REFERER'] && '' === $baseUrl) {
        $explodeUrl = explode('/', $_SERVER['HTTP_REFERER']);
        // echo "<pre>";print_r($explodeUrl);die;
        for ($u = 0; $u < count($explodeUrl); ++$u) {
            if ('assets' === $explodeUrl[$u] || 'admin' === $explodeUrl[$u]) {
                break;
            }
            $baseUrl .= $explodeUrl[$u] . '/';
        }
    }

// $baseUrl = "http://192.168.1.131/cubejekdev_cubejekx/";
// $basePath = dirname(dirname(dirname(dirname(__FILE__))))."/";

// Added By HJ On 06-11-2019 For Solved Issue 442 Of Sheet End
    /*
     * CKFinder Configuration File
     *
     * For the official documentation visit https://ckeditor.com/docs/ckfinder/ckfinder3-php/
     */

// ============================ PHP Error Reporting ====================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/debugging.html

// Production
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
// ini_set('display_errors', 0);

// Development
// ============================ General Settings =======================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html

    $config = [];

// ============================ Enable PHP Connector HERE ==============================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_authentication

    $config['authentication'] = static function () {
        // return false;
        return true;
    };

// ============================ License Key ============================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_licenseKey

    if (!defined('CKFINDER_LICENCE_NAME')) {
        $licenseName = '';
    } else {
        $licenseName = CKFINDER_LICENCE_NAME;
    }

    if (!defined('CKFINDER_LICENCE_KEY')) {
        $licenseName = '';
    } else {
        $licenseKey = CKFINDER_LICENCE_KEY;
    }

    $config['licenseName'] = $licenseName;
    $config['licenseKey'] = $licenseKey;

// ============================ CKFinder Internal Directory ============================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_privateDir

    $config['privateDir'] = [
        'backend' => 'default',
        'tags' => '.ckfinder/tags',
        'logs' => '.ckfinder/logs',
        'cache' => '.ckfinder/cache',
        'thumbs' => '.ckfinder/cache/thumbs',
    ];

// ============================ Images and Thumbnails ==================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_images

    $config['images'] = [
        'maxWidth' => 1_600,
        'maxHeight' => 1_200,
        'quality' => 80,
        'sizes' => [
            'small' => ['width' => 480, 'height' => 320, 'quality' => 80],
            'medium' => ['width' => 600, 'height' => 480, 'quality' => 80],
            'large' => ['width' => 800, 'height' => 600, 'quality' => 80],
        ],
    ];

// =================================== Backends ========================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_backends

    $config['backends'][] = [
        'name' => 'default',
        'adapter' => 'local',
        // 'baseUrl'      => $baseUrl.'assets/plugins/ckfinder/userfiles/',
        'baseUrl' => $baseUrl . 'webimages/upload/ckImages/',
        //  'root'         => '', // Can be used to explicitly set the CKFinder user files directory.
        'chmodFiles' => 0777,
        'chmodFolders' => 0755,
        'filesystemEncoding' => 'UTF-8',
    ];

// ================================ Resource Types =====================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_resourceTypes

    $config['defaultResourceTypes'] = '';

    $config['resourceTypes'][] = [
        'name' => 'Files', // Single quotes not allowed.
        'directory' => 'files',
        'maxSize' => 0,
        'allowedExtensions' => '7z,aiff,asf,avi,bmp,csv,doc,docx,fla,flv,gif,gz,gzip,jpeg,jpg,mid,mov,mp3,mp4,mpc,mpeg,mpg,ods,odt,pdf,png,ppt,pptx,pxd,qt,ram,rar,rm,rmi,rmvb,rtf,sdc,sitd,swf,sxc,sxw,tar,tgz,tif,tiff,txt,vsd,wav,wma,wmv,xls,xlsx,zip',
        'deniedExtensions' => '',
        'backend' => 'default',
    ];

    $config['resourceTypes'][] = [
        'name' => 'Images',
        'directory' => 'images',
        'maxSize' => 0,
        'allowedExtensions' => 'bmp,gif,jpeg,jpg,png',
        'deniedExtensions' => '',
        'backend' => 'default',
    ];

// ================================ Access Control =====================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_roleSessionVar

    $config['roleSessionVar'] = 'CKFinder_UserRole';

// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_accessControl
    $config['accessControl'][] = [
        'role' => '*',
        'resourceType' => '*',
        'folder' => '/',

        'FOLDER_VIEW' => true,
        'FOLDER_CREATE' => true,
        'FOLDER_RENAME' => true,
        'FOLDER_DELETE' => true,

        'FILE_VIEW' => true,
        'FILE_CREATE' => true,
        'FILE_RENAME' => true,
        'FILE_DELETE' => false,

        'IMAGE_RESIZE' => true,
        'IMAGE_RESIZE_CUSTOM' => true,
    ];

// ================================ Other Settings =====================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html

    $config['overwriteOnUpload'] = true;
    $config['checkDoubleExtension'] = true;
    $config['disallowUnsafeCharacters'] = false;
    $config['secureImageUploads'] = true;
    $config['checkSizeAfterScaling'] = true;
    $config['htmlExtensions'] = ['html', 'htm', 'xml', 'js'];
    $config['hideFolders'] = ['.*', 'CVS', '__thumbs'];
    $config['hideFiles'] = ['.*'];
    $config['forceAscii'] = false;
    $config['xSendfile'] = false;

// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_debug
    $config['debug'] = false;

// ==================================== Plugins ========================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_plugins

    $config['pluginsDirectory'] = __DIR__ . '/plugins';
    $config['plugins'] = [];

// ================================ Cache settings =====================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_cache

    $config['cache'] = [
        'imagePreview' => 24 * 3_600,
        'thumbnails' => 24 * 3_600 * 365,
        'proxyCommand' => 0,
    ];

// ============================ Temp Directory settings ================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_tempDirectory

// $config['tempDirectory'] = sys_get_temp_dir();
    $config['tempDirectory'] = $basePath . 'webimages/upload/ckImages/';
// $config['tempDirectory'] = $basePath.'assets/plugins/ckfinder/userfiles/';

// ============================ Session Cause Performance Issues =======================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_sessionWriteClose

    $config['sessionWriteClose'] = true;

// ================================= CSRF protection ===================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_csrfProtection

    $config['csrfProtection'] = true;

// ===================================== Headers =======================================
// https://ckeditor.com/docs/ckfinder/ckfinder3-php/configuration.html#configuration_options_headers

    $config['headers'] = [];

// ============================== End of Configuration =================================

// Config must be returned - do not change it.
    return $config;
