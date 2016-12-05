<?php

//require_once 'Common.inc.php';

class WebBaseImage {

    var $error_no = 0;
    var $error_msg = '';
    var $images_dir = 'pub';
    var $data_dir = '';
    var $bgcolor = '';
    static $type_maping = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png');

    /*  function __construct($bgcolor='') {
      $this->UploadFile($bgcolor);
      } */

    function WebBaseImage($bgcolor = '') {
        if ($bgcolor) {
            $this->bgcolor = $bgcolor;
        } else {
            $this->bgcolor = "#FFFFFF";
        }
    }

//
//    /**
//     * 图片上传的处理函数
//     *
//     * @access      public
//     * @param       array       upload       包含上传的图片文件信息的数组
//     * @param       array       dir          文件要上传在$this->data_dir下的目录名。如果为空图片放在则在$this->images_dir下以当月命名的目录下
//     * @param       array       img_name     上传图片名称，为空则随机生成
//     * @return      mix         如果成功则返回文件名，否则返回false
//     */
//    function upload_image($upload, $dir = '', $img_name = '') {
//        if ((isset($upload['error']) && $upload['error'] == 0) || (!isset($upload['error']) && isset($upload['tmp_name']) && $upload['tmp_name'] != 'none')) {
//            /* 没有指定目录默认为根目录images */
//            if (empty($dir)) {
//                /* 创建当月目录 */
//                $dir = date('Ym');
//                $dir = ROOT . $this->images_dir . '/' . $dir . '/';
//            } else {
//                /* 创建目录 */
//                $dir = ROOT . $this->data_dir . '/' . $dir . '/';
//                if ($img_name) {
//                    $img_name = $dir . $img_name; // 将图片定位到正确地址
//                }
//            }
//
//            /* 如果目标目录不存在，则创建它 */
//            if (!file_exists($dir)) {
//                if (!webbase_common::make_dir($dir)) {
//                    /* 创建目录失败 */
//                    $this->error_msg = sprintf("文件目录不可写", $dir);
//                    $this->error_no = 14;
//
//                    return false;
//                }
//            }
//
//            if (empty($img_name)) {
//                $img_name = $this->unique_name($dir);
//                $img_name = $dir . $img_name . $this->get_filetype($upload['name']);
//            }
//
//            if (!$this->check_img_type($upload['type'])) {
//                $this->error_msg = "不允许的文件类型"; //$GLOBALS['_LANG']['invalid_upload_image_type'];
//                $this->error_no = 13;
//
//                return false;
//            }
//
//            if ($this->move_file($upload, $img_name)) {
//                //echo $img_name;
//                return str_replace(ROOT, '', $img_name);
//            } else {
//                $this->error_msg = sprintf("上传失败", $upload['name']);
//                $this->error_no = 12;
//
//                return false;
//            }
//        } else {
//            return false;
//        }
//    }
//
//    function upload_other($upload, $dir = '', $allow_type = false, $img_name = '') {
//        if (!$allow_type) {
//            $allow_type = array('image/gif', 'image/jpeg', 'image/png', "application/x-shockwave-flash");
//        }
//        /* 没有指定目录默认为根目录images */
//        if ((isset($upload['error']) && $upload['error'] == 0) || (!isset($upload['error']) && isset($upload['tmp_name']) && $upload['tmp_name'] != 'none')) {
//            if (empty($dir)) {
//                /* 创建当月目录 */
//                $dir = date('Ym');
//                $dir = ROOT . $this->images_dir . '/' . $dir . '/';
//            } else {
//                /* 创建目录 */
//                $dir = ROOT . $this->data_dir . '/' . $dir . '/';
//                if ($img_name) {
//                    $img_name = $dir . $img_name; // 将图片定位到正确地址
//                }
//            }
//
//            /* 如果目标目录不存在，则创建它 */
//            if (!file_exists($dir)) {
//                if (!webbase_common::make_dir($dir)) {
//                    /* 创建目录失败 */
//                    $this->error_msg = sprintf("文件目录不可写", $dir);
//                    $this->error_no = 11;
//
//                    return false;
//                }
//            }
//
//            if (empty($img_name)) {
//                $img_name = $this->unique_name($dir);
//                $img_name = $dir . $img_name . $this->get_filetype($upload['name']);
//            }
//            if (!$this->check_file_type($upload['type'], $allow_type)) {
//                $this->error_msg = "不允许的文件类型"; //$GLOBALS['_LANG']['invalid_upload_image_type'];
//                $this->error_no = 18;
//
//                return false;
//            }
//
//            if ($this->move_file($upload, $img_name)) {
//                //echo $img_name;
//                return str_replace(ROOT, '', $img_name);
//            } else {
//                $this->error_msg = sprintf("上传失败", $upload['name']);
//                $this->error_no = 17;
//
//                return false;
//            }
//        } else {
//            return false;
//        }
//    }

