<?php



/**
 * Resizes Animated GIF Files.
 *
 *	///IMPORTANT NOTE: The script needs a temporary directory where all the frames should be extracted.
 *	Create a directory with a 777 permission level and write the path into $temp_dir variable below.
 *
 *	Default directory is "frames".
 */
class gifImageResizer
{
    public $temp_dir = 'frames';
    private $pointer = 0;
    private $index = 0;
    private $globaldata = [];
    private $imagedata = [];
    private $imageinfo = [];
    private $handle = 0;
    private $orgvars = [];
    private $encdata = [];
    private $parsedfiles = [];
    private $originalwidth = 0;
    private $originalheight = 0;
    private $wr;
    private $hr;
    private $props = [];
    private $decoding = false;

    /**
     * Public part of the class.
     *
     * @orgfile - Original file path
     *
     * @newfile - New filename with path
     *
     * @width   - Desired image width
     *
     * @height  - Desired image height
     *
     * @param mixed $orgfile
     * @param mixed $newfile
     * @param mixed $width
     * @param mixed $height
     */
    public function resize($orgfile, $newfile, $width, $height): void
    {
        $this->decode($orgfile);
        $this->wr = $width / $this->originalwidth;
        $this->hr = $height / $this->originalheight;
        $this->resizeframes();
        $this->encode($newfile, $width, $height);
        $this->clearframes();
    }

    /**
     * GIF Decoder function.
     * Parses the GIF animation into single frames.
     *
     * @param mixed $filename
     */
    private function decode($filename): void
    {
        $this->decoding = true;
        $this->clearvariables();
        $this->loadfile($filename);
        $this->get_gif_header();
        $this->get_graphics_extension(0);
        $this->get_application_data();
        $this->get_application_data();
        $this->get_image_block(0);
        $this->get_graphics_extension(1);
        $this->get_comment_data();
        $this->get_application_data();
        $this->get_image_block(1);
        while (!$this->checkbyte(0x3B) && !$this->checkEOF()) {
            $this->get_comment_data(1);
            $this->get_graphics_extension(2);
            $this->get_image_block(2);
        }
        $this->writeframes(time());
        $this->closefile();
        $this->decoding = false;
    }

