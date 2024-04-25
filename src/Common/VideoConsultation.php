<?php



namespace Kesk\Web\Common;

class VideoConsultation
{
    public function __construct() {}

    public function checkVideoConsultEnable($iVehicleCategoryId)
    {
        global $obj;
        $sql_vehicle_category_table_name = getVehicleCategoryTblName();
        $vehicle_sub_category_arr = $obj->MySQLSelect("SELECT eVideoConsultEnable FROM {$sql_vehicle_category_table_name} WHERE iParentId = '{$iVehicleCategoryId}' AND eStatus = 'Active'");
        $isVideoConsultEnable = 'No';
        if (!empty($vehicle_sub_category_arr) && \count($vehicle_sub_category_arr) > 0) {
            foreach ($vehicle_sub_category_arr as $sub_category) {
                if ('Yes' === $sub_category['eVideoConsultEnable']) {
                    $isVideoConsultEnable = 'Yes';

                    break;
                }
            }
        }

        return $isVideoConsultEnable;
    }

    public function updateVideoConsultService($Data)
    {
        global $obj;
        $iDriverId = $Data['iDriverId'];
        $iVehicleCategoryId = $Data['iVehicleCategoryId'];
        $eVideoConsultServiceCharge = $Data['eVideoConsultServiceCharge'];
        $eVideoConsultEnableProvider = $Data['eVideoConsultEnableProvider'] ?? 'No';
        $eVideoServiceDescription = $Data['eVideoServiceDescription'];
        $sqlServicePro = "SELECT * FROM `driver_services_video_consult_charges` WHERE iDriverId = '".$iDriverId."' AND iVehicleCategoryId='".$iVehicleCategoryId."'";
        $serviceProData = $obj->MySQLSelect($sqlServicePro);
        $Data_update = [];
        $Data_update['iDriverId'] = $iDriverId;
        $Data_update['iVehicleCategoryId'] = $iVehicleCategoryId;
        if (isset($Data['eVideoConsultServiceCharge'])) {
            $Data_update['eVideoConsultServiceCharge'] = $eVideoConsultServiceCharge;
        }
        if (isset($Data['eVideoConsultEnableProvider'])) {
            $Data_update['eVideoConsultEnableProvider'] = $eVideoConsultEnableProvider;
        }
        if (isset($Data['eVideoServiceDescription'])) {
            $Data_update['eVideoServiceDescription'] = $eVideoServiceDescription;
        }
        if (\count($serviceProData) > 0) {
            if ('Yes' === $eVideoConsultEnableProvider && 'No' === $serviceProData[0]['eApproved']) {
                $Data_update['eStatus'] = 'Pending';
            }
            $where = " iDriverServiceId = '".$serviceProData[0]['iDriverServiceId']."'";
            $id = $obj->MySQLQueryPerform('driver_services_video_consult_charges', $Data_update, 'update', $where);
        } else {
            $Data_update['eStatus'] = 'Inactive';
            if ('Yes' === $eVideoConsultEnableProvider) {
                $Data_update['eStatus'] = 'Pending';
            }
            $id = $obj->MySQLQueryPerform('driver_services_video_consult_charges', $Data_update, 'insert');
        }

        return $id;
    }

    public function getServiceDetails($iDriverId, $iVehicleCategoryId)
    {
        global $obj;
        $returnArr = [];
        $returnArr['eVideoConsultServiceCharge'] = 0;
        $returnArr['eVideoConsultEnableProvider'] = 'No';
        $returnArr['eVideoServiceDescription'] = '';
        $returnArr['eVideoConsultStatus'] = 'Inactive';
        $vc_data = $obj->MySQLSelect("SELECT fCommissionVideoConsult FROM vehicle_category WHERE iVehicleCategoryId = '{$iVehicleCategoryId}'");
        $returnArr['fCommission'] = $vc_data[0]['fCommissionVideoConsult'];
        $service_data = $obj->MySQLSelect("SELECT eVideoConsultServiceCharge, eVideoConsultEnableProvider, eVideoServiceDescription, eStatus FROM driver_services_video_consult_charges WHERE iDriverId = '{$iDriverId}' AND iVehicleCategoryId = '{$iVehicleCategoryId}'");
        if (!empty($service_data) && \count($service_data) > 0) {
            $returnArr['eVideoConsultServiceCharge'] = $service_data[0]['eVideoConsultServiceCharge'];
            $returnArr['eVideoConsultEnableProvider'] = $service_data[0]['eVideoConsultEnableProvider'];
            $returnArr['eVideoServiceDescription'] = $service_data[0]['eVideoServiceDescription'];
            $returnArr['eVideoConsultStatus'] = $service_data[0]['eStatus'];
        }

        return $returnArr;
    }
}
