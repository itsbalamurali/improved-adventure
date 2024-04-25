<?php
include_once '../common.php';
$tbl_name = 'food_menu';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
$iParentId = $_REQUEST['iParentId'] ?? '';
$iFoodMenuId = $_REQUEST['iFoodMenuId'] ?? '';
$iMenuItemId = $_REQUEST['iMenuItemId'] ?? '';
$method = $_REQUEST['method'] ?? '';
$oldVal = $_REQUEST['oldVal'] ?? '';
$page = $_REQUEST['page'] ?? '';
$itemParentId = $_REQUEST['itemParentId'] ?? '';
$iServiceId = $_REQUEST['iServiceId'] ?? '';
$iDisplayOrder = $_REQUEST['iDisplayOrder'] ?? '';
$vCode = $_REQUEST['vCode'] ?? '';
if ('items' === $page) {
    if ('getSubMenuCategory' === $method) {
        $sql = 'SELECT iFoodMenuId,vMenu_EN FROM '.$tbl_name." WHERE iParentId = '".$iParentId."' AND eStatus='Active'";
        $db_data = $obj->MySQLSelect($sql); ?>
		<label>Sub-Menu Category</label>
		<select class="form-control" name = 'iFoodMenuId' onChange="changeDisplayOrder(this.value,'<?php echo $iMenuItemId; ?>');" required>
		<option value="">--select--</option>
		<?php for ($i = 0; $i < count($db_data); ++$i) {?>
			<option value="<?php echo $db_data[$i]['iFoodMenuId']; ?>" <?php if ($oldVal === $db_data[$i]['iFoodMenuId']) {
			    echo 'selected';
			} ?> ><?php echo $db_data[$i]['vMenu_EN']; ?></option>
		<?php } ?>
		</select>
	<?php exit;
    }

    if ('getParentItems' === $method) {
        $sql = "SELECT iMenuItemId,vItemType_EN FROM menu_items WHERE iFoodMenuId = '".$iFoodMenuId."' AND iParentId='0' AND eStatus='Active'";
        $db_data = $obj->MySQLSelect($sql); ?>
		<option value="0">Add New Parent</option>
		<?php for ($i = 0; $i < count($db_data); ++$i) {?>
			<option value="<?php echo $db_data[$i]['iMenuItemId']; ?>" <?php if ($itemParentId === $db_data[$i]['iMenuItemId']) {
			    echo 'selected';
			} ?> ><?php echo $db_data[$i]['vItemType_EN']; ?></option>
		<?php } ?>
	<?php exit;
    }

    if (isset($_REQUEST['iFoodMenuId'])) {
        $cmpss = '';
        if ('' !== $iFoodMenuId) {
            $cmpss = " AND iFoodMenuId = '{$iFoodMenuId}' ";
        }

        $sql = "SELECT COUNT(iMenuItemId) AS Total FROM menu_items WHERE 1=1 {$cmpss}";
        $db_count = $obj->MySQLSelect($sql);
        $count = $db_count[0]['Total'];
        $newCnt = $count + 1;
        $totalVal = $count + 1;

        if ('' !== $iMenuItemId) {
            $sql = "SELECT iDisplayOrder FROM menu_items WHERE 1=1 AND iMenuItemId='{$iMenuItemId}' {$cmpss}";
            $db_old = $obj->MySQLSelect($sql);
            if (!empty($db_old)) {
                $newCnt = $count;
                $totalVal = $db_old[0]['iDisplayOrder'];
            }
        }
        ?>
		<select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
		<?php for ($i = 1; $i <= $newCnt; ++$i) {?>
		<option value="<?php echo $i; ?>"
		<?php if ($i === $totalVal) {
		    echo 'selected';
		}?>> <?php echo $i; ?> </option>
		<?php } ?>
		</select>
	<?php exit;
    }
} elseif ('cuisine' === $page) {
    $display_order_cuisine = $obj->MySQLSelect("SELECT MAX(iDisplayOrder) as max_display_order FROM cuisine WHERE iServiceId = '{$iServiceId}' ");

    if ('Add' === $method) {
        $max_display_order = $display_order_cuisine[0]['max_display_order'] + 1;
    } else {
        $max_display_order = $display_order_cuisine[0]['max_display_order'];
    }
    if ('' === $iServiceId) {
        $max_display_order = 1;
    }

    ?>
	<select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
		<?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
		<option value="<?php echo $i; ?>" <?php echo $i === $max_display_order && 'Add' === $method || $i === $iDisplayOrder ? 'selected' : ''; ?> > <?php echo $i; ?> </option>
		<?php } ?>
	</select>
<?php exit;
} elseif ('store_banner' === $page) {
    $display_order_banner = $obj->MySQLSelect("SELECT MAX(iDisplayOrder) as max_display_order FROM banners WHERE iServiceId = '{$iServiceId}' AND vCode='{$vCode}'");
    if ('Add' === $method) {
        $max_display_order = $display_order_banner[0]['max_display_order'] + 1;
    } else {
        $max_display_order = $display_order_banner[0]['max_display_order'];
    } ?>
	<select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
		<?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
		<option value="<?php echo $i; ?>" <?php echo $i === $max_display_order && 'Add' === $method || $i === $iDisplayOrder ? 'selected' : ''; ?> > <?php echo $i; ?> </option>
		<?php } ?>
	</select>
<?php exit;
} elseif ('driver_details_field' === $page) {
    $display_order_driver_details = $obj->MySQLSelect("SELECT MAX(iDisplayOrder) as max_display_order FROM ride_share_driver_fields WHERE eStatus != 'Deleted' ");
    if ('Add' === $method) {
        $max_display_order = $display_order_driver_details[0]['max_display_order'] + 1;
    } else {
        $max_display_order = $display_order_driver_details[0]['max_display_order'];
    } ?>
	<select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
		<?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
		<option value="<?php echo $i; ?>" <?php echo $i === $max_display_order && 'Add' === $method || $i === $iDisplayOrder ? 'selected' : ''; ?> > <?php echo $i; ?> </option>
		<?php } ?>
	</select>
<?php exit;
} else {
    if (isset($_REQUEST['iCompanyId'], $_REQUEST['iParentId'])) {
        $cmpss = '';
        if ('' !== $iCompanyId && '' !== $iParentId) {
            $cmpss = " AND iCompanyId='{$iCompanyId}'";
        }
        $sql = 'SELECT COUNT(iFoodMenuId) AS Total FROM '.$tbl_name." WHERE 1=1 {$cmpss}";
        $db_count = $obj->MySQLSelect($sql);
        $count = $db_count[0]['Total'];
        $newCnt = $count + 1;
        $totalVal = $count + 1;

        if ('' !== $iFoodMenuId) {
            $sql = 'SELECT iDisplayOrder FROM '.$tbl_name." WHERE 1=1 AND iFoodMenuId='{$iFoodMenuId}'";
            $db_old = $obj->MySQLSelect($sql);
            if (!empty($db_old)) {
                $newCnt = $count;
                $totalVal = $db_old[0]['iDisplayOrder'];
            }
        }
        ?>
		<select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
		<?php for ($i = 1; $i <= $newCnt; ++$i) {?>
		<option value="<?php echo $i; ?>"
		<?php if ($i === $totalVal) {
		    echo 'selected';
		}?>> <?php echo $i; ?> </option>
		<?php } ?>
		</select>
	<?php exit;
    }
} ?>