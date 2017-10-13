<?php
namespace app\api\controller;
use think\Controller;
/**
 * testController接口测试
 * 
 * @author tank
 *
 */
class Testopenapi extends Controller{
	
    private $httpurl = 'http://www.api.com/api/';	/* 请求URL地址*/
	
	
	public function index(){

		 $sendDataArr = array(
		 	'phone'=>'18610695932',
		 	'ucode'=>"kjj",
		 	'type'=>"kjj",
		); 
		$ret = $this->sendHttp($sendDataArr,'public.tanktest.getUserInfo');
		print_r($ret);
		$ret = json_decode($ret,true);
		print_r($ret);
	}
	
	/**
	 * 发送请求信息
	 * @param unknown $sendDataArr		请求数据体
	 * @param unknown $method			请求方法
	 * @return mixed
	 */
	private function sendHttp($sendDataArr,$method){
		$httpJiChuArr = array();
		$httpJiChuArr['token'] = 'tanktest001';
		$httpJiChuArr['timestamp'] = time();
		$httpJiChuArr['version'] = 2;
		$httpJiChuArr['method'] = $method;
		if(is_array($sendDataArr)){
			$httpJiChuArr = array_merge($httpJiChuArr,$sendDataArr);
		}
		$secret = 'djakhfiuaguaygf45fhagf';
		$httpJiChuArr['sign'] = tankOpenApiSign($httpJiChuArr,$secret);
		return post($this->httpurl.'/tanktest', $httpJiChuArr);
	}
}