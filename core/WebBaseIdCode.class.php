<?php

/**
 * 验证码图片类
 * ============================================================================
 * 版权所有 (C) 2009-2010
 * ----------------------------------------------------------------------------
 *
 * ============================================================================
 * $Author: jeffzhong $
 * $Date: 2010-01-08 13:38:07 +0800 (星期一, 21 一月 2008) $
 * $Id: Captcha.php 14017 2008-01-21 08:19:07Z jeffzhong $
 */
class WebBaseIdCode {

    /**
     * 背景图片所在目录
     *
     * @var string  $folder
     */
    var $folder = 'data/captcha';

    /**
     * 图片的文件类型
     *
     * @var string  $img_type
     */
    var $img_type = 'png';

    /**
     * 背景图片以及背景颜色
     *
     * 0 => 背景图片的文件名
     * 1 => Red, 2 => Green, 3 => Blue
     * @var array   $themes
     */
    var $themes_jpg = array(
        1 => array('captcha_bg1.jpg', 255, 255, 255),
        2 => array('captcha_bg2.jpg', 0, 0, 0),
        3 => array('captcha_bg3.jpg', 0, 0, 0),
        4 => array('captcha_bg4.jpg', 255, 255, 255),
        5 => array('captcha_bg5.jpg', 255, 255, 255),
    );
    var $themes_gif = array(
        1 => array('captcha_bg1.gif', 255, 255, 255),
        2 => array('captcha_bg2.gif', 0, 0, 0),
        3 => array('captcha_bg3.gif', 0, 0, 0),
        4 => array('captcha_bg4.gif', 255, 255, 255),
        5 => array('captcha_bg5.gif', 255, 255, 255),
    );

    /**
     * 图片的宽度
     *
     * @var integer $width
     */
    var $width = 130;

    /**
     * 图片的高度
     *
     * @var integer $height
     */
    var $height = 20;

    /**
     * 构造函数
     *
     * @access  public
     * @param   string  $folder     背景图片所在目录
     * @param   integer $width      图片宽度
     * @param   integer $height     图片高度
     * @return  bool
     */
    function __construct($folder = '', $width = 145, $height = 20) {
        if (!empty($folder)) {
            $this->folder = $folder;
        }

        $this->width = $width;
        $this->height = $height;

        /* 检查是否支持 GD */
        if (PHP_VERSION >= '4.3') {

            return (function_exists('imagecreatetruecolor') || function_exists('imagecreate'));
        } else {
            return (((imagetypes() & IMG_GIF) > 0) || ((imagetypes() & IMG_JPG)) > 0 );
        }
    }

    /**
     * 生成图片并输出到浏览器
     *
     * @access  public
     * @param   string  $word   验证码
     * @return  mix
     */
    function generate_image($word = false) {

        if (!$word) {
            $word = $this->generate_word();
        }

        $letters = strlen($word);

        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        if (function_exists('imagecreatefromjpeg') && ((imagetypes() & IMG_JPG) > 0)) {
            $theme = $this->themes_jpg[mt_rand(1, count($this->themes_jpg))];
        } else {
            $theme = $this->themes_gif[mt_rand(1, count($this->themes_gif))];
        }
        $this->folder = ROOT . $this->folder;
        if (!file_exists($this->folder . $theme[0])) {
            return false;
        } else {
            $img_bg = (function_exists('imagecreatefromjpeg') && ((imagetypes() & IMG_JPG) > 0)) ?
                    imagecreatefromjpeg($this->folder . $theme[0]) : imagecreatefromgif($this->folder . $theme[0]);
            $bg_width = imagesx($img_bg);
            $bg_height = imagesy($img_bg);

            $img_org = ((function_exists('imagecreatetruecolor')) && PHP_VERSION >= '4.3') ?
                    imagecreatetruecolor($this->width, $this->height) : imagecreate($this->width, $this->height);

            /* 将背景图象复制原始图象并调整大小 */
            if (function_exists('imagecopyresampled') && PHP_VERSION >= '4.3') { // GD 2.x
                imagecopyresampled($img_org, $img_bg, 0, 0, 0, 0, $this->width, $this->height, $bg_width, $bg_height);
            } else { // GD 1.x
                imagecopyresized($img_org, $img_bg, 0, 0, 0, 0, $this->width, $this->height, $bg_width, $bg_height);
            }
            imagedestroy($img_bg);

            $clr = imagecolorallocate($img_org, $theme[1], $theme[2], $theme[3]);

            /* 绘制边框 */
            //imagerectangle($img_org, 0, 0, $this->width - 1, $this->height - 1, $clr);

            /* 获得验证码的高度和宽度 */
            $x = ($this->width - (imagefontwidth(5) * $letters)) / 2;
            $y = ($this->height - imagefontheight(5)) / 2;
            imagestring($img_org, 5, $x, $y, $word, $clr);
            header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

            // HTTP/1.1
            header('Cache-Control: private, no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);

            // HTTP/1.0
            header('Pragma: no-cache');
            if ($this->img_type == 'jpeg' && function_exists('imagecreatefromjpeg')) {

                header('Content-type: image/jpeg');
                imageinterlace($img_org, 1);
                imagejpeg($img_org, false, 95);
            } else {

                header('Content-type: image/png');
                
                imagepng($img_org);
            }

            imagedestroy($img_org);

            return $word;
        }
    }

    /**
     * 生成随机的验证码
     *
     * @access  private
     * @param   integer $length     验证码长度
     * @return  string
     */
    function generate_word($length = 4) {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        for ($i = 0, $count = strlen($chars); $i < $count; $i++) {
            $arr[$i] = $chars[$i];
        }

        mt_srand((double) microtime() * 1000000);
        shuffle($arr);

        return substr(implode('', $arr), 5, $length);
    }

}

?>