<?php
class thumbnail
{
    public $img;

    public function createthumbnail($imgfile="")
    {
        //detect image format
        $this->img["format"]=preg_replace('/.*\.(.*)$/', "\\1", $imgfile);
        $this->img["format"]=strtoupper($this->img["format"]);
        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
            //JPEG
            $this->img["format"]="JPEG";
            $this->img["src"] = ImageCreateFromJPEG($imgfile);
      
            if($this->img["src"] == "") {
                $this->img["src"] = ImageCreateFromPNG($imgfile);
            }
      
        } elseif ($this->img["format"]=="PNG") {
            //PNG
            $this->img["format"]="PNG";
            $this->img["src"] = ImageCreateFromPNG($imgfile);
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            $this->img["format"]="GIF";
            $this->img["src"] = ImageCreateFromGIF($imgfile);
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            $this->img["format"]="WBMP";
            $this->img["src"] = ImageCreateFromWBMP($imgfile);
        } else {
            //DEFAULT
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = "File is not supported";
            $previous = "javascript:history.go(-1)";
            if(isset($_SERVER['HTTP_REFERER'])) {
                $previous = $_SERVER['HTTP_REFERER'];
            }
            header("Location:" . $previous);
            exit();

            /*echo "Not Supported File";
            exit();*/
        }
        @$this->img["lebar"] = imagesx($this->img["src"]);
        @$this->img["tinggi"] = imagesy($this->img["src"]);
        //default quality jpeg
        $this->img["quality"]=75;
    }

    public function size_height($size=100)
    {
        //height
        $this->img["tinggi_thumb"]=$size;
        @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
    }

    public function size_width($size=100)
    {
        //width
        $this->img["lebar_thumb"]=$size;
        @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
    }

    public function size_auto($size=100)
    {
        //size
        if ($this->img["lebar"]>=$this->img["tinggi"]) {
            $this->img["lebar_thumb"]=$size;
            @$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
        } else {
            $this->img["tinggi_thumb"]=$size;
            @$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
        }
    }

    public function jpeg_quality($quality=75)
    {
        //jpeg quality
        $this->img["quality"]=$quality;
    }

    public function show()
    {
        //show thumb
        @Header("Content-Type: image/".$this->img["format"]);

        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        @imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
            //JPEG
            imageJPEG($this->img["des"], "", $this->img["quality"]);
        } elseif ($this->img["format"]=="PNG") {
            //PNG
            imagePNG($this->img["des"]);
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            imageGIF($this->img["des"]);
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            imageWBMP($this->img["des"]);
        }
    }

    public function save($save="")
    {
        //save thumb
        if (empty($save)) {
            $save=strtolower("./thumb.".$this->img["format"]);
        }
        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
        @$this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        @imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG" || $this->img["format"]=="jpg") {
            //JPEG
            imageJPEG($this->img["des"], "$save", $this->img["quality"]);
        } elseif ($this->img["format"]=="PNG") {
            //PNG
            $im = $this->img["des"];
            $red = @imagecolorallocate($im, 255, 0, 0);
            $black = @imagecolorallocate($im, 0, 0, 0);
            // Make the background transparent
            @imagecolortransparent($im, $black);
            @imagePNG($im, "$save");
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            @imageGIF($this->img["des"], "$save");
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            @imageWBMP($this->img["des"], "$save");
        }
    }
    public function save_pngs($save="", $main_path="", $nsize="", $osize="")
    {
        //save thumb
        if (empty($save)) {
            $save=strtolower("./thumb.".$this->img["format"]);
        }
        /* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
        $this->img["des"] = ImageCreateTrueColor($this->img["lebar_thumb"], $this->img["tinggi_thumb"]);
        @imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);

        if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG" || $this->img["format"]=="jpg") {
            //JPEG
            imageJPEG($this->img["des"], "$save", $this->img["quality"]);
        } elseif ($this->img["format"]=="PNG") {
            //PNG
            
            $im = imagecreatefrompng($main_path);
            $srcWidth = imagesx($im);
            $srcHeight = imagesy($im);

            $nWidth = $nsize;
            $nHeight = $nsize;

            $newImg = imagecreatetruecolor($nWidth, $nHeight);
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
            imagecopyresampled(
                $newImg,
                $im,
                0,
                0,
                0,
                0,
                $nWidth,
                $nHeight,
                $srcWidth,
                $srcHeight
            );
            imagepng($newImg, "$save");
            // $im = $this->img["des"];
            // $red = imagecolorallocate($im, 255, 0, 0);
            // $black = imagecolorallocate($im, 0, 0, 0);
            //Make the background transparent
            // imagecolortransparent($im, $black);
            // imagePNG($im,"$save");
        } elseif ($this->img["format"]=="GIF") {
            //GIF
            imageGIF($this->img["des"], "$save");
        } elseif ($this->img["format"]=="WBMP") {
            //WBMP
            imageWBMP($this->img["des"], "$save");
        }
    }
}
