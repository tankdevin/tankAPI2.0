<?php
// 应用公共文件
include 'newfunction.php';
/*
 * 获取所有导航
 * @param $status mixed 是否显示||全部
 * @param $limit mixed
 * @param $pid int 父级id 0则为顶级栏目
 */
function getAllCategory($status, $pid = '', $limit = '')
{
    //导航
    $nav = think\Db::name('category');
    $module = request()->module();
    if ($status !== 'all') {
        $nav = $nav->where(['status' => $status]);
    }

    //pid为空则会获取所有导航并且拼装二级，其他值则只能获取该父id下的导航
    if ($pid !== '') {
        $nav = $nav->where('pid',$pid);
    }

    if ($limit != '') {
        $nav = $nav->limit($limit);
    }
    $nav = $nav->select();
    $all_nav = [];
    $tree = [];
    //拼接导航 一级二级
    // foreach ($nav as $key => $val) {
    //     //生成url，前端
    //     if ($val['type'] == 1){
    //         $val['url'] = $val['outurl'];
    //     } elseif ($val['modelid'] == 6){
    //         $val['url'] = url($module.'/guestbook/index', ['cid' => $val['id']]);
    //     } else {
    //       $val['url'] = url($module.'/listing/index', ['cid' => $val['id']]);  
    //     }
    //     //pid 不为空则只取该pid下的栏目
    //     if ($pid !== '') {
    //         $all_nav[] = $val;
    //     } else {
    //          if ($val['pid'] == 0) {
    //             $all_nav[$val['id']] = $val;
    //         } else {
    //             $all_nav[$val['pid']]['children'][] = $val;
    //         }   
    //     }
        
    // }
    //拼接栏目树形结构
    foreach ($nav as $val) {
        //生成url，前端调用
        if ($val['type'] == 1){
            $val['url'] = $val['outurl'];
        } elseif ($val['modelid'] == 6){
            $val['url'] = url($module.'/guestbook/index', ['cid' => $val['id']]);
        } else {
          $val['url'] = url($module.'/listing/index', ['cid' => $val['id']]);  
        }
        $all_nav[$val['id']] = $val;
    }

    //返回全部栏目，非树形结构
    if ($pid !== '') {
        return $all_nav;
    }

    //树形结构,无限级分类
    $tree = create_tree($all_nav);

    return $tree;
}

/**
 * 根据数据生成树形结构数据
 * @param array $data 要转换的数据集
 * @param int $pk 主键（栏目id）
 * @param string $pid parent标记字段
 * @return array
 */
function create_tree($data,$pk='id',$pid='pid'){
    $tree = $list = [];

    foreach ($data as $val) {
        $list[$val[$pk]] = $val;
    }

    foreach ($list as $key =>$val){     
        if($val[$pid] == 0){      
            $tree[] = &$list[$key];
        }else{
            //找到其父类
            $list[$val[$pid]]['children'][] = &$list[$key];
        }
    }
    return $tree;
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function get_password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : get_randomstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 生成随机字符串
 */
function get_randomstr($length = 6) {
    $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 获取system配置参数
 */
function get_system_value($name=''){
    $value = think\Db::name('system')->where('name',$name)->value('value');
    return $value;
}
/**
 * 发起POST请求
 *
 * @access public
 * @param string $url
 * @param array $data
 * @return string
 */
function post($url, $data = '', $cookie = '', $type = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if($cookie){
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt ($ch, CURLOPT_REFERER,'https://wx.qq.com');
    }
    if($type){
        $header = array(
        'Content-Type: application/json',
        );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
    dump($output);
    return $output;
}
/**
 * 截取字符串
 * @param  string  $str     字符串
 * @param  int $start   开始位置
 * @param  int  $length  结束位置
 * @param  string  $charset 字符编码
 * @param  boolean $suffix  是否要...
 * @return string           截取后的字符串
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=false) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

//判断移动端
function is_mobile()  
{  
     $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
     $mobile_browser = '0';  
     if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
      $mobile_browser++;  
     if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
      $mobile_browser++;  
     if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
      $mobile_browser++;  
     if(isset($_SERVER['HTTP_PROFILE']))  
      $mobile_browser++;  
      $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
      $mobile_agents = array(  
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
        'wapr','webc','winw','winw','xda','xda-'
        );  
     if(in_array($mobile_ua, $mobile_agents))  
      $mobile_browser++;  
     if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
      $mobile_browser++;  
     // Pre-final check to reset everything if the user is on Windows  
     if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
      $mobile_browser=0;  
     // But WP7 is also Windows, with a slightly different characteristic  
     if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
      $mobile_browser++;  
     if($mobile_browser>0)  
      return true;  
     else
      return false;
}

/**
 * 根据路由判断权限
 * $route 路由 格式：controller/action 或 ['controller','action'] 或 空
 */
function has_auth_by_route($route = '') {
    if (!session('?userinfo')) {
        return 0;
    } else if (session('userinfo.usertype') == 9) {
        //超级管理员
        return 1;
    }
    
    if ($route) {
        //主动判断权限
        if (is_array($route)) {
            $route = implode('/', $route);
        }
    } else {
        $controller = request()->controller();
        $action = request()->action();
        
        $route = $controller . '/' . $action;
    }
    $route = strtolower($route);
    list($controller,$action) = explode('/', $route);
    //操作是否在权限列表中
    $rabc = include APP_PATH.'admin/rbac.php';
    if (!isset($rabc[$controller][$action])) return 1;

    if (!$auth = think\Cache::get('auth_'.session('userinfo.id'))) {
        $auth = get_power_by_uid(session('userinfo.role_id'));
        think\Cache::tag('auth')->set('auth_'.session('userinfo.id'), $auth);
    }
    return in_array($route,$auth)?1:0;
}

/**
 * 通过角色id获取权限
 */
function get_power_by_uid($roleid) {
    $power = think\Db::name('admin_role')->where('id', $roleid)->value  ('power');
    $auths = think\Db::name('admin_power')->where('id','IN',explode(',',$power))->select();
    return array_column($auths,'route');
}

if(!function_exists("array_column"))
{

    function array_column($array,$column_name)
    {

        return array_map(function($element) use($column_name){return $element[$column_name];}, $array);

    }

}

