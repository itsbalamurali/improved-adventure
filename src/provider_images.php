<?php
include_once 'common.php';
$AUTH_OBJ->checkMemberAuthentication();

require_once TPATH_CLASS.'/Imagecrop.class.php';
$thumb = new thumbnail();
$tSiteUrl = $tconfig['tSiteUrl'];
$script = 'Gallary';
$abc = 'driver';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
setRole($abc, $url);
$tbl_name = 'driver_vehicle';
$success = $_GET['success'] ?? '';
$action = $_REQUEST['action'] ?? '';
$id = $_GET['id'] ?? '';
$driverid = $_SESSION['sess_iUserId'];
$error = isset($_GET['success']) && 0 === $_GET['success'] ? 1 : '';
$var_msg = $_REQUEST['var_msg'] ?? '';
$getProviderImages = $obj->MySQLSelect("SELECT * FROM provider_images WHERE iDriverId='".$driverid."' AND eStatus='Active'");
// echo "<pre>";
$providerImgesArr = [];
$providerImgUrl = $tconfig['tsite_upload_provider_image'];
$providerImgPath = $tconfig['tsite_upload_provider_image_path'];
$tSiteUrl = $tconfig['tsite_url'];
$comfirmMessage = addslashes($langage_lbl['LBL_DELETE_IMG_CONFIRM_NOTE_WEB']);
echo "<script>localStorage.confirmmessage = '{$comfirmMessage}';</script>";
?>
<script type="text/javascript">
    var existingFiles = [];
</script>
<?php
for ($r = 0; $r < count($getProviderImages); ++$r) {
    $imgTmpArr = [];
    $imgNameArr = explode('.', $getProviderImages[$r]['vImage']);
    $imgTmpArr['ext'] = array_pop($imgNameArr);
    if (in_array($imgTmpArr['ext'], [
        'mp4',
        'webm',
        'mov',
        'wmv',
        'avi',
        'flv',
        'mkv',
    ], true)
    ) {
        $imgTmpArr['imageUrl'] = $providerImgUrl.'/'.$getProviderImages[$r]['vImage'];
    } else {
        $imgTmpArr['imageUrl'] = $tconfig['tsite_url'].'resizeImg.php?h=120&MAX_WIDTH=120&src='.$providerImgUrl.'/'.$getProviderImages[$r]['vImage'];
    }
    $imgTmpArr['imagePath'] = $providerImgPath.'/'.$getProviderImages[$r]['vImage'];
    $imgFileSize = filesize($imgTmpArr['imagePath']);
    $imgTmpArr['name'] = $getProviderImages[$r]['vImage'];
    $imgTmpArr['size'] = $imgFileSize;
    $imgNameArr = explode('.', $getProviderImages[$r]['vImage']);
    $imgTmpArr['ext'] = array_pop($imgNameArr);
    // $providerImgesArr[] = $imgTmpArr;
    ?>
    <script>
        existingFiles.push(<?php echo json_encode($imgTmpArr); ?>);
    </script><?php
}
// print_r($providerImgesArr);die;
?>
<!DOCTYPE html>
<html lang="en"
      dir="<?php echo (isset($_SESSION['eDirectionCode']) && '' !== $_SESSION['eDirectionCode']) ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl['LBL_VEHICLES']; ?></title>
    <!-- Default Top Script and css -->
    <?php include_once 'top/top_script.php'; ?>
    <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css"/>
    <?php if ('Ride-Delivery-UberX' === $APP_TYPE) { ?>
        <link rel="stylesheet" type="text/css" href="assets/css/vehicles_cubejek.css">
    <?php } else { ?>
        <link rel="stylesheet" type="text/css" href="assets/css/vehicles.css">
    <?php } ?>
    <link href="assets/plugins/dropzone/css/dropzone.css" rel="stylesheet"/>
    <link rel="stylesheet" href="assets/css/modal_alert.css"/>
    <style>
        .fileupload-preview {
            line-height: 150px;
        }

        .dropzone {
            border: 2px dashed #ccc;
            background: #fff;
            width: 100%;
            padding: 0;
            min-height: 361px;
        }

        .add-more-images {
            padding: 0 !important;
            border: 4px dashed #ffffff !important;
            background: transparent !important;
            cursor: pointer !important;
            display: none;
        }
        .add-more-images:hover, .dz-details img {
            cursor: pointer !important;
        }
        .add-more-images .dz-details img {
            right: 0;
            bottom: 0;
            margin: auto;
            height: 30px;
            width: 30px !important;
            cursor: pointer !important;
        }
        .add-more-images .dz-details {
            width: 126px !important;
            height: 156px !important;
            margin-bottom: 0 !important;
            cursor: pointer !important;
        }

        .dz-details, .dz-details img {
            width: 0px !important
            height: 0px !important
        }

        .dz-remove:hover {
            cursor: pointer;
        }
        .dropzone .add-more-images {
            display:none;
        }
        .dropzone.dz-started .add-more-images{
            display:inline-block;
        }
    </style>
    <!-- End: Default Top Script and css-->
