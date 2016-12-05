<?php

class WebBaseCommon {

    static function addslashes_deep_obj($obj) {
        if (is_object($obj) == true) {
            foreach ($obj AS $key => $val) {
                $obj->$key = self::addslashes_deep($val);
            }
        } else {
            $obj = self::addslashes_deep($obj);
        }

        return $obj;
    }

    /**
     * 递归方式的对变量中的特殊字符进行转义
     *
     * @access  public
     * @param   mix     $value
     *
     * @return  mix
     */
    static function addslashes_deep($value) {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
        }
    }

    /**
     * 递归方式的对变量中的特殊字符去除转义
     *
     * @access  public
     * @param   mix     $value
     *
     * @return  mix
     */
    static function stripslashes_deep($value) {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        }
    }

    /**
     * 检查目标文件夹是否存在，如果不存在则自动创建该目录
     *
     * @access      public
     * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
     *
     * @return      bool
     */
    static function makeDir($folder) {
        $reval = false;

        if (!file_exists($folder)) {
            /* 如果目录不存在则尝试创建该目录 */
            @umask(0);

            /* 将目录路径拆分成数组 */
            preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

            /* 如果第一个字符为/则当作物理路径处理 */
            $base = ($atmp[0][0] == '/') ? '/' : '';

            /* 遍历包含路径信息的数组 */
            foreach ($atmp[1] AS $val) {
                if ('' != $val) {
                    $base .= $val;

                    if ('..' == $val || '.' == $val) {
                        /* 如果目录为.或者..则直接补/继续下一个循环 */
                        $base .= '/';

                        continue;
                    }
                } else {
                    continue;
                }

                $base .= '/';

                if (!file_exists($base)) {
                    /* 尝试创建目录，如果创建失败则继续循环 */
                    if (@mkdir(rtrim($base, '/'), 0777)) {
                        @chmod($base, 0777);
                        $reval = true;
                    }
                }
            }
        } else {
            /* 路径已经存在。返回该路径是不是一个目录 */
            $reval = is_dir($folder);
        }

        clearstatcache();

        return $reval;
    }

    function gmtime() {
        return (time() - date('Z'));
    }

    /**
     * 将上传文件转移到指定位置
     *
     * @param string $file_name
     * @param string $target_name
     * @return blog
     */
    static function moveUploadFile($file_name, $target_name = '') {
        if (function_exists("move_uploaded_file")) {
            if (move_uploaded_file($file_name, $target_name)) {
                return true;
            } else if (copy($file_name, $target_name)) {
                return true;
            }
        } elseif (copy($file_name, $target_name)) {
            return true;
        }
        return false;
    }
    
    
    static function sleep($ms){
        usleep($ms*1000);
    }

}