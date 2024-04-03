<?php

/**
 * 获取\think\response\Json对象实例
 * @param integer $code 返回状态
 * @param string  $msg  返回提示语
 * @param array   $data 返回的数据
 * @param string  $url  跳转链接
 * @param integer $httpCode 头部状态码
 * @param array   $header 头部
 * @param array   $options 参数
 * @return \think\response\Json
 */
function vae_assign($code = 200, $msg = 'OK', $data = [], $url = '', $httpCode = 200, $header = [], $options = [], $open = false)
{
    $res['code'] = $code;
    $res['msg']  = $msg;
    $res['url']  = $url;
    if (is_object($data)) {
        $data = $data->toArray();
    }
    $res['data'] = $data;
    $response = \think\Response::create($res, 'json', $httpCode, $header, $options);
    if ($open == true) {
        throw new \think\exception\HttpResponseException($response);
    } else {
        return $response;
    }
}

/**
 * 获取输入数据 支持默认值和过滤
 * @param string    $key 获取的变量名
 * @return mixed
 */
function vae_get_param($key = '')
{
    return \think\facade\Request::param(strip_tags($key));
}

/**
 * 获取客户端IP
 * @return string
 */
function get_client_ip()
{
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 * 打印数据
 * @return string
 */
function p($data)
{
    if (empty($data)) return false;
    if (is_array($data) || is_object($data)) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    } elseif (is_string($data) || is_int($data)) {
        echo '<pre><h2>';
        echo $data;
        echo '</h2></pre>';
    } else {
        var_dump($data);
    }
}

/**
 * 生成一个随机TOKEN
 * @param string    $strting 变量字符串
 * @return string   获取随机生成的token
 */
function creatToken($strting, $time = 60)
{
    if (empty($strting)) return false;
    $redis = new module\Redis();
    $code = chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE)) . chr(mt_rand(0xB0, 0xF7)) . chr(mt_rand(0xA1, 0xFE));
    $value = random(120, authcode($code));
    $token = $redis->setRedis($strting, $value, $time);
    if (!empty($token)) {
        return $value;
    } else {
        return false;
    }
}

/**
 * 读取Token并验证
 * @param string    $token 变量字符串
 * @param string    $strting Token的key键，$redis->pullRedis读取缓存并删除
 * @return boolean
 */
function checkToken($token, $strting)
{
    if (empty($strting) || empty($token)) return false;
    $redis = new module\Redis();
    if ($token === $redis->pullRedis($strting)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 加密TOKEN
 * @param string    $strting 变量字符串
 * @return string   生成120位数随机token
 */
function authcode($str)
{
    $key = "BLOCKIAMON";
    $str = substr(md5($str), 8, 10);
    return md5($key . $str);
}


/**
 * 生成令牌
 * @param string $form
 * @return string
 */
function myshopToken($form = 'form')
{
    $value = creatToken($form, 86400);   //1天过期
    return '<input type="hidden" name="validate_form" value="' . $form . '"><input type="hidden" name="Myshop_Token" value="' . $value . '" class="Myshop_Token">';
}

/***
 * 令牌校检
 * @return array
 */
function validateMyshopToken($form = 'form', $_token)
{
    $cache_token = checkToken($_token, $form);
    if ($cache_token == false) {
        $new_token = creatToken($form, 86400);
        $return = [
            'code' => 400,
            'token' => $new_token
        ];
        return $return;
    } else {
        $return = [
            'code' => 200,
            'msg'  => '令牌验证成功'
        ];
        return $return;
    }
}

/**
 * 随机生成随机数
 * @param int    $length 生成随机数的长度
 * @param string $chars 随机数包含的字符串
 * @return string   获取随机生成随机数
 */
function random($length, $chars = '0123456789')
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 随机生成字符串
 * @param int    $length 生成随机数的长度
 * @param string $chars 随机数包含的字符串
 * @return string   获取随机生成字符串
 */
function create_randomstr($lenth = 6, $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ')
{
    return random($lenth, $chars);
}

/**
 * 字符串转换成数组
 * @param string $string 有规则的字符串
 * @return array
 */
function parse_config_attr($string)
{
    $str = "/[,;\r\n]+/";
    $array = preg_split($str, trim($string, ",;\r\n"));
    if (strpos($string, ':')) {
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    } elseif (strpos($string, '--')) {
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode('--', $val);
            $value[$k]   = $v;
        }
    } else {
        $value  =   $array;
    }
    return $value;
}

/**
 * 字符串转换成数组
 * @param string $string 有规则的字符串
 * @return array
 */
function parse_config_array($string)
{
    $str = "/[\r\n]+/";
    $array = preg_split($str, trim($string, "\r\n"));
    $value  =   $array;
    return $value;
}



/**
 * 读取字符串中的值
 * @param string $config 有规则的字符串
 * @param int    $group  键值
 * @return string
 */
function get_config_group($config, $group = 0)
{
    $list = parse_config_attr($config);
    return $group ? $list[$group] : '';
}


/**
 * 获得访问者浏览器
 */
function browse_info()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $br = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $br)) {
            $br = 'MSIE';
        } else if (preg_match('/Firefox/i', $br)) {
            $br = 'Firefox';
        } else if (preg_match('/Chrome/i', $br)) {
            $br = 'Chrome';
        } else if (preg_match('/Safari/i', $br)) {
            $br = 'Safari';
        } else if (preg_match('/Opera/i', $br)) {
            $br = 'Opera';
        } else {
            $br = 'Other';
        }
        return $br;
    } else {
        return 'unknow';
    }
}