</head>

<body>
<!-- home page -->
<div id="main-uber-page">
    <!-- Top Menu -->
    <!-- Left Menu -->
    <?php include_once 'top/left_menu.php'; ?>
    <!-- End: Left Menu-->
    <?php include_once 'top/header_topbar.php'; ?>
    <!-- End: Top Menu-->
    <!-- contact page-->
    <section class="profile-section my-trips">
        <div class="profile-section-inner">
            <div class="profile-caption">
                <div class="page-heading">
                    <h1><?php echo $langage_lbl['LBL_SERVICE_TXT'].' '.$langage_lbl['LBL_IMAGE'].'s'; ?></h1>
                    <p style="margin-top: 10px"><?php echo $langage_lbl['LBL_DROPZONE_UPLOAD_IMAGE_TXT']; ?></p>
                </div>
            </div>
        </div>
    </section>
    <section class="profile-earning">
        <div class="profile-earning-inner">
            <form action="<?php echo $tSiteUrl; ?>ajax_dropzon_upload.php?action=upload" class="dropzone"
                  id="my-awesome-dropzone">
                <div class="upload-images"></div>
                <div class="add-more-images dz-preview dz-image-preview" style="display: none;">
                    <div class="dz-details">
                        <img src="<?php echo $tconfig['tsite_url']; ?>assets/img/plus.svg">
                    </div>
                </div>

            </form>
        </div>
    </section>

    <!-- footer part -->
    <?php include_once 'footer/footer_home.php'; ?>
    <!-- footer part end -->
    <!-- End:contact page-->
    <div style="clear:both;"></div>
