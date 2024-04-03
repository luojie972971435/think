<?php

/**
 * 生成哈希值加密
 * @param string  $pwd   明文密码字符串
 * @param string  $salt  随机字符串
 * @return string
 */
function vae_set_password($pwd, $salt)
{
    return password_hash(md5(md5($pwd . md5($salt)) . $salt), PASSWORD_DEFAULT);
}

/**
 * 哈希值验证
 * @param string  $pwd   明文密码字符串
 * @param string  $salt  随机字符串
 * @param string  $pwd_n  pwd + salt 生成的哈希值
 * @return boolean
 */
function vae_get_password($pwd, $salt, $pwd_n)
{
    $password = md5(md5($pwd . md5($salt)) . $salt);
    if (!password_verify($password, $pwd_n)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 递归排序
 * @param array $result 数组
 * @return array
 */
function vae_set_recursion($result, $pid = 0, $format = "└ ")
{
    /*记录排序后的类别数组*/
    static $list = array();
    foreach ($result as $k => $v) {
        if ($v['pid'] == $pid) {
            // if ($pid != 0) {
                $v['title'] = $format . $v['title'];
            // }
            /*将该类别的数据放入list中*/
            $list[] = $v;
            vae_set_recursion($result, $v['id'], "└" . $format);
        }
    }
    return $list;
}

/**
 * 重组数组
 */
function vae_list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[$data[$pk]] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][$data[$pk]] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 循环删除目录和文件
 * @param string $dir_name 目录名
 * @return bool
 */
if (!function_exists('delete_dir_file')) {
    function delete_dir_file($dir_name)
    {
        $result = false;
        if (is_dir($dir_name)) { //检查指定的文件是否是一个目录
            if ($handle = opendir($dir_name)) {   //打开目录读取内容
                while (false !== ($item = readdir($handle))) { //读取内容
                    if ($item != '.' && $item != '..') {
                        if (is_dir($dir_name . '/' . $item)) {
                            delete_dir_file($dir_name . '/' . $item);
                        } else {
                            unlink($dir_name . '/' . $item);  //删除文件
                        }
                    }
                }
                closedir($handle);  //打开一个目录，读取它的内容，然后关闭
                if (rmdir($dir_name)) { //删除空白目录
                    $result = true;
                }
            }
        }
        return $result;
    }
}


function int_upload($module, $use)
{
    $request = new \think\Request;
    if ($request->file('file')) {
        $file = $request->file('file');
    } else {
        $res['code'] = 0;
        $res['msg']  = '没有上传文件';
        return $res;
    }
    // 保存路径
    $savePath = \Env::get('ROOT_PATH') . 'public/upload/' . $module . '/' . $use;
    $info = $file->validate(['ext' => 'jpg,jpeg,png,gif,bmp,xls,xlsx,csv,mp4,flv,avi'])->rule('sha1')->move($savePath);
    if ($info) {
        $vphotograph = $info->getSaveName();
        $res['code'] = 1;
        $res['data'] = '/upload/' . $module . '/' . $use . '/' . $vphotograph;
        return $res;
    } else {
        // 上传失败获取错误信息
        return vae_assign(0, '上传失败：' . $file->getError());
    }
}