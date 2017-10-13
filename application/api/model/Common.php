<?php
namespace app\api\model;

use think\Model;
/**
 * 公共模型类
 * @author tank
 *
 */
class common extends Model {
	
	public $httpDataInfo = '';		/* 用户请求数据体信息*/
	public $sysNeiBuHttp = false;	/* 系统内部调用API接口*/
	public $sysOrderno = '';		/* 系统订单编号*/
	public $sysDefaultMsg = '';		/* 系统默认提示信息*/
	
	protected $errorMsg = '';/*错误信息*/
	
	
	/**
	 * 错误信息
	 * @param unknown $msg
	 * @return boolean
	 */
	protected function setError($msg){
		if(!empty($this->sysOrderno)){
			$msg .= '-单号:'.$this->sysOrderno;
		}
		$this->errorMsg = $this->sysDefaultMsg.$msg;
		return false;
	}
	
	/**
	 * 获取错误数据信息
	 * @return string
	 */
	public function getError(){
	
		return $this->errorMsg;
	}
	
	/**
	 * Tank开放OpenAPI接口输出信息
	 * @param unknown $msg
	 * @param unknown $code
	 * @param unknown $data
	 * @param string $exit
	 */
	protected function retMsgtank($msg,$code,$data='',$exit=true){
		echo msgTankOpenApi(1,$msg,$code,$data);
		if($exit)exit;
	}
}