    /**
     * GIF Encoder function.
     * Combines the parsed GIF frames into one single animation.
     *
     * @param mixed $new_filename
     * @param mixed $newwidth
     * @param mixed $newheight
     */
    private function encode($new_filename, $newwidth, $newheight): void
    {
        $mystring = '';
        $this->pointer = 0;
        $this->imagedata = [];
        $this->imageinfo = [];
        $this->handle = 0;
        $this->index = 0;

        $k = 0;
        foreach ($this->parsedfiles as $imagepart) {
            $this->loadfile($imagepart);
            $this->get_gif_header();
            $this->get_application_data();
            $this->get_comment_data();
            $this->get_graphics_extension(0);
            $this->get_image_block(0);

            // get transparent color index and color
            if (isset($this->encdata[$this->index - 1])) {
                $gxdata = $this->encdata[$this->index - 1]['graphicsextension'];
            } else {
                $gxdata = null;
            }
            $ghdata = $this->imageinfo['gifheader'];
            $trcolor = '';
            $hastransparency = ($gxdata[3] && 1 === 1);

            if ($hastransparency) {
                $trcx = ord($gxdata[6]);
                $trcolor = substr($ghdata, 13 + $trcx * 3, 3);
            }

            // global color table to image data;
            $this->transfercolortable($this->imageinfo['gifheader'], $this->imagedata[$this->index - 1]['imagedata']);

            $imageblock = &$this->imagedata[$this->index - 1]['imagedata'];

            // if transparency exists transfer transparency index
            if ($hastransparency) {
                $haslocalcolortable = ((ord($imageblock[9]) & 128) === 128);
                if ($haslocalcolortable) {
                    // local table exists. determine boundaries and look for it.
                    $tablesize = (2 ** ((ord($imageblock[9]) & 7) + 1) * 3) + 10;
                    $this->orgvars[$this->index - 1]['transparent_color_index'] =
                    ((strrpos(substr($this->imagedata[$this->index - 1]['imagedata'], 0, $tablesize), $trcolor) - 10) / 3);
                } else {
                    // local table doesnt exist, look at the global one.
                    $tablesize = (2 ** ((ord($gxdata[10]) & 7) + 1) * 3) + 10;
                    $this->orgvars[$this->index - 1]['transparent_color_index'] =
                    ((strrpos(substr($ghdata, 0, $tablesize), $trcolor) - 10) / 3);
                }
            }

            // apply original delay time,transparent index and disposal values to graphics extension

            if (!$this->imagedata[$this->index - 1]['graphicsextension']) {
                $this->imagedata[$this->index - 1]['graphicsextension'] = chr(0x21).chr(0xF9).chr(0x04).chr(0x00).chr(0x00).chr(0x00).chr(0x00).chr(0x00);
            }

            $imagedata = &$this->imagedata[$this->index - 1]['graphicsextension'];

            $imagedata[3] = chr((ord($imagedata[3]) & 0xE3) | ($this->orgvars[$this->index - 1]['disposal_method'] << 2));
            $imagedata[4] = chr($this->orgvars[$this->index - 1]['delay_time'] % 256);
            $imagedata[5] = chr(floor($this->orgvars[$this->index - 1]['delay_time'] / 256));
            if ($hastransparency) {
                $imagedata[6] = chr($this->orgvars[$this->index - 1]['transparent_color_index']);
            }
            $imagedata[3] = chr(ord($imagedata[3]) | $hastransparency);

            // apply calculated left and top offset
            $imageblock[1] = chr(round(($this->orgvars[$this->index - 1]['offset_left'] * $this->wr) % 256));
            $imageblock[2] = chr(floor(($this->orgvars[$this->index - 1]['offset_left'] * $this->wr) / 256));
            $imageblock[3] = chr(round(($this->orgvars[$this->index - 1]['offset_top'] * $this->hr) % 256));
            $imageblock[4] = chr(floor(($this->orgvars[$this->index - 1]['offset_top'] * $this->hr) / 256));

            if (1 === $this->index) {
                if (!isset($this->imageinfo['applicationdata']) || !$this->imageinfo['applicationdata']) {
                    $this->imageinfo['applicationdata'] = chr(0x21).chr(0xFF).chr(0x0B).'NETSCAPE2.0'.chr(0x03).chr(0x01).chr(0x00).chr(0x00).chr(0x00);
                }
                if (!isset($this->imageinfo['commentdata']) || !$this->imageinfo['commentdata']) {
                    $this->imageinfo['commentdata'] = chr(0x21).chr(0xFE).chr(0x10).'PHPGIFRESIZER1.0'.chr(0);
                }
                $mystring .= $this->orgvars['gifheader'].$this->imageinfo['applicationdata'].$this->imageinfo['commentdata'];
                if (isset($this->orgvars['hasgx_type_0']) && $this->orgvars['hasgx_type_0']) {
                    $mystring .= $this->globaldata['graphicsextension_0'];
                }
                if (isset($this->orgvars['hasgx_type_1']) && $this->orgvars['hasgx_type_1']) {
                    $mystring .= $this->globaldata['graphicsextension'];
                }
            }

            $mystring .= $imagedata.$imageblock;
            ++$k;
            $this->closefile();
        }

        $mystring .= chr(0x3B);

        // applying new width & height to gif header
        $mystring[6] = chr($newwidth % 256);
        $mystring[7] = chr(floor($newwidth / 256));
        $mystring[8] = chr($newheight % 256);
        $mystring[9] = chr(floor($newheight / 256));
        $mystring[11] = $this->orgvars['background_color'];
        // if(file_exists($new_filename)){unlink($new_filename);}
        file_put_contents($new_filename, $mystring);
    }

    /**
     * Variable Reset function
     * If a instance is used multiple times, it's needed. Trust me.
     */
    private function clearvariables(): void
    {
        $this->pointer = 0;
        $this->index = 0;
        $this->imagedata = [];
        $this->imageinfo = [];
        $this->handle = 0;
        $this->parsedfiles = [];
    }

    /**
     * Clear Frames function
     * For deleting the frames after encoding.
     */
    private function clearframes(): void
    {
        foreach ($this->parsedfiles as $temp_frame) {
            unlink($temp_frame);
        }
    }

    /**
     * Frame Writer
     * Writes the GIF frames into files.
     *
     * @param mixed $prepend
     */
    private function writeframes($prepend): void
    {
        for ($i = 0; $i < count($this->imagedata); ++$i) {
            file_put_contents($this->temp_dir.'/frame_'.$prepend.'_'.str_pad($i, 2, '0', STR_PAD_LEFT).'.gif', $this->imageinfo['gifheader'].$this->imagedata[$i]['graphicsextension'].$this->imagedata[$i]['imagedata'].chr(0x3B));
            $this->parsedfiles[] = $this->temp_dir.'/frame_'.$prepend.'_'.str_pad($i, 2, '0', STR_PAD_LEFT).'.gif';
        }
    }

