<?php
namespace app\api\controller;
use think\Controller;
/**
 * TP5API入口自用
 * @author tank
 *
 */
class Tanktest extends Controller{
	private $appid = '';
	
	public function index(){
	    
		if(defined('XINYITOKEN'))$this->msgTankOpenApi('通信密钥异常','A001');
		if(isset($_POST['secret']))$this->msgXinyiOpenApi('存在多余参数-secret','A001');
		if(!isset($_POST['method']) || empty(iPost('method'))){
			$this->msgTankOpenApi('命令方法不能为空','A001');
		}
		if(!isset($_POST['timestamp']) || empty(iPost('timestamp'))){
			$this->msgTankOpenApi('当前服务器时间戳不能为空','A001');
		}
		if(!isset($_POST['version']) || empty(iPost('version'))){
			$this->msgTankOpenApi('API版本号不能为空','A001');
		}
		if(!isset($_POST['sign']) || empty(iPost('sign'))){
			$this->msgTankOpenApi('接口请求签名不能为空','A001');
		}
		
		$passTime = time()-iPost('timestamp');
		if($passTime>120){
			$this->msgTankOpenApi('请求数据信息超时','A001');
		}
		
		/* TANK令牌*/
		if(!isset($_POST['token']) || empty(iPost('token'))){
			$this->msgTankOpenApi('TANK令牌不能为空','A001');
		}
		if($_POST['token'] !== 'tanktest001')$this->msgTankOpenApi('TANK令牌信息错误', 'A001');
		
		$retSign = tankOpenApiSign($_POST,'djakhfiuaguaygf45fhagf');
		if(iPost('sign') != $retSign)$this->msgTankOpenApi('签名验证错误', 'A001');
		$methodArr = explode('.', strtolower(iPost('method')));
		$methodNum = count($methodArr);
		if($methodNum != 3) $this->msgTankOpenApi('请求的命令方法错误','A001');
		/* 类名*/
		$className = 'Openapi'.$methodArr[1];
// 		if(!$this->_existClassName($className)) $this->msgTankOpenApi('请求的命令方法错误','A001');
		/* 方法名*/
		$methodName = $methodArr[2].'OpenApi';
		//读取基本模块特殊配置
		$fileSrc =   APP_PATH.'api/apiconfig/'.strtolower($className).'.apiconf.php';
		if(!file_exists($fileSrc))$this->msgTankOpenApi('请求方法体文件不存在','A001');
		$dataArr = include_once $fileSrc;
		if(!is_array($dataArr))$this->msgTankOpenApi('请求方法体文件引用失败','A001');
		$newDataArr = array();
		foreach ($dataArr as $key=>$v){
			$newDataArr[strtolower($key)] = $v;
		}
		/* 方法名称*/
		$action = strtolower($methodName);
		if(!isset($newDataArr[$action]))$this->msgTankOpenApi('请求方法名称不存在','A001');
		
		/* 请求数据体*/
		$data = $_POST;
		/* 删除公共数据*/
		$publicPostData = array('token','method','timestamp','version','sign');
		foreach ($publicPostData as $pVo){
			unset($data[$pVo]);
		}
		
		foreach ($newDataArr[$action] as $nVo){
			if($nVo['required']){
				if(!isset($data[$nVo['name']]))$this->msgTankOpenApi('请求参数“'.$nVo['name'].'”不存在','A001');
				/* 验证上传数据不能为空*/
				if(empty($data[$nVo['name']]) && !is_numeric($data[$nVo['name']])){
					$this->msgTankOpenApi('请求参数“'.$nVo['name'].'”不能为空','A001');
				}
			}
			if(isset($data[$nVo['name']]))unset($data[$nVo['name']]);
		}
		/* 验证是否有多余参数信息*/
		if(!empty($data)){
			$duoYuData = array();
			foreach ($data as $dKey => $dVo){
				$duoYuData[] = $dKey;
			}
			$this->msgTankOpenApi('存在多余参数'.implode(',', $duoYuData).'请删除后重试！','A001');
		}
		
		
		$obj = MM($className);
		if(!method_exists($obj,$methodName))$this->msgTankOpenApi('请求的命令方法错误','A001');
		$obj->httpDataInfo = $_POST;
		$obj->$methodName();
	}
	
	/**
	 * 验证类名
	 * @param unknown $name
	 * @return boolean
	 */
	private function _existClassName($name){
		$classNameData = S_db_public('openapi_zizhuxinyi_class_public');
		if(!isset($classNameData[$name])){
			$classNameData = $this->__getClassNameFile();
		}
		
		if(isset($classNameData[$name])){
			return true;
		}else{
			return false;
		}
		return true;
	}

	private function __getClassNameFile(){
		
		$classNameNewArr = array();
		$handler = opendir(APP_PATH.'api/model/');
		while (($filename = readdir($handler)) !== false) {
			if ($filename != "." && $filename != "..") {
				$d = explode('model', $filename);
				$classNameNewArr[$d[0]] = true;
			}
		}
		closedir($handler);
		
		S_db_public('openapi_zizhuxinyi_class_public',$classNameNewArr);
		return $classNameNewArr;
	}
	
	
	
	/**
	 * 输出提示信息
	 * @param unknown $msg
	 * @param unknown $code
	 */
	private function msgTankOpenApi($msg,$code){
	
		echo msgTankOpenApi(1,$msg,$code);
		exit;
	}
}