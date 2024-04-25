<?php



use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\Facade;

require_once __DIR__.'/../../../vendor/autoload.php';

include_once $tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/library/helper.php';

include_once $tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/library/User.php';

$db = new Manager();

$db->addConnection([
    'driver' => 'mysql',
    'host' => TSITE_SERVER,
    'database' => TSITE_DB,
    'username' => TSITE_USERNAME,
    'password' => TSITE_PASS,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$db->setAsGlobal();
$db->bootEloquent();

class common_include extends Facade
{
    public static function connection($name)
    {
        global $db;

        return $db->connection($name)->getSchemaBuilder();
    }

    protected static function getFacadeAccessor()
    {
        global $db;

        return $db->connection()->getSchemaBuilder();
    }
}

class_alias(Manager::class, 'DB');