    /**
     * Color Palette Transfer Device
     * Transferring Global Color Table (GCT) from frames into Local Color Tables in animation.
     *
     * @param mixed $src
     * @param mixed $dst
     */
    private function transfercolortable($src, &$dst): void
    {
        // src is gif header,dst is image data block
        // if global color table exists,transfer it
        if ((ord($src[10]) & 128) === 128) {
            // Gif Header Global Color Table Length
            $ghctl = 2 ** ($this->readbits(ord($src[10]), 5, 3) + 1) * 3;
            // cut global color table from gif header
            $ghgct = substr($src, 13, $ghctl);
            // check image block color table length
            if ((ord($dst[9]) & 128) === 128) {
                // Image data contains color table. skip.
            } else {
                // Image data needs a color table.
                // get last color table length so we can truncate the dummy color table
                $idctl = 2 ** ($this->readbits(ord($dst[9]), 5, 3) + 1) * 3;
                // set color table flag and length
                $dst[9] = chr(ord($dst[9]) | (0x80 | (log($ghctl / 3, 2) - 1)));
                // inject color table
                $dst = substr($dst, 0, 10).$ghgct.substr($dst, -1 * strlen($dst) + 10);
            }
        }
        // global color table doesn't exist. skip.
    }

    /**
     * GIF Parser Functions.
     * Below functions are the main structure parser components.
     */
    private function get_gif_header(): void
    {
        $this->p_forward(10);
        if (1 === $this->readbits($mybyte = $this->readbyte_int(), 0, 1)) {
            $this->p_forward(2);
            $this->p_forward(2 ** ($this->readbits($mybyte, 5, 3) + 1) * 3);
        } else {
            $this->p_forward(2);
        }

        $this->imageinfo['gifheader'] = $this->datapart(0, $this->pointer);
        if ($this->decoding) {
            $this->orgvars['gifheader'] = $this->imageinfo['gifheader'];
            $this->originalwidth = ord($this->orgvars['gifheader'][7]) * 256 + ord($this->orgvars['gifheader'][6]);
            $this->originalheight = ord($this->orgvars['gifheader'][9]) * 256 + ord($this->orgvars['gifheader'][8]);
            $this->orgvars['background_color'] = $this->orgvars['gifheader'][11];
        }
    }

    // -------------------------------------------------------
    private function get_application_data(): void
    {
        $startdata = $this->readbyte(2);
        if ($startdata === chr(0x21).chr(0xFF)) {
            $start = $this->pointer - 2;
            $this->p_forward($this->readbyte_int());
            $this->read_data_stream($this->readbyte_int());
            $this->imageinfo['applicationdata'] = $this->datapart($start, $this->pointer - $start);
        } else {
            $this->p_rewind(2);
        }
    }

    // -------------------------------------------------------
    private function get_comment_data(): void
    {
        $startdata = $this->readbyte(2);
        if ($startdata === chr(0x21).chr(0xFE)) {
            $start = $this->pointer - 2;
            $this->read_data_stream($this->readbyte_int());
            $this->imageinfo['commentdata'] = $this->datapart($start, $this->pointer - $start);
        } else {
            $this->p_rewind(2);
        }
    }

    // -------------------------------------------------------
    private function get_graphics_extension($type): void
    {
        $startdata = $this->readbyte(2);
        if ($startdata === chr(0x21).chr(0xF9)) {
            $start = $this->pointer - 2;
            $this->p_forward($this->readbyte_int());
            $this->p_forward(1);
            if (2 === $type) {
                $this->imagedata[$this->index]['graphicsextension'] = $this->datapart($start, $this->pointer - $start);
            } elseif (1 === $type) {
                $this->orgvars['hasgx_type_1'] = 1;
                $this->globaldata['graphicsextension'] = $this->datapart($start, $this->pointer - $start);
            } elseif (0 === $type && false === $this->decoding) {
                $this->encdata[$this->index]['graphicsextension'] = $this->datapart($start, $this->pointer - $start);
            } elseif (0 === $type && true === $this->decoding) {
                $this->orgvars['hasgx_type_0'] = 1;
                $this->globaldata['graphicsextension_0'] = $this->datapart($start, $this->pointer - $start);
            }
        } else {
            $this->p_rewind(2);
        }
    }