    /**
     * 创建图片的缩略图
     *
     * @access  public
     * @param   string      $img    原始图片的路径
     * @param   int         $thumb_width  缩略图宽度
     * @param   int         $thumb_height 缩略图高度
     * @param   strint      $path         指定生成图片的目录名
     * @return  mix         如果成功返回缩略图的路径，失败则返回false
     */
    public static function makeThumb($img, $target, $thumb_width = 0, $thumb_height = 0, $bgcolor = '#FFFFFF') {
        $gd = self::gd_version(); //获取 GD 版本。0 表示没有 GD 库，1 表示 GD 1.x，2 表示 GD 2.x
        if ($gd == 0) {
            //$this->error_msg = "不支持GD";
            return array('suc' => false, 'msg' => '不支持GD', 'path' => '');
        }

        $ext = '';
        if (!empty($img)) {
            $nameAr = explode('.', $img);
            $ext = $nameAr[count($nameAr) - 1];
            $target = $target . '.' . $ext;
        }

        /* 检查缩略图宽度和高度是否合法 */
        if ($thumb_width == 0 || $thumb_height == 0) {
            return array('suc' => false, 'msg' => '宽高不合法', 'path' => '');
        }

        /* 检查原始文件是否存在及获得原始文件的信息 */
        $org_info = @getimagesize($img);
        if (!$org_info) {
            return array('suc' => false, 'msg' => '原始文件有问题', 'path' => '');
        }

        if (!self::checkImgFunction($org_info[2])) {

            return array('suc' => false, 'msg' => '不支持的类型', 'path' => '');
        }

        $img_org = self::imgResource($img, $org_info[2]);

        /* 原始图片以及缩略图的尺寸比例 */
        $scale_org = $org_info[0] / $org_info[1];
        /* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
        if ($thumb_width == 0) {
            $thumb_width = $thumb_height * $scale_org;
        }
        if ($thumb_height == 0) {
            $thumb_height = $thumb_width / $scale_org;
        }

        /* 创建缩略图的标志符 */

        if ($gd == 2) {

            $img_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
        } else {
            $img_thumb = imagecreate($thumb_width, $thumb_height);
        }

        /* 背景颜色 */
        $bgcolor = trim($bgcolor, "#");
        sscanf($bgcolor, "%2x%2x%2x", $red, $green, $blue);
        $clr = imagecolorallocate($img_thumb, $red, $green, $blue);
        imagefilledrectangle($img_thumb, 0, 0, $thumb_width, $thumb_height, $clr);

        if ($org_info[0] / $thumb_width > $org_info[1] / $thumb_height) {
            $lessen_width = $thumb_width;
            $lessen_height = $thumb_width / $scale_org;
        } else {
            /* 原始图片比较高，则以高度为准 */
            $lessen_width = $thumb_height * $scale_org;
            $lessen_height = $thumb_height;
        }

        $dst_x = ($thumb_width - $lessen_width) / 2;
        $dst_y = ($thumb_height - $lessen_height) / 2;

        /* 将原始图片进行缩放处理 */
        if ($gd == 2) {
            imagecopyresampled($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info[0], $org_info[1]);
        } else {
            imagecopyresized($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info[0], $org_info[1]);
        }

        /* 生成文件 */
        if (function_exists('imagejpeg')) {
            //$filename .= '.jpg';
            imagejpeg($img_thumb, $target);
        } elseif (function_exists('imagegif')) {
            //$filename .= '.gif';
            imagegif($img_thumb, $target);
        } elseif (function_exists('imagepng')) {
            //$filename .= '.png';
            imagepng($img_thumb, $target);
        } else {
            return array('suc' => false, 'msg' => '创建失败', 'path' => '');
        }

        imagedestroy($img_thumb);
        imagedestroy($img_org);

        //确认文件是否生成
        if (file_exists($target)) {
            //$path = str_replace(ROOT, '', $dir) . $filename;
            $path = str_replace(ROOT, '', $target);
            return array('suc' => true, 'msg' => '生成成功', 'path' => $path, 'fpath' => $target);
        } else {
            return array('suc' => true, 'msg' => '文件不可写', 'path' => '');
        }
    }

