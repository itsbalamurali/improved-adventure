<?php
include_once '../common.php';
$id = $_REQUEST['id'] ?? '';
$sql = "SELECT * FROM register_driver where iDriverId='{$id}'";
$data_drv = $obj->MySQLSelect($sql);

?>

<span><b>Email: </b><?php echo clearEmail($data_drv[0]['vEmail']); ?></span>
<br>
<span><b>Phone Number: </b>(<?php echo $data_drv[0]['vCode']; ?>) <?php echo clearPhone($data_drv[0]['vPhone']); ?></span>

