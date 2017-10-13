<?php
/**
 * 实例化表
 * @param unknown $name
 * @return unknown
 */
function M($name){
    $conconts = think\Db::name($name);
    return $conconts;
}
/**
 * 接收post
 * @param unknown $name
 */
function iPost($name){
    $post = input('post.'.$name);
    return $post;
}
/**
 * 接收get
 * @return unknown
 */
function iGet(){
    $get = input('get.'.$name);
    return $get;
}
/**
 * 是否是手机号码
 *
 * @param string $phone 手机号码
 * @return boolean
 */
function is_phone($phone) {
    if (strlen ( $phone ) != 11 || ! preg_match ( '/^1[3|4|5|8][0-9]\d{4,8}$/', $phone )) {
        return false;
    } else {
        return true;
    }
}
/**
 * 验证字符串是否为数字,字母,中文和下划线构成
 * @param string $username
 * @return bool
 */
function is_check_string($str){
    if(preg_match('/^[\x{4e00}-\x{9fa5}\w_]+$/u',$str)){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否为一个合法的email
 * @param sting $email
 * @return boolean
 */
function is_email($email){
    if (filter_var ($email, FILTER_VALIDATE_EMAIL )) {
        return true;
    } else {
        return false;
    }
}
/**
 * 是否为一个合法的url
 * @param string $url
 * @return boolean
 */
function is_url($url){
    if (filter_var ($url, FILTER_VALIDATE_URL )) {
        return true;
    } else {
        return false;
    }
}
/**
 * 是否为一个合法的ip地址
 * @param string $ip
 * @return boolean
 */
function is_ip($ip){
    if (ip2long($ip)) {
        return true;
    } else {
        return false;
    }
}
/**
 * 是否为整数
 * @param int $number
 * @return boolean
 */
function is_number($number){
    if(preg_match('/^[-\+]?\d+$/',$number)){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否为正整数
 * @param int $number
 * @return boolean
 */
function is_positive_number($number){
    if(ctype_digit ($number)){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否为小数
 * @param float $number
 * @return boolean
 */
function is_decimal($number){
    if(preg_match('/^[-\+]?\d+(\.\d+)?$/',$number)){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否为正小数
 * @param float $number
 * @return boolean
 */
function is_positive_decimal($number){
    if(preg_match('/^\d+(\.\d+)?$/',$number)){
        return true;
    }else{
        return false;
    }
}
/**
 * 是否为英文
 * @param string $str
 * @return boolean
 */
function is_english($str){
    if(ctype_alpha($str))
        return true;
    else
        return false;
}
/**
 * 是否为中文
 * @param string $str
 * @return boolean
 */
function is_chinese($str){
    if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$str))
        return true;
    else
        return false;
}
/**
 * 判断是否为图片
 * @param string $file  图片文件路径
 * @return boolean
 */
function is_image($file){
    if(file_exists($file)&&getimagesize($file===false)){
        return false;
    }else{
        return true;
    }
}
/**
 * 是否为合法的身份证(支持15位和18位)
 * @param string $card
 * @return boolean
 */
function is_card($card){
    if(preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/',$card)||preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/',$card))
        return true;
    else
        return false;
}
/**
 * 验证日期格式是否正确
 * @param string $date
 * @param string $format
 * @return boolean
 */
function is_date($date,$format='Y-m-d'){
    $t=date_parse_from_format($format,$date);
    if(empty($t['errors'])){
        return true;
    }else{
        return false;
    }
}
/**
 * 接口返回方法
 * @param unknown $appid 暂无
 * @param unknown $msg
 * @param number $code
 * @param string $data
 */

function msgTankOpenApi($appid,$msg,$code=1,$data=''){
    $retData = array('code'=>$code,'msg'=>$msg);
    if(!empty($data) || is_array($data))$retData['data'] = $data;
    return json_encode($retData);
}
/**
 * TankopenAPI获取MD5签名信息
 * @param unknown $data		需要签名验证的信息
 * @param unknown $secret	参与签名验证的密钥
 * @return string
 */
function tankOpenApiSign($data,$secret){
    $data['secret'] = $secret;
    ksort($data);//array按照key进行排序
    $querystring = array();
    foreach ($data as $key=>$value) {//字符串拼接
        if($key == 'sign')continue;
        $querystring[] = "{$key}={$value}";
    }
    return md5(implode('&', $querystring));
}
/**
 * \实例化model
 * @param unknown $model
 */
function MM($model){
    $obj = \think\Loader::model($model);
    return $obj;
}