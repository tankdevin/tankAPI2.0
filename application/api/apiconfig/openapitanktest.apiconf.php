<?php
return array(
		//接口私参数验证配置
	'getUserInfoOpenApi'=>array(
		array(
				'name'=>'type',
				'required' => true,
				'type'=>'int',
		),
		array(
				'name'=>'phone',
				'required' => false,
				'type'=>'string',
		),
		array(
				'name'=>'ucode',
				'required' => false,
				'type'=>'int',
		)
	),
);