<?php



namespace Kesk\Web\Common;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;

class MenuItemMedia
{
    public function __construct()
    {
        if (isset($_POST['delete_img']) && $_POST['delete_img']) {
            $this->delete_images();
        }
    }

    public function delete_images(): void
    {
        global $obj, $tconfig;
        if (isset($_POST['delete_img']) && !empty($_POST['delete_img'])) {
            $delete_img = explode(',', $_POST['delete_img']);
            if (isset($delete_img[0]) && empty($delete_img[0])) {
                unset($delete_img[0]);
            }
            foreach ($delete_img as $id) {
                $sql = "SELECT * FROM `menu_item_media` WHERE iMediaId = {$id}";
                $data = $obj->MySQLSelect($sql);
                $oldImage = $data[0]['vImage'];
                $img_path = $tconfig['tsite_upload_images_menu_item_path'];
                $temp_gallery = $img_path.'/';
                $oldFilePath = $temp_gallery.$oldImage;
                if ('' !== $oldImage && file_exists($oldFilePath)) {
                    unlink($img_path.'/'.$oldImage);
                }
                $sql = 'DELETE FROM `menu_item_media` WHERE iMediaId = '.$id.' ';
                $obj->MySQLSelect($sql);
            }
        }
    }

    public function multiImageHTMl($label, $id)
    {
        global $tconfig;
        $getImageVideo = $this->getImageVideo($id);
        $html = '';
        $html .= ' <div class="row"> <div class="col-md-12 col-sm-12"> <label>'.$label.'</label> </div>';
        $class = 'hidden';
        if (\count($getImageVideo) > 0) {
            $class = 'show';
        }
        $html .= '<div class="col-md-12 col-sm-12"> <div class="manage-banner-section '.$class.'"> <div id ="gallery" class="banner-img-block">';
        foreach ($getImageVideo as $IV) {
            $imgUrl = $tconfig['tsite_upload_images_menu_item'].'/'.$IV['vImage'];
            $fileextarr = explode('.', $IV['vImage']);
            $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
            if ('' !== $IV['vImage']) {
                if (\in_array($ext, ['mp4', 'mov', 'wmv', 'avi', 'flv', 'mkv', 'webm'], true)) {
                    $imgUrl = $this->videoConvertTomp4($IV['vImage']);
                    $html .= '<div id = "'.$IV['iMediaId'].'" class="banner-img" ><video controls> <source src="'.$imgUrl.'" > </video> <div attr-id = "'.$IV['iMediaId'].'" class= "removebtn item-video">Delete</div></div>';
                } else {
                    $html .= '<div id = "'.$IV['iMediaId'].'" class="banner-img" ><img test001 src="'.$tconfig['tsite_url'].'resizeImg.php?w=220&src='.$imgUrl.'" /><div attr-name = "'.$IV['vImage'].'" attr-id = "'.$IV['iMediaId'].'" class= "removebtn">Delete</div></div>';
                }
            }
        }
        $html .= '</div></div></div> <div class="col-md-12 col-sm-12"> <div class="imageupload"> <div class="file-tab"> <span id="multi_img001"> </span> <div> <input multiple id = "previewmultiImg" name="vMultiImage[]" type="file" class="form-control"> <input value = "" id = "delete_img" name="delete_img" type="hidden" class="form-control"> </div> </div> </div> </div> </div> ';

        return $html;
    }

    public function getImageVideo($id)
    {
        global $obj;
        $sql = "SELECT * FROM `menu_item_media` WHERE iMenuItemId={$id} ";

        return $obj->MySQLSelect($sql);
    }

    public function videoConvertTomp4($video_file)
    {
        global $tconfig;
        $tmpArr = explode('.', $video_file);
        $thumb_img = $tmpArr[0].'.mp4';
        $img_url = '';
        $img_path = $tconfig['tsite_upload_images_menu_item_path'].'/'.$thumb_img;
        $vFile = $tconfig['tsite_upload_images_menu_item_path'].'/'.$video_file;
        $mainVideoUrl = $tconfig['tsite_upload_images_menu_item'].'/'.$video_file;
        if ('mp4' !== $tmpArr[1] && file_exists($vFile)) {
            if (!file_exists($img_path)) {
                require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
                $ffmpeg = FFMpeg\FFMpeg::create();
                $video = $ffmpeg->open($vFile);
                $format = new X264();
                $format->setAudioCodec('libmp3lame');
                $video->save($format, $img_path);
            }
        }
        $img = $tconfig['tsite_upload_images_menu_item'].'/'.$thumb_img;
        if (file_exists($img_path)) {
            $img_url = $img;
        } elseif (file_exists($vFile)) {
            $img_url = $mainVideoUrl;
        }

        return $img_url;
    }