    /**
     * 为图片增加水印
     *
     * @access      public
     * @param       string      filename            原始图片文件名，包含完整路径
     * @param       string      target_file         需要加水印的图片文件名，包含完整路径。如果为空则覆盖源文件
     * @param       string      $watermark          水印完整路径
     * @param       int         $watermark_place    水印位置代码
     * @return      mix         如果成功则返回文件路径，否则返回false
     */
    static function addWatermark($filename, $target_file = '', $watermark = '', $watermark_place = '', $watermark_alpha = 100) {

        $gd = self::gd_version();

        if ($gd == 0) {
            return array('suc' => false, 'msg' => '不支持GD库', 'path' => '');
        }


        $ext = '';
        if (!empty($filename)) {
            $nameAr = explode('.', $filename);
            $ext = $nameAr[count($nameAr) - 1];
            $target_file = $target_file . '.' . $ext;
        }


        if (!self::validateImage($watermark)) {
            /* 已经记录了错误信息 */
            return array('suc' => false, 'msg' => '不支持的类型', 'path' => '');
        }

        // 获得水印文件以及源文件的信息
        $watermark_info = @getimagesize($watermark);

        $watermark_handle = self::imgResource($watermark, $watermark_info[2]);

        if (!$watermark_handle) {
            return array('suc' => false, 'msg' => '文件不支持水印', 'path' => '');
        }

        // 根据文件类型获得原始图片的操作句柄
        $source_info = @getimagesize($filename);
        $source_handle = self::imgResource($filename, $source_info[2]);
        if (!$source_handle) {

            return array('suc' => false, 'msg' => '文件不支持水印', 'path' => '');
        }

        // 根据系统设置获得水印的位置

        switch ($watermark_place) {
            case 'lefttop':
                $x = 0;
                $y = 0;
                break;
            case 'righttop':
                $x = $source_info[0] - $watermark_info[0];
                $y = 0;
                break;
            case 'leftbottom':
                $x = 0;
                $y = $source_info[1] - $watermark_info[1];
                break;
            case 'rightbottom':
                $x = $source_info[0] - $watermark_info[0];
                $y = $source_info[1] - $watermark_info[1];
                break;
            case '6':
                $x = 0;
                $y = 10;
                break;
            default:
                $x = $source_info[0] / 2 - $watermark_info[0] / 2;
                $y = $source_info[1] / 2 - $watermark_info[1] / 2;
        }
        if ($watermark_info['mime'] == "image/png" || $watermark_info['mime'] == "image/gif" || $watermark_info['mime'] == "image/x-png") {
            
            imagecopy($source_handle, $watermark_handle, $x, $y, 0, 0, $watermark_info[0], $watermark_info[1]);
        } else {
            imagecopymerge($source_handle, $watermark_handle, $x, $y, 0, 0, $watermark_info[0], $watermark_info[1], $watermark_alpha);
        }
        //生成混合图像，这是系统的
        // imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], 

        $target = empty($target_file) ? $filename : $target_file;

        switch ($source_info[2]) {
            case 'image/gif':
            case 1:
                imagegif($source_handle, $target);
                break;

            case 'image/pjpeg':
            case 'image/jpeg':
            case 2:
                imagejpeg($source_handle, $target, 90);
                break;

            case 'image/x-png':
            case 'image/png':
            case 3:
                imagepng($source_handle, $target);
                break;

            default:
                return array('suc' => false, 'msg' => '创建失败', 'path' => '');
        }

        imagedestroy($source_handle);

        $path = realpath($target);

        if ($path) {
            $path = str_replace(ROOT, '', str_replace('\\', '/', $path));
            return array('suc' => false, 'msg' => '创建成功', 'path' => $path);
        } else {
            return array('suc' => false, 'msg' => '写入失败', 'path' => '');
        }
    }

