<?php



namespace Kesk\Web\Admin\Library;

use Kesk\Web\Admin\Library\Models\Administrator;

class User
{
    public $id;

    public $role_id;

    public $locations;

    private $is_login;

    private $permissions = [];

    private $roles = [];

    private $debug = false;

    public function __construct()
    {
        $this->checkSession();
        $this->getRoles();
        $this->getPermission();
    }

    public function isLogin($redirect = false)
    {
        if (!$this->is_login && true === $redirect) {
            $this->redirect('index.php');
        }

        return $this->is_login;
    }

    public function redirect($path = 'dashboard.php'): void
    {
        global $tconfig;

        ob_get_clean();

        if (ONLYDELIVERALL === 'Yes') {
            $path = 'store-dashboard.php';
        }

        if (!$this->is_login) {
            $path = 'index.php';

            if (isset($_SERVER['REQUEST_URI'])) {
                $_SESSION['login_redirect_url'] = $_SERVER['REQUEST_URI'];
            }
        }

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH']); // check file is from ajax then session is not set bc it is not redirect after login

        if (!$isAjax) {
            $_SESSION['login_redirect_url'] = $_SERVER['REQUEST_URI']; // added by SP for redirection on admin after login on 15-7-2019, here put bc when page open from admin side and logout then open same link
        }

        header('Location:'.$tconfig['tsite_url_main_admin'].$path);

        exit;
    }

    public function hasRole($role)
    {
        // return true;

        return $this->hasRoles($role);
    }

    public function hasPermission($permission_name)
    {
        return $this->hasPermissions($permission_name);
    }

    public function hasRoles($roles)
    {
        if (!\is_array($roles)) {
            $roles = [$roles];
        }

        $has_role = false;

        foreach ($roles as $key => $role) {
            if (is_numeric($role) && \in_array($role, array_keys($this->roles), true)) {
                $has_role = true;
            } elseif (\in_array($role, $this->roles, true)) {
                $has_role = true;
            }
        }

        return $has_role;
    }

    public function errorMessage($message = 'You are not authorized.'): void {}

    public function hasPermissions($permission_name)
    {
        // return true;

        if (!\is_array($permission_name)) {
            $permission_name = [$permission_name];
        }

        $hasPermission = false;

        foreach ($permission_name as $key => $role) {
            if (\in_array($role, $this->permissions, true)) {
                $hasPermission = true;
            }
        }

        return $hasPermission;
    }

    public function getLocations()
    {
        return $this->locations;
    }

    private function checkSession(): void
    {
        if (isset($_SESSION['sess_iAdminUserId']) && !empty($_SESSION['sess_iAdminUserId'])) {
            $this->id = $_SESSION['sess_iAdminUserId'];
            $this->is_login = true;
            // $this->locations = \Models\Administrator::find($this->id)->locations->pluck('iLocationId')->toArray();
            $this->locations = Administrator::find($this->id);
        } else {
            $dataRoles = Administrator::sessionRoles();
            $this->is_login = $dataRoles ? true : false;
        }

        $this->role_id = $_SESSION['sess_iGroupId'] ?? 0;

        if (0 === $this->role_id) {
            $this->is_login = false;
        }
    }

    /* private function getUser(){

      $sql = "SELECT * FROM administrators WHERE iAdminId=".$this->id."";

      $row = $obj->MySQLSelect($sql);

      return $row[0];

      } */

    private function getRoles(): void
    {
        global $obj;

        $sql = "SELECT ag.iGroupId, ag.vGroup FROM administrators as a LEFT JOIN admin_groups as ag ON a.iGroupId = ag.iGroupId where a.iGroupId = {$this->role_id}";

        $row = $obj->MySQLSelect($sql);

        if ($row) {
            foreach ($row as $key => $value) {
                $this->roles[$value['iGroupId']] = $value['vGroup'];
            }
        }
    }