    public function uploadImageVideo($file, $tsite_upload_images_menu_item_path)
    {
        global $UPLOAD_OBJ;
        $img1 = [];
        if (isset($_FILES['vMultiImage']) && !empty($_FILES['vMultiImage'])) {
            $img_path = $tsite_upload_images_menu_item_path;
            $temp_gallery = $img_path.'/';
            for ($i = 0; $i < \count($_FILES['vMultiImage']['name']); ++$i) {
                $image_object = $_FILES['vMultiImage']['tmp_name'][$i];
                $image_name = $_FILES['vMultiImage']['name'][$i];
                $Photo_Gallery_folder = $img_path.'/';
                $fileextarr = explode('.', $_FILES['vMultiImage']['name'][$i]);
                $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
                if (\in_array($ext, ['mp4', 'mov', 'wmv', 'avi', 'flv', 'mkv', 'webm'], true)) {
                    $image_name = random_int(11_111, 99_999).'.'.$ext;
                    $target_file = $Photo_Gallery_folder.basename($image_name);
                    if (move_uploaded_file($image_object, $target_file)) {
                    }

                    $img1[] = $image_name;
                } else {
                    $img1[] = $UPLOAD_OBJ->GeneralUploadImage($image_object, $image_name, $Photo_Gallery_folder, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
                }
            }
        }

        return $img1;
    }

    public function delete_image(): void
    {
        if (isset($_REQUEST['deleteId']) && !empty($_REQUEST['deleteId'])) {
            global $obj, $tconfig;
            $oldImage = $_REQUEST['name'];
            $img_path = $tconfig['tsite_upload_images_menu_item_path'];
            $temp_gallery = $img_path.'/';
            $oldFilePath = $temp_gallery.$oldImage;
            if ('' !== $oldImage && file_exists($oldFilePath)) {
                unlink($img_path.'/'.$oldImage);
            }
            $sql = 'DELETE FROM `menu_item_media` WHERE iMediaId = '.$_REQUEST['deleteId'].' ';
            echo $obj->MySQLSelect($sql);
        }
    }

    public function findTheExt($fileName)
    {
        $fileextarr = explode('.', $fileName);

        return strtolower($fileextarr[\count($fileextarr) - 1]);
    }

    public function getItemMedia($id)
    {
        global $tconfig;
        $itemimimgUrl = $tconfig['tsite_upload_images_menu_item'];
        $item_media = $this->getImageVideo($id);
        $dbItemData['MenuItemMedia'] = [];
        if (!empty($item_media)) {
            $mCount = 0;
            foreach ($item_media as $media) {
                $tmp = explode('.', $media['vImage']);
                for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                    $tmp1[] = $tmp[$i];
                }
                $file = implode('_', $tmp1);
                $ext = $tmp[\count($tmp) - 1];
                $videoExt_arr = ['MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM'];
                $dbItemData['MenuItemMedia'][$mCount]['vImage'] = $itemimimgUrl.'/'.$media['vImage'];
                $dbItemData['MenuItemMedia'][$mCount]['eFileType'] = 'Image';
                $dbItemData['MenuItemMedia'][$mCount]['ThumbImage'] = '';
                if (\in_array(strtoupper($ext), $videoExt_arr, true)) {
                    $dbItemData['MenuItemMedia'][$mCount]['eFileType'] = 'Video';
                    $dbItemData['MenuItemMedia'][$mCount]['ThumbImage'] = $this->getVideoThumbImage($media['vImage']);
                }
                ++$mCount;
            }
        }

        return $dbItemData;
    }

    public function getVideoThumbImage($video_file)
    {
        global $tconfig;
        $tmpArr = explode('.', $video_file);
        $thumb_img = $tmpArr[0].'.png';
        $img_path = $tconfig['tsite_upload_images_menu_item_path'].'/thumnails/'.$thumb_img;
        $img_url = $tconfig['tsite_upload_images_menu_item'].'/thumnails/'.$thumb_img;
        if (!is_dir($tconfig['tsite_upload_images_menu_item_path'].'/thumnails/')) {
            mkdir($tconfig['tsite_upload_images_menu_item_path'].'/thumnails/', 0777);
            chmod($tconfig['tsite_upload_images_menu_item_path'].'/thumnails/', 0777);
        }
        if (file_exists($img_path)) {
            return $img_url;
        }

        require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
        $sec = 3;
        $vFile = $tconfig['tsite_upload_images_menu_item_path'].'/'.$video_file;
        if (file_exists($vFile)) {
            $thumb_video = $tmpArr[0].'.mp4';
            if ('mkv' === $tmpArr[1]) {
                if (!file_exists($tconfig['tsite_upload_images_menu_item_path'].'/'.$thumb_video)) {
                    $ffmpeg = FFMpeg\FFMpeg::create();
                    $video = $ffmpeg->open($vFile);
                    $format = new X264();
                    $format->setAudioCodec('libmp3lame');
                    $vFile = $tconfig['tsite_upload_images_menu_item_path'].'/'.$thumb_video;
                    $video->save($format, $vFile);
                } else {
                    $vFile = $tconfig['tsite_upload_images_menu_item_path'].'/'.$thumb_video;
                }
            }
            $ffprobe = FFProbe::create();
            $vDuration = $ffprobe->streams($vFile)->videos()->first()->get('duration');
            if ($vDuration < 3) {
                $sec = floor($vDuration);
            }
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($vFile);
            $frame = $video->frame(TimeCode::fromSeconds($sec));
            $frame->save($img_path);

            return $img_url;
        }

        return '';
    }
}