    // -------------------------------------------------------
    private function get_image_block($type): void
    {
        if ($this->checkbyte(0x2C)) {
            $start = $this->pointer;
            $this->p_forward(9);
            if (1 === $this->readbits($mybyte = $this->readbyte_int(), 0, 1)) {
                $this->p_forward(2 ** ($this->readbits($mybyte, 5, 3) + 1) * 3);
            }
            $this->p_forward(1);
            $this->read_data_stream($this->readbyte_int());
            $this->imagedata[$this->index]['imagedata'] = $this->datapart($start, $this->pointer - $start);

            if (0 === $type) {
                $this->orgvars['hasgx_type_0'] = 0;
                if (isset($this->globaldata['graphicsextension_0'])) {
                    $this->imagedata[$this->index]['graphicsextension'] = $this->globaldata['graphicsextension_0'];
                } else {
                    $this->imagedata[$this->index]['graphicsextension'] = null;
                }
                unset($this->globaldata['graphicsextension_0']);
            } elseif (1 === $type) {
                if (isset($this->orgvars['hasgx_type_1']) && 1 === $this->orgvars['hasgx_type_1']) {
                    $this->orgvars['hasgx_type_1'] = 0;
                    $this->imagedata[$this->index]['graphicsextension'] = $this->globaldata['graphicsextension'];
                    unset($this->globaldata['graphicsextension']);
                } else {
                    $this->orgvars['hasgx_type_0'] = 0;
                    $this->imagedata[$this->index]['graphicsextension'] = $this->globaldata['graphicsextension_0'];
                    unset($this->globaldata['graphicsextension_0']);
                }
            }

            $this->parse_image_data();
            ++$this->index;
        }
    }

    // -------------------------------------------------------
    private function parse_image_data(): void
    {
        $this->imagedata[$this->index]['disposal_method'] = $this->get_imagedata_bit('ext', 3, 3, 3);
        $this->imagedata[$this->index]['user_input_flag'] = $this->get_imagedata_bit('ext', 3, 6, 1);
        $this->imagedata[$this->index]['transparent_color_flag'] = $this->get_imagedata_bit('ext', 3, 7, 1);
        $this->imagedata[$this->index]['delay_time'] = $this->dualbyteval($this->get_imagedata_byte('ext', 4, 2));
        $this->imagedata[$this->index]['transparent_color_index'] = ord($this->get_imagedata_byte('ext', 6, 1));
        $this->imagedata[$this->index]['offset_left'] = $this->dualbyteval($this->get_imagedata_byte('dat', 1, 2));
        $this->imagedata[$this->index]['offset_top'] = $this->dualbyteval($this->get_imagedata_byte('dat', 3, 2));
        $this->imagedata[$this->index]['width'] = $this->dualbyteval($this->get_imagedata_byte('dat', 5, 2));
        $this->imagedata[$this->index]['height'] = $this->dualbyteval($this->get_imagedata_byte('dat', 7, 2));
        $this->imagedata[$this->index]['local_color_table_flag'] = $this->get_imagedata_bit('dat', 9, 0, 1);
        $this->imagedata[$this->index]['interlace_flag'] = $this->get_imagedata_bit('dat', 9, 1, 1);
        $this->imagedata[$this->index]['sort_flag'] = $this->get_imagedata_bit('dat', 9, 2, 1);
        $this->imagedata[$this->index]['color_table_size'] = 2 ** ($this->get_imagedata_bit('dat', 9, 5, 3) + 1) * 3;
        $this->imagedata[$this->index]['color_table'] = substr($this->imagedata[$this->index]['imagedata'], 10, $this->imagedata[$this->index]['color_table_size']);
        $this->imagedata[$this->index]['lzw_code_size'] = ord($this->get_imagedata_byte('dat', 10, 1));
        if ($this->decoding) {
            $this->orgvars[$this->index]['transparent_color_flag'] = $this->imagedata[$this->index]['transparent_color_flag'];
            $this->orgvars[$this->index]['transparent_color_index'] = $this->imagedata[$this->index]['transparent_color_index'];
            $this->orgvars[$this->index]['delay_time'] = $this->imagedata[$this->index]['delay_time'];
            $this->orgvars[$this->index]['disposal_method'] = $this->imagedata[$this->index]['disposal_method'];
            $this->orgvars[$this->index]['offset_left'] = $this->imagedata[$this->index]['offset_left'];
            $this->orgvars[$this->index]['offset_top'] = $this->imagedata[$this->index]['offset_top'];
        }
    }

    // -------------------------------------------------------
    private function get_imagedata_byte($type, $start, $length)
    {
        if ('ext' === $type) {
            return substr($this->imagedata[$this->index]['graphicsextension'], $start, $length);
        }
        if ('dat' === $type) {
            return substr($this->imagedata[$this->index]['imagedata'], $start, $length);
        }
    }