    private function getPermission(): void
    {
        global $obj, $MODULES_OBJ;

        /*$table1 = "admin_group_permission";
        $table2 = "admin_permissions";*/

        $table1 = 'admin_pro_group_permission';
        $table2 = 'admin_pro_permissions';

        $sql = "SELECT ap.id, ap.permission_name, ap.eFor
            FROM
               {$table1} as agp
                LEFT JOIN {$table2} as ap ON ap.id = agp.permission_id
            WHERE

                agp.group_id = {$this->role_id} AND ap.status = 'Active'";

        $permissions = $obj->MySQLSelect($sql);

        // Added By HJ As Per Disucss with KS On 06-12-2019 For Check Uberx Service Status Start

        $uberxService = $MODULES_OBJ->isUberXFeatureAvailable('Yes') ? 'Yes' : 'No';
        $rideEnable = $MODULES_OBJ->isRideFeatureAvailable('Yes') ? 'Yes' : 'No';
        $deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable('Yes') ? 'Yes' : 'No';
        $deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable('Yes') ? 'Yes' : 'No';

        $biddingEnable = $MODULES_OBJ->isEnableBiddingServices('Yes') ? 'Yes' : 'No';
        $nearbyEnable = $MODULES_OBJ->isEnableNearByService('Yes') ? 'Yes' : 'No';
        $trackServiceEnable = $MODULES_OBJ->isEnableTrackServiceFeature('Yes') ? 'Yes' : 'No';
        $trackAnyServiceEnable = $MODULES_OBJ->isEnableTrackAnyServiceFeature('Yes') ? 'Yes' : 'No';
        $rideShareEnable = $MODULES_OBJ->isEnableRideShareService('Yes') ? 'Yes' : 'No';
        $rentitemEnable = $MODULES_OBJ->isEnableRentItemService('Yes') ? 'Yes' : 'No';
        $rentestateEnable = $MODULES_OBJ->isEnableRentEstateService('Yes') ? 'Yes' : 'No';
        $rentcarEnable = $MODULES_OBJ->isEnableRentCarsService('Yes') ? 'Yes' : 'No';
        $genieEnable = GENIE_ENABLED;
        $runnerEnable = RUNNER_ENABLED;
        $KioskEnable = ENABLEKIOSKPANEL;

        $IS_FLY_MODULE_AVAIL = $MODULES_OBJ->isAirFlightModuleAvailable('', 'Yes');

        $newTmpArr = [];

        for ($i = 0; $i < \count($permissions); ++$i) {
            $eForPermission = $permissions[$i]['eFor'];

            if ('No' === $uberxService && 'UberX' === $eForPermission) {
                continue;
            }

            if ('No' === $rideEnable && 'Ride' === $eForPermission) {
                continue;
            }

            if ('No' === $deliveryEnable && ('Delivery' === $eForPermission || 'Multi-Delivery' === $eForPermission)) {
                continue;
            }

            if ('No' === $deliverallEnable && 'DeliverAll' === $eForPermission) {
                continue;
            }

            if ('No' === $uberxService && 'No' === $rideEnable && 'No' === $deliveryEnable && 'Ride,Delivery,UberX' === $eForPermission) {
                continue;
            }

            if ('No' === $biddingEnable && 'Bidding' === $eForPermission) {
                continue;
            }

            if ('No' === $nearbyEnable && 'NearBy' === $eForPermission) {
                continue;
            }

            if ('No' === $trackServiceEnable && 'TrackService' === $eForPermission) {
                continue;
            }

            if ('No' === $rideShareEnable && 'RideShare' === $eForPermission) {
                continue;
            }

            if ('No' === $rentitemEnable && 'RentItem' === $eForPermission) {
                continue;
            }

            if ('No' === $rentestateEnable && 'RentEstate' === $eForPermission) {
                continue;
            }

            if ('No' === $rentcarEnable && 'RentCars' === $eForPermission) {
                continue;
            }

            if (false === $IS_FLY_MODULE_AVAIL && 'Fly' === $eForPermission) {
                continue;
            }

            if ('No' === $trackAnyServiceEnable && 'TrackAnyService' === $eForPermission) {
                continue;
            }

            if ('No' === $genieEnable && 'Genie' === $eForPermission) {
                continue;
            }

            if ('No' === $runnerEnable && 'Runner' === $eForPermission) {
                continue;
            }

            if ('No' === $KioskEnable && 'Kiosk' === $eForPermission) {
                continue;
            }

            $newTmpArr[] = $permissions[$i];
        }

        $permissions = array_values($newTmpArr);

        // Added By HJ As Per Disucss with KS On 06-12-2019 For Check Uberx Service Status End

        if ($permissions) {
            $this->permissions = array_map(static fn ($item) => $item['permission_name'], $permissions);
        }
    }
}
