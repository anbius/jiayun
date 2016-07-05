<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");
class CodeController extends Controller {		
	/**
	* 发送验证短信
	* @param integer mobile 手机号
	* return json
	*/
    public function sendVerCode(){
		$mobile  = I("mobile");//手机号
		$phone   = I('phone');
		if(empty($mobile)){
			$mobile = $phone;
		}
        
        if(empty($mobile))json(4000,'手机号为空！');
        $rs = M("sms")->where("mobile = '{$mobile}'")->find();
      //  $user          = getC('SMS_USER');//获取用户名
       // $key           = getC('SMS_KEY');//获取key
        $data["code"]  = mt_rand(1000,9999);//生成随机的四位数
       // $content       = getC('SMS_CONTENT');
        $content       = str_replace("{CODE}", $data["code"], $content);//获取短信内容
        $data["send_time"] = time();//短信发送时间
        $is_sucess = false;
		$rs1 = m('user')->where("user_name = '{$mobile}'")->find();
		if(empty($rs1)){
			if($rs){
				if(time() < ($rs["send_time"]+60))json(4001,'60秒内不能重复发送!');// 60秒不能重复发送
				$is_sucess = M("sms")->where("mobile = {$mobile}")->save($data);
			}else{//当前手机号不存在短信表里
				$data['mobile'] = $mobile;
				$is_sucess = M("sms")->add($data);
				
			}
			if($is_sucess){ 
				/*$str = "您的验证码为{$data['code']}有效时间3分钟";
				$str .= "【嘉云健康】";
				$str = rawurlencode(mb_convert_encoding($str, "gb2312", "utf-8"));
				$url   = "http://yzm.mb345.com/ws/BatchSend.aspx?CorpID=BJLK096&Pwd=asdzxc111@&Mobile={$mobile}&Content={$str}&Cell=&SendTime=";
				$result = file_get_contents($url);
				if($result>0){
					json(2000,'发送成功!', array('code' => $data["code"]));
					} */ 
				json(2000,'发送成功!', array('code' => $data["code"]));          
			}else{
				json(4002,'发送失败!');
			}
		}else{
			
			 json(4003,'该手机号已注册');
		}
   }
  
  		/**
	* 发送验证短信
	* @param integer mobile 手机号
	* return json
	*/
    public function sendVerCodes(){
        $mobile  = I("mobile");//手机号
		$phone   = I('phone');
		if(empty($mobile)){
			$mobile = $phone;
		}
        
        if(empty($mobile))json(4000,'手机号为空！');
        $rs = M("sms")->where("mobile = '{$mobile}'")->find();
       
        $data["code"]  = mt_rand(1000,9999);//生成随机的四位数
       
        $content       = str_replace("{CODE}", $data["code"], $content);//获取短信内容
        $data["send_time"] = time();//短信发送时间
        $is_sucess = false;
		$rs1 = m('user')->where("user_name = '{$mobile}'")->find();
		
		if(empty($rs1)){
			
			json('4004','该用户没有注册');
		}
			if($rs){
				if(time() < ($rs["send_time"]+60))json(4001,'60秒内不能重复发送!');// 60秒不能重复发送
				$is_sucess = M("sms")->where("mobile = {$mobile}")->save($data);
			}else{//当前手机号不存在短信表里
				$data['mobile'] = $mobile;
				$is_sucess = M("sms")->add($data);
				
			}
			if($is_sucess){ 
				/*$str = "您的验证码为{$data['code']}有效时间3分钟";
				$str .= "【嘉云健康】";
				$str = rawurlencode(mb_convert_encoding($str, "gb2312", "utf-8"));
				$url   = "http://yzm.mb345.com/ws/BatchSend.aspx?CorpID=BJLK096&Pwd=asdzxc111@&Mobile={$mobile}&Content={$str}&Cell=&SendTime=";
				$result = file_get_contents($url);
				if($result>0){
					json(2000,'发送成功!', array('code' => $data["code"]));
					}  */
				json(2000,'发送成功!', array('code' => $data["code"]));             
			}else{
				json(4002,'发送失败!');
			}
		
   }
	
}