</div>
<!-- home page end-->
<!-- Footer Script -->
<?php include_once 'top/footer_script.php'; ?>
<script src="assets/plugins/dropzone/dropzone.js"></script>
<script src="assets/js/modal_alert.js"></script>
<script>
    Dropzone.autoDiscover = false;
    var dropzoneOptions = {
        dictDefaultMessage: '<?php echo addslashes($langage_lbl['LBL_CLICK_DROP_TO_UPLOAD_MSG']); ?>',
        dictRemoveFile: '<?php echo addslashes($langage_lbl['LBL_REMOVE_TEXT']); ?>',
        dictCancelUpload: '<?php echo addslashes($langage_lbl['LBL_CANCEL_UPLOAD']); ?>',
        addedfile: function (file) {
            var node, removeFileEvent, removeLink, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2, _results,
                _this = this;
            if (this.element === this.previewsContainer) {
                this.element.classList.add("dz-started");
            }
            file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
            file.previewTemplate = file.previewElement;
            this.previewsContainer.appendChild(file.previewElement);
            _ref = file.previewElement.querySelectorAll("[data-dz-name]");
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i];
                node.textContent = file.name;

            }
            _ref1 = file.previewElement.querySelectorAll("[data-dz-size]");
            for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
                node = _ref1[_j];
                node.innerHTML = this.filesize(file.size);
            }
            if (this.options.addRemoveLinks) {
                file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\"  data-dz-remove>" + this.options.dictRemoveFile + "</a>");
                file.previewElement.appendChild(file._removeLink);

            }
            removeFileEvent = function (e) {

                e.preventDefault();
                e.stopPropagation();
                //alert(Dropzone.UPLOADING);
                window.file = file;
                window._this = _this;
                show_alert("", localStorage.confirmmessage, "<?php echo addslashes($langage_lbl_admin['LBL_BTN_CANCEL_TXT']); ?>", "<?php echo addslashes($langage_lbl['LBL_BTN_OK_TXT']); ?>", "", function (btn_id) {

                    if (btn_id == 1) {
                        if (file.status === Dropzone.UPLOADING) {
                            return Dropzone.confirm(_this.options.dictCancelUploadConfirmation, function () {
                                return _this.removeFile(file);
                            });
                        } else {
                            if (_this.options.dictRemoveFileConfirmation) {
                                return Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function () {
                                    return _this.removeFile(file);
                                });
                            } else {
                                return _this.removeFile(file);
                            }
                        }
                    } else {
                        return false;
                    }
                }, true, true, true);
            };
            _ref2 = file.previewElement.querySelectorAll("[data-dz-remove]");
            _results = [];
            for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                removeLink = _ref2[_k];
                _results.push(removeLink.addEventListener("click", removeFileEvent));
            }
            return _results;
        },
    };
    var uploader = document.querySelector('#my-awesome-dropzone');


    var newDropzone = new Dropzone(uploader, dropzoneOptions);

    newDropzone.on("addedfile", function(file) {
        addMoreImagesBlock();
    });

    newDropzone.on("success", function(file, response) {
        addMoreImagesBlock()
    });
    $(document).ready(function () {

        /*$(".dropzone").dropzone({
            url: "<?php echo $tSiteUrl; ?>ajax_dropzon_upload.php?action=upload",
        });*/

        setTimeout(function () {
            imgtovideo()
            $('.fullscreen-button').click(function () {
                //console.log('hellooooooo');
                if (this.requestFullscreen) {
                    this.requestFullscreen();
                } else if (this.webkitRequestFullscreen) {
                    /* Safari */
                    this.webkitRequestFullscreen();
                } else if (this.msRequestFullscreen) {
                    /* IE11 */
                    this.msRequestFullscreen();
                }

            })
        }, 500);
    });

    function imgtovideo(image = '') {
        $("img").each(function () {

            var extension = $(this).attr('src').split('.').pop();

            if (extension == 'mp4' || extension == 'webm' || extension == 'mov' || extension == 'wmv' || extension == 'avi' || extension == 'flv' || extension == 'mkv') {
                var video = '<video class = "fullscreen-button" width = 100px  height = 100px src="' + $(this).attr('src') + '"></video>';
                $(this).replaceWith(video);
            }
        })
    }

    $(document).ready(function() {
        console.log(existingFiles.length);
        if(existingFiles.length > 0)
        {
            setTimeout(function(){
                addMoreImagesBlock();
            }, 200);
        }
    });

    function addMoreImagesBlock() {
        var add_more_images = $('.add-more-images').clone();
        $('.add-more-images').remove();
        setTimeout(function(){
            add_more_images.appendTo('#my-awesome-dropzone');
            $('.add-more-images').show();
        }, 200);
    }

    $(document).on('click', '.add-more-images', function() {
        $('#my-awesome-dropzone').trigger('click');
    });

    $(document).on('click', '.dz-preview img:not(.add-more-images .dz-details img)', function() {
        var img_url = $(this).attr('src');
        img_url = img_url.split('src=');
        window.open(img_url[1], '_blank');
    });
</script>
</body>

</html>