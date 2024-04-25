<?php



namespace Kesk\Web\Common;

class UploadFile
{
    public function __construct() {}

    public function UploadImage($photopath, $vphoto, $vphoto_name, $prefix = '', $vaildExt = 'gif,jpg,jpeg,bmp,png')
    {
        global $langage_lbl;
        $msg = '';
        if (!empty($vphoto_name) && is_file($vphoto)) {
            $vphoto_name = replace_content($vphoto_name);
            $tmp = explode('.', $vphoto_name);
            for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode('_', $tmp1);
            $ext = $tmp[\count($tmp) - 1];
            $vaildExt_arr = explode(',', strtoupper($vaildExt));
            if (\in_array(strtoupper($ext), $vaildExt_arr, true)) {
                $vphotofile = $file.'_'.date('YmdHis').'.'.$ext;
                $ftppath1 = $photopath.$vphotofile;
                if (!copy($vphoto, $ftppath1)) {
                    $vphotofile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                } else {
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                }
            } else {
                $vphotofile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'].$vaildExt.'!!!';
            }
        }
        $ret[0] = $vphotofile;
        $ret[1] = $msg;

        return $ret;
    }

    public function GeneralUploadImage($temp_name, $image_name, $path, $size1, $size2, $size3, $size4, $option, $modulename, $original, $size5, $temp_gallery)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        $thumb = new thumbnail();
        $time_val = time();
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = random_int(11_111, 99_999);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            if ('menu' === $option && '' !== $option) {
                [$width, $height] = getimagesize($temp_gallery.'/'.$vImage_name1);
                $size3 = $width;
            }
            if ('Y' === $original || 'y' === $original) {
                copy($temp_gallery.'/'.$vImage_name1, $path.$time_val.'_'.$filename.'.'.$fileextension);
            }
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size1);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'1'.'_'.$time_val.'_'.$filename.'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size2);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'2'.'_'.$time_val.'_'.$filename.'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size3);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'3'.'_'.$time_val.'_'.$filename.'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size5);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'5'.'_'.$time_val.'_'.$filename.'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size4);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'4'.'_'.$time_val.'_'.$filename.'.'.$fileextension);
            @unlink($temp_gallery.'/'.$vImage_name1);
            @unlink($path.$old_image1);
            @unlink($path.'1_'.$old_image1);
            @unlink($path.'2_'.$old_image1);
            @unlink($path.'3_'.$old_image1);
            @unlink($path.'4_'.$old_image1);
            @unlink($path.'5_'.$old_image1);
            $vImage1 = $time_val.'_'.$filename.'.'.$fileextension;

            return $vImage1;
        }

        return $old_image1;
    }

    public function GeneralUploadImageService($temp_name, $image_name, $path, $size1, $size2, $size3, $size4, $option, $modulename, $original, $size5, $temp_gallery)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        $thumb = new thumbnail();
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            if ('menu' === $option && '' !== $option) {
                [$width, $height] = getimagesize($temp_gallery.'/'.$vImage_name1);
                $size3 = $width;
            }
            if ('Y' === $original || 'y' === $original) {
                copy($temp_gallery.'/'.$vImage_name1, $path.'org_'.$filename.'_'.date('YmdHis').'.'.$fileextension);
            }
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size1);
            $thumb->jpeg_quality(100);
            $thumb->save($path.$filename.'_'.date('YmdHis').'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size2);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'2_'.$filename.'_'.date('YmdHis').'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size3);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'3_'.$filename.'_'.date('YmdHis').'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size5);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'5_'.$filename.'_'.date('YmdHis').'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size4);
            $thumb->jpeg_quality(100);
            $thumb->save($path.'4_'.$filename.'_'.date('YmdHis').'.'.$fileextension);
            @unlink($temp_gallery.'/'.$vImage_name1);
            @unlink($path.$old_image1);
            @unlink($path.'1_'.$old_image1);
            @unlink($path.'2_'.$old_image1);
            @unlink($path.'3_'.$old_image1);
            @unlink($path.'4_'.$old_image1);
            @unlink($path.'5_'.$old_image1);
            $vImage1 = $filename.'_'.date('YmdHis').'.'.$fileextension;

            return $vImage1;
        }

        return $old_image1;
    }

    public function GeneralFileUpload($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = 'mp3,wav')
    {
        global $langage_lbl;
        $imgExt_arr = ['JPG', 'JPEG', 'PNG', 'BMP', 'GIF', 'HEIC'];
        $msg = '';
        if (!empty($vfile_name) && is_file($vfile)) {
            $vfile_name = replace_content($vfile_name);
            $tmp = explode('.', $vfile_name);
            for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode('_', $tmp1);
            $ext = $tmp[\count($tmp) - 1];
            $vaildExt_arr = explode(',', strtoupper($vaildExt));
            $vaildExt_arr = array_map('trim', $vaildExt_arr);
            if (\in_array(strtoupper($ext), $vaildExt_arr, true)) {
                if (\in_array(strtoupper($ext), $imgExt_arr, true)) {
                    ExifCleaning::adjustImageOrientation($vfile);
                }
                $random = substr(random_int(0, getrandmax()), 0, 3);
                $vfilefile = date('YmdHis').$random.'.'.$ext;
                $ftppath1 = $filepath.$vfilefile;
                if (!copy($vfile, $ftppath1)) {
                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = '1';
                } else {
                    @chmod($vfile, 0777);
                    @chmod($ftppath1.'/'.$vfile, 0777);
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = '0';
                }
            } else {
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'].$vaildExt.'!!!';
                $errorflag = '1';
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;

        return $ret;
    }

    public function GeneralFileUploadHome($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = 'mp3,wav', $vCode = 'EN')
    {
        global $langage_lbl;
        $msg = '';
        if (!empty($vfile_name) && is_file($vfile)) {
            $vfile_name = replace_content($vfile_name);
            $tmp = explode('.', $vfile_name);
            for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode('_', $tmp1);
            $ext = $tmp[\count($tmp) - 1];
            $vaildExt_arr = explode(',', strtoupper($vaildExt));
            if (\in_array(strtoupper($ext), $vaildExt_arr, true)) {
                $vfilefile = $file.'_'.$vCode.'.'.$ext;
                $ftppath1 = $filepath.$vfilefile;
                if (!copy($vfile, $ftppath1)) {
                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = '1';
                } else {
                    @chmod($vfile, 0777);
                    @chmod($ftppath1.'/'.$vfile, 0777);
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = '0';
                }
            } else {
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'].$vaildExt.'!!!';
                $errorflag = '1';
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;

        return $ret;
    }

    public function img_data_upload($temp_gallery, $vImage_name1, $path, $size1, $size2, $size3, $size4)
    {
        global $thumb;
        $vImage_name1 = replace_content($vImage_name1);
        $filename = $vImage_name1;
        $time_val = time();
        $img_arr = explode('.', $vImage_name1);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
        $thumb->size_auto((int) $size1);
        $thumb->jpeg_quality(100);
        $thumb->save($path.'1'.'_'.$filename);
        $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
        $thumb->size_auto((int) $size2);
        $thumb->jpeg_quality(100);
        $thumb->save($path.'2'.'_'.$filename);
        $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
        $thumb->size_auto((int) $size3);
        $thumb->jpeg_quality(100);
        $thumb->save($path.'3'.'_'.$filename);
        $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
        $thumb->size_auto((int) $size4);
        $thumb->jpeg_quality(100);
        $thumb->save($path.'4'.'_'.$filename);
        $vImage1 = $filename;

        return $vImage1;
    }

    public function uploadImagesOrFiles($temp_name, $image_name, $path, $size1, $size2, $size3, $size4, $option, $modulename, $original, $size5, $temp_gallery, $vehicle_type, $hover)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        global $currrent_upload_time;
        if ('' !== $currrent_upload_time) {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }
        $time_val = $time_val.'_'.random_int(11_111, 99_999);
        $thumb = new thumbnail();
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = random_int(11_111, 99_999);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            if ('menu' === $option && '' !== $option) {
                [$width, $height] = getimagesize($temp_gallery.'/'.$vImage_name1);
                $size3 = $width;
            }
            if ('Y' === $original || 'y' === $original) {
                copy($temp_gallery.'/'.$vImage_name1, $path.$time_val.'.'.$fileextension);
            }
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size1);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'mdpi'.'_'.$hover.$time_val.'.'.$fileextension, $path.$time_val.'.'.$fileextension, $size1, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size2);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'hdpi'.'_'.$hover.$time_val.'.'.$fileextension, $path.$time_val.'.'.$fileextension, $size2, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size3);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xhdpi'.'_'.$hover.$time_val.'.'.$fileextension, $path.$time_val.'.'.$fileextension, $size3, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size5);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xxxhdpi'.'_'.$hover.$time_val.'.'.$fileextension, $path.$time_val.'.'.$fileextension, $size5, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size4);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xxhdpi'.'_'.$hover.$time_val.'.'.$fileextension, $path.$time_val.'.'.$fileextension, $size4, '360');
            $vImage1 = $time_val.'.'.$fileextension;
            @unlink($temp_gallery.'/'.$vImage_name1);
            @unlink($path.$old_image1);
            @unlink($path.'1_'.$old_image1);
            @unlink($path.'2_'.$old_image1);
            @unlink($path.'3_'.$old_image1);
            @unlink($path.'4_'.$old_image1);
            @unlink($path.'5_'.$old_image1);

            return $vImage1;
        }

        return $old_image1;
    }

    public function GeneralImageUploadVehicleCategoryAndroid($temp_name, $image_name, $path, $size1, $size2, $size3, $size4, $option, $modulename, $original, $size5, $temp_gallery, $vehicle_type, $hover)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        global $currrent_upload_time;
        if ('' !== $currrent_upload_time) {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }
        $thumb = new thumbnail();
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = random_int(11_111, 99_999);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            if ('menu' === $option && '' !== $option) {
                [$width, $height] = getimagesize($temp_gallery.'/'.$vImage_name1);
                $size3 = $width;
            }
            if ('Y' === $original || 'y' === $original) {
                copy($temp_gallery.'/'.$vImage_name1, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension);
            }
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size1);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'mdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size1, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size2);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'hdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size2, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size3);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xhdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size3, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size5);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xxxhdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size5, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size4);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'xxhdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size4, '360');
            $vImage1 = $time_val.'_'.$filename.'.'.$fileextension;
            @unlink($temp_gallery.'/'.$vImage_name1);
            @unlink($path.$old_image1);
            @unlink($path.'1_'.$old_image1);
            @unlink($path.'2_'.$old_image1);
            @unlink($path.'3_'.$old_image1);
            @unlink($path.'4_'.$old_image1);
            @unlink($path.'5_'.$old_image1);

            return $vImage1;
        }

        return $old_image1;
    }

    public function GeneralImageUploadVehicleCategoryIOS($temp_name, $image_name, $path, $size1, $size2, $size3, $size4, $option, $modulename, $original, $size5, $temp_gallery, $vehicle_type, $hover)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        $thumb = new thumbnail();
        global $currrent_upload_time;
        if ('' !== $currrent_upload_time) {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = random_int(11_111, 99_999);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            if ('menu' === $option && '' !== $option) {
                [$width, $height] = getimagesize($temp_gallery.'/'.$vImage_name1);
                $size3 = $width;
            }
            if ('Y' === $original || 'y' === $original) {
                copy($temp_gallery.'/'.$vImage_name1, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension);
            }
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size1);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'mdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size1, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size2);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'hdpi'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size2, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size3);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'1x'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size3, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size5);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'3x'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size5, '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($size4);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path.'2x'.'_'.$hover.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $path.'ic_car_'.$vehicle_type.'_'.$time_val.'.'.$fileextension, $size4, '360');
            $vImage1 = $time_val.'_'.$filename.'.'.$fileextension;
            @unlink($temp_gallery.'/'.$vImage_name1);
            @unlink($path.$old_image1);
            @unlink($path.'1_'.$old_image1);
            @unlink($path.'2_'.$old_image1);
            @unlink($path.'3_'.$old_image1);
            @unlink($path.'4_'.$old_image1);
            @unlink($path.'5_'.$old_image1);

            return $vImage1;
        }

        return $old_image1;
    }

    public function GeneralImageUploadVehicleType($vehicleid, $image_name, $temp_name, $old_image_name)
    {
        include_once TPATH_CLASS.'Imagecrop.class.php';
        $thumb = new thumbnail();
        global $currrent_upload_time, $tconfig;
        if ('' !== $currrent_upload_time) {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }
        $vImage1 = $temp_name;
        $img_path = $tconfig['tsite_upload_images_vehicle_type_path'];
        $temp_gallery = $img_path.'/'.$vehicleid;
        if ('' !== $vehicleid) {
            $check_file['vLogo'] = $old_image_name;
            $android_path = $img_path.'/'.$vehicleid.'/android';
            $ios_path = $img_path.'/'.$vehicleid.'/ios';
            if ('' !== $check_file['vLogo'] && file_exists($check_file['vLogo'])) {
                @unlink($android_path.'/'.$old_image_name);
                @unlink($android_path.'/mdpi_'.$old_image_name);
                @unlink($android_path.'/hdpi_'.$old_image_name);
                @unlink($android_path.'/xhdpi_'.$old_image_name);
                @unlink($android_path.'/xxhdpi_'.$old_image_name);
                @unlink($android_path.'/xxxhdpi_'.$old_image_name);
                @unlink($ios_path.'/'.$old_image_name);
                @unlink($ios_path.'/1x_'.$old_image_name);
                @unlink($ios_path.'/2x_'.$old_image_name);
                @unlink($ios_path.'/3x_'.$old_image_name);
            }
        }
        $Photo_Gallery_folder = $img_path.'/'.$vehicleid.'/';
        $Photo_Gallery_folder_android = $Photo_Gallery_folder.'android/';
        $Photo_Gallery_folder_ios = $Photo_Gallery_folder.'ios/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            mkdir($Photo_Gallery_folder_android, 0777);
            mkdir($Photo_Gallery_folder_ios, 0777);
        }
        $vImage_name1 = str_replace(' ', '_', trim($image_name));
        $img_arr = explode('.', $vImage_name1);
        if ('' === $modulename) {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = random_int(11_111, 99_999);
        $fileextension = strtolower($img_arr[\count($img_arr) - 1]);
        if ('' !== $vImage1) {
            copy($vImage1, $temp_gallery.'/'.$vImage_name1);
            copy($temp_gallery.'/'.$vImage_name1, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension);
            copy($temp_gallery.'/'.$vImage_name1, $Photo_Gallery_folder_ios.$time_val.'.'.$fileextension);
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size1_android']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android.'mdpi'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size1_android'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size2_android']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android.'hdpi'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size2_android'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size3_both']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android.'xhdpi'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size3_both'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size4_android']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android.'xxhdpi'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size4_android'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size5_both']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android.'xxxhdpi'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_android.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size5_both'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size3_both']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios.'1x'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_ios.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size3_both'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size5_both']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios.'2x'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_ios.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size5_both'], '360');
            $thumb->createthumbnail($temp_gallery.'/'.$vImage_name1);
            $thumb->size_auto($tconfig['tsite_upload_images_vehicle_type_size5_ios']);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios.'3x'.'_'.$time_val.'.'.$fileextension, $Photo_Gallery_folder_ios.$time_val.'.'.$fileextension, $tconfig['tsite_upload_images_vehicle_type_size5_ios'], '360');
            @unlink($temp_gallery.'/'.$vImage_name1);
            $vImage1 = $time_val.'_'.$filename.'.'.$fileextension;

            return $vImage1;
        }

        return $old_image1;
    }

    public function UploadFileHome($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = 'mp3,wav', $vCode = 'EN')
    {
        global $currrent_upload_time, $langage_lbl;
        $msg = '';
        if ('' !== $currrent_upload_time) {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }
        if (!empty($vfile_name) && is_file($vfile)) {
            $vfile_name = replace_content($vfile_name);
            $tmp = explode('.', $vfile_name);
            for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode('_', $tmp1);
            $ext = $tmp[\count($tmp) - 1];
            $vaildExt_arr = explode(',', strtoupper($vaildExt));
            if (\in_array(strtoupper($ext), $vaildExt_arr, true)) {
                $vfilefile = $file.$time_val.'.'.$ext;
                $ftppath1 = $filepath.$vfilefile;
                if (!copy($vfile, $ftppath1)) {
                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = '1';
                } else {
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = '0';
                }
            } else {
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'].$vaildExt.'!!!';
                $errorflag = '1';
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;

        return $ret;
    }

    public function GetFileExtension($file_name)
    {
        $filecheck = basename($file_name);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $check_ext = 'is_file';
        } else {
            $check_ext = 'is_image';
        }

        return $check_ext;
    }
}