    /**
     *  检查水印图片是否合法
     *
     * @access  public
     * @param   string      $path       图片路径
     *
     * @return boolen
     */
    static function validateImage($path) {
        if (empty($path)) {
            return false;
        }

        /* 文件是否存在 */
        if (!file_exists($path)) {
            return false;
        }

        // 获得文件以及源文件的信息
        $image_info = @getimagesize($path);

        if (!$image_info) {
            return false;
        }

        /* 检查处理函数是否存在 */
        if (!self::checkImgFunction($image_info[2])) {
            return false;
        }

        return true;
    }

//    /**
//     * 返回错误信息
//     *
//     * @return  string   错误信息
//     */
//    function error_msg() {
//        return $this->error_msg;
//    }

    /* ------------------------------------------------------ */
    //-- 工具函数
    /* ------------------------------------------------------ */

    /**
     * 检查图片类型
     * @param   string  $img_type   图片类型
     * @return  bool
     */
    static function checkImgType($img_type) {
        return $img_type == 'image/pjpeg' ||
                $img_type == 'image/x-png' ||
                $img_type == 'image/png' ||
                $img_type == 'image/gif' ||
                $img_type == 'image/jpeg';
    }

//    /**
//     * 检查图片类型
//     * @param   string  $img_type   图片类型
//     * @return  bool
//     */
//    function check_file_type($file_type, $allow_type) {
//        $is_allow = false;
//        foreach ($allow_type as $type) {
//            if ($file_type == $type) {
//                $is_allow = true;
//                break;
//            }
//        }
//        return $is_allow;
//    }

    /**
     * 检查图片处理能力
     *
     * @access  public
     * @param   string  $img_type   图片类型
     * @return  void
     */
    static function checkImgFunction($img_type) {
        switch ($img_type) {
            case 'image/gif':
            case 1:

                if (PHP_VERSION >= '4.3') {
                    return function_exists('imagecreatefromgif');
                } else {
                    return (imagetypes() & IMG_GIF) > 0;
                }
                break;

            case 'image/pjpeg':
            case 'image/jpeg':
            case 2:
                if (PHP_VERSION >= '4.3') {
                    return function_exists('imagecreatefromjpeg');
                } else {
                    return (imagetypes() & IMG_JPG) > 0;
                }
                break;

            case 'image/x-png':
            case 'image/png':
            case 3:
                if (PHP_VERSION >= '4.3') {
                    return function_exists('imagecreatefrompng');
                } else {
                    return (imagetypes() & IMG_PNG) > 0;
                }
                break;

            default:
                return false;
        }
    }

//    /**
//     * 生成随机的数字串
//     *
//     * @author: weber liu
//     * @return string
//     */
//    function random_filename() {
//        $str = '';
//        for ($i = 0; $i < 9; $i++) {
//            $str .= mt_rand(0, 9);
//        }
//        $common = new webbase_common();
//        return $common->gmtime() . $str;
//    }
//
//    /**
//     *  生成指定目录不重名的文件名
//     *
//     * @access  public
//     * @param   string      $dir        要检查是否有同名文件的目录
//     *
//     * @return  string      文件名
//     */
//    function unique_name($dir) {
//        $filename = '';
//        while (empty($filename)) {
//            $filename = $this->random_filename();
//            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png')) {
//                $filename = '';
//            }
//        }
//
//        return $filename;
//    }
//    /**
//     *  返回文件后缀名，如‘.php’
//     *
//     * @access  public
//     * @param
//     *
//     * @return  string      文件后缀名
//     */
//    function get_filetype($path) {
//        $pos = strrpos($path, '.');
//        if ($pos !== false) {
//            return substr($path, $pos);
//        } else {
//            return '';
//        }
//    }