    // -------------------------------------------------------
    private function get_imagedata_bit($type, $byteindex, $bitstart, $bitlength)
    {
        if ('ext' === $type) {
            return $this->readbits(ord(substr($this->imagedata[$this->index]['graphicsextension'], $byteindex, 1)), $bitstart, $bitlength);
        }
        if ('dat' === $type) {
            return $this->readbits(ord(substr($this->imagedata[$this->index]['imagedata'], $byteindex, 1)), $bitstart, $bitlength);
        }
    }

    // -------------------------------------------------------
    private function dualbyteval($s)
    {
        return ord($s[1]) * 256 + ord($s[0]);
    }

    // ------------   Helper Functions ---------------------
    private function read_data_stream($first_length)
    {
        $this->p_forward($first_length);
        $length = $this->readbyte_int();
        if (0 !== $length) {
            while (0 !== $length) {
                $this->p_forward($length);
                $length = $this->readbyte_int();
            }
        }

        return true;
    }

    // -------------------------------------------------------
    private function loadfile($filename): void
    {
        $this->handle = fopen($filename, 'r');
        $this->pointer = 0;
    }

    // -------------------------------------------------------
    private function closefile(): void
    {
        fclose($this->handle);
        $this->handle = 0;
    }

    // -------------------------------------------------------
    private function readbyte($byte_count)
    {
        $data = fread($this->handle, $byte_count);
        $this->pointer += $byte_count;

        return $data;
    }

    // -------------------------------------------------------
    private function readbyte_int()
    {
        $data = fread($this->handle, 1);
        ++$this->pointer;

        return ord($data);
    }

    // -------------------------------------------------------
    private function readbits($byte, $start, $length)
    {
        $bin = str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        $data = substr($bin, $start, $length);

        return bindec($data);
    }

    // -------------------------------------------------------
    private function p_rewind($length): void
    {
        $this->pointer -= $length;
        fseek($this->handle, $this->pointer);
    }

    // -------------------------------------------------------
    private function p_forward($length): void
    {
        $this->pointer += $length;
        fseek($this->handle, $this->pointer);
    }

    // -------------------------------------------------------
    private function datapart($start, $length)
    {
        fseek($this->handle, $start);
        $data = fread($this->handle, $length);
        fseek($this->handle, $this->pointer);

        return $data;
    }

    // -------------------------------------------------------
    private function checkbyte($byte)
    {
        if (fgetc($this->handle) === chr($byte)) {
            fseek($this->handle, $this->pointer);

            return true;
        }
        fseek($this->handle, $this->pointer);

        return false;
    }

    // -------------------------------------------------------
    private function checkEOF()
    {
        if (false === fgetc($this->handle)) {
            return true;
        }
        fseek($this->handle, $this->pointer);

        return false;
    }

    // -------------------------------------------------------
    /**
     * Debug Functions.
     * Parses the GIF animation into single frames.
     *
     * @param mixed $string
     */
    private function debug($string): void
    {
        echo '<pre>';
        for ($i = 0; $i < strlen($string); ++$i) {
            echo str_pad(dechex(ord($string[$i])), 2, '0', STR_PAD_LEFT).' ';
        }
        echo '</pre>';
    }

    // -------------------------------------------------------
    private function debuglen($var, $len): void
    {
        echo '<pre>';
        for ($i = 0; $i < $len; ++$i) {
            echo str_pad(dechex(ord($var[$i])), 2, '0', STR_PAD_LEFT).' ';
        }
        echo '</pre>';
    }

    // -------------------------------------------------------
    private function debugstream($length): void
    {
        $this->debug($this->datapart($this->pointer, $length));
    }

    // -------------------------------------------------------
    /**
     * GD Resizer Device
     * Resizes the animation frames.
     */
    private function resizeframes(): void
    {
        $k = 0;
        foreach ($this->parsedfiles as $img) {
            $src = imagecreatefromgif($img);
            $sw = $this->imagedata[$k]['width'];
            $sh = $this->imagedata[$k]['height'];
            $nw = round($sw * $this->wr);
            $nh = round($sh * $this->hr);
            $sprite = imagecreatetruecolor($nw, $nh);
            $trans = imagecolortransparent($sprite);
            imagealphablending($sprite, false);
            imagesavealpha($sprite, true);
            imagepalettecopy($sprite, $src);
            imagefill($sprite, 0, 0, imagecolortransparent($src));
            imagecolortransparent($sprite, imagecolortransparent($src));
            imagecopyresized($sprite, $src, 0, 0, 0, 0, $nw, $nh, $sw, $sh);
            imagegif($sprite, $img);
            imagedestroy($sprite);
            imagedestroy($src);
            ++$k;
        }
    }
}