/**
 * 生成唯一主键ID
 */
function create_guid($namespace = '')
{
    static $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= empty($_SERVER['REQUEST_TIME']) ? '' : $_SERVER['REQUEST_TIME'];
    $data .= empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
    $data .= get_client_ip();
    $data .= empty($_SERVER['SERVER_PORT']) ? '' : $_SERVER['SERVER_PORT'];
    $data .= empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
    $data .= empty($_SERVER['REMOTE_PORT']) ? '' : $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);
    return $guid;
}

// 随机从数组中取出一个值
function arr_rand($arr)
{
    return $arr[array_rand($arr)];
}

// 移除数组中的指定值
function arr_unset($arr, $val)
{
    $array = [];
    foreach ($arr as $value) {
        if ($value != $val) {
            $array[] = $value;
        }
    }
    return $array;
}


function curl($url, $data = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    if (count($data) > 0) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $headers = array('Content-Type:application/json; charset=utf-8');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //执行请求
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}


function downTime($end_time)
{
    $today = time();
    $second = $end_time - $today;
    if ($second <= 0) return 0;
    $day    = ceil($second / 3600 / 24);    //倒计时还有多少天
    $daystr = $day ? $day : 0;
    return $daystr;
}

// 繁琐版红包计算
function random_red($totalAmount, $num)
{
    //数组用于存储生成的红包金额
    $amountList = array_fill(0, $num, 0);
    //最小金额，保证每个红包不会为0
    $minAmount = 0.001;
    // 剩余金额
    $leftAmount = $totalAmount;
    // 循环生成随机金额
    for ($i = 0; $i < $num - 1; $i++) {
        // 剩余红包数量
        $leftNum = $num - $i;
        // 保证每个红包有剩余金额可支配，并且最多不超过平均值两倍
        $avgAmount = number_format($leftAmount / $leftNum, 3, '.', '');
        $maxAmount = number_format(2 * $avgAmount, 3, '.', '');
        $randAmount = mt_rand(0.01, $maxAmount * 1000) / 1000;
        $randAmount = $randAmount < $minAmount ? $minAmount : $randAmount;
        //$randAmount = max($randAmount, $minAmount);
        // 红包金额不超过剩余金额
        if ($randAmount > $leftAmount) {
            $randAmount = $leftAmount;
        }
        // 存储到数组里
        $amountList[$i] = number_format($randAmount, 3, '.', '');
        // 剩余金额
        $leftAmount = number_format($leftAmount - $randAmount, 3, '.', '');
    }
    // 最后一个红包，保证总金额不变
    $amountList[$num - 1] = $leftAmount;
    $update_num = 0;
    for ($e = 0; $e < $num; $e++) {
        if ($amountList[$e] <= 0) {
            $amountList[$e] = $minAmount;
            $update_num += 1;
        }
    }
    sort($amountList);
    if ($update_num > 0) {
        $amountList[0] -= $update_num * $minAmount;
    }
    // 打乱红包金额顺序
    shuffle($amountList);
    return $amountList;
}

// 简易版红包计算
function random_red_packet($totalAmount, $num)
{
    $total = $totalAmount; //总额
    $min = 0.1; //每个人最少能收到0.01元
    $arr = [];
    for ($i = 1; $i < $num; $i++) {
        $safe_total = number_format(($total - ($num - $i) * $min) / ($num - $i), 3, '.', ''); //随机安全上限 
        $money = number_format(mt_rand($min * 1000, $safe_total * 1000) / 1000, 3, '.', '');
        $total = number_format(($total * 1000 - $money * 1000) / 1000, 3, '.', '');
        $arr[] = $money;
    }
    $arr[$num - 1] = $total;
    shuffle($arr);
    return $arr;
}

/**
 * 记录日志
 */
function scheduledLog($content, $path, $file_name = '')
{
    if (empty($file_name)) {
        $file_name = date('Ymd').'.txt';
    }
    $file_path = $path . $file_name;
    if(!is_dir($path)){
        mkdir(iconv("UTF-8", "GBK", $path), 0755, true);
    }
    @$file = file_exists($file_path) ? fopen($file_path, 'a') : fopen($file_path, 'w');
    fwrite($file, date('Y-m-d H:i:s') . '  msg:' . $content . PHP_EOL . PHP_EOL);
    fclose($file);
}