    /**
     * 根据来源文件的文件类型创建一个图像操作的标识符
     *
     * @access  public
     * @param   string      $img_file   图片文件的路径
     * @param   string      $mime_type  图片文件的文件类型
     * @return  resource    如果成功则返回图像操作标志符，反之则返回错误代码
     */
    static function imgResource($img_file, $mime_type) {
        switch ($mime_type) {
            case 1:
            case 'image/gif':
                $res = imagecreatefromgif($img_file);
//                if (function_exists('ImageAlphaBlending'))
//                    ImageAlphaBlending($res, true); //设定图像的混色模式
//                if (function_exists('ImageSaveAlpha'))
//                    ImageSaveAlpha($res, true); //保存完整的 alpha 通道信息
                break;

            case 2:
            case 'image/pjpeg':
            case 'image/jpeg':
                $res = imagecreatefromjpeg($img_file);
                break;

            case 3:
            case 'image/x-png':
            case 'image/png':
                $res = imagecreatefrompng($img_file);
//                //if (function_exists('ImageAlphaBlending'))
//                    ImageAlphaBlending($res, true); //设定图像的混色模式
//                //if (function_exists('ImageSaveAlpha'))
//                    ImageSaveAlpha($res, true); //保存完整的 alpha 通道信息
                break;

            default:
                return false;
        }

        return $res;
    }

    /**
     * 获得服务器上的 GD 版本
     *
     * @access      public
     * @return      int         可能的值为0，1，2
     */
    private static function gd_version() {
        static $version = -1;

        if ($version >= 0) {
            return $version;
        }

        if (!extension_loaded('gd')) {
            $version = 0;
        } else {
            // 尝试使用gd_info函数
            if (PHP_VERSION >= '4.3') {
                if (function_exists('gd_info')) {
                    $ver_info = gd_info();
                    preg_match('/\d/', $ver_info['GD Version'], $match);
                    $version = $match[0];
                } else {
                    if (function_exists('imagecreatetruecolor')) {
                        $version = 2;
                    } elseif (function_exists('imagecreate')) {
                        $version = 1;
                    }
                }
            } else {
                if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
                    /* 如果phpinfo被禁用，无法确定gd版本 */
                    $version = 1;
                } else {
                    // 使用phpinfo函数
                    ob_start();
                    phpinfo(8);
                    $info = ob_get_contents();
                    ob_end_clean();
                    $info = stristr($info, 'gd version');
                    preg_match('/\d/', $info, $match);
                    $version = $match[0];
                }
            }
        }

        return $version;
    }

//    /**
//     *
//     *
//     * @access  public
//     * @param
//     *
//     * @return void
//     */
//    function move_file($upload, $target) {
//        if (isset($upload['error']) && $upload['error'] > 0) {
//            return false;
//        }
//
//        if (!webbase_common::move_upload_file($upload['tmp_name'], $target)) {
//            return false;
//        }
//
//        return true;
//    }
}
