<?php
namespace app\api\model;

use app\api\model\Common;
/**
 * TP5API测试相关操作
 * @author tank
 *
 */
class Openapitanktest extends Common{
    
    /**
     * API测试方法
     */
    public function getUserInfoOpenApi(){
        
        $httpData = $this->httpDataInfo;
        /*
         * 这里具体操作
         */
        $this->retMsgtank('测试通过',1);
    }
    
}