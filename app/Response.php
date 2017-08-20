<?php
	namespace App;
	/*
	 * 
	 */
	class Response{
		const SUCCESS = 1;
		const FAILED = 0;
		const USER_NOT_FOUND = 10001;
		const PASSWORD_INCORRECT = 10002;
		const MODIFY_USER_INFO_FAILED = 10003;
		const NO_MORE_INFO = 10004;
		const WRONG_OPERATION = 10005;
		const WRONG_PARAMS = 10006;
		const WRONG_IMG_PATTERN = 10007;
		const SAVE_IMG_FAILED = 10008;
		const CHECKOUT_FAILED = 10009;
		
		static public function getResponseMsg($code = Response::SUCCESS){
			switch($code){
				case self::SUCCESS:
					return '请求成功';
				case self::FAILED:
					return '请求失败';
				case self::USER_NOT_FOUND:
					return '用户不存在';
				case self::PASSWORD_INCORRECT:
					return '密码不正确';
				case self::MODIFY_USER_INFO_FAILED:
					return '修改用户信息失败';
				case self::NO_MORE_INFO:
					return '没有更多信息';
				case self::WRONG_OPERATION:
					return '操作失败';
				case self::WRONG_PARAMS:
					return '参数错误';
				case self::WRONG_IMG_PATTERN:
					return '图片格式错误';
				case self::SAVE_IMG_FAILED:
					return '保存图片失败';
					case self::CHECKOUT_FAILED:
					return '结账失败';
				default:
					return '未知错误';
			}
		}
		
	}
	
