<?php
namespace Api\Controller;
use Think\Controller;
/* 用户管理 */
class UserController extends ApiController {  
    public function index(){
        
    }
	
	//用户登录
	public function login(){
		$user_name = I('user_name');
		$user_pwd  = I('user_pwd');		
		if(empty($user_name) || empty($user_pwd))\Org\Response::show(4000,'数据缺失!');	
		
		//$map['user_name'] = array('eq',$user_name);
		$map = "user_name = '{$user_name}' or mobile_phone = '{$user_name}'";
		$user = D('dt_manager')->field('id,user_pwd')->where($map)->find();
		if(empty($user))\Org\Response::show(4001,'该用户不存在!');			
		if($user['user_pwd'] != $user_pwd)	\Org\Response::show(4002,'密码错误!');
		
		\Org\Response::show(2000,'',$user['id']);		
	}
	//用户注册
	public function register(){
		//header("Access-Control-Allow-Origin:*");
		$user = D('DtManager');
		$data['user_name'] 	  = I('user_name')?I('user_name'):I('mobile_phone');	
		$data['user_pwd']  	  = I('user_pwd');
		$data['mobile_phone'] = I('mobile_phone');	
		$data['work_name'] = I('mobile_phone');
		$data['wordtype'] = I('wordtype','000');	
		$data['work_location'] = I('work_location','000');
		$data['work_code'] = I('work_code','000');
		$data['word_corporation'] = I('word_corporation','000');
		$data['websites'] = I('websites','000');
		$code       		  = I("code");//验证码	
		//logDebug(json_encode($data));
		//验证验证码
		
        $virify = M("sms")->field("code,send_time")->where("mobile = '{$data['mobile_phone']}'")->find();
		
        if($virify){
            if(trim($virify["code"]) != $code){
                \Org\Response::show(4002,'验证码错误！');
            }else{
                if(time() > ($virify["send_time"]+180))\Org\Response::show(4007,'验证码已过期请您重新发送!');// 180过后就过期
            }
        }else{
            \Org\Response::show(4004,'您还没有点击发送验证码！');
        }
		
		if($user->create($data)){
			$rs = $user->add();	
			registerToken($data['mobile_phone'],$data['user_pwd']);	//环信注册用户	
			if($rs){
				\Org\Response::show(2000,'',$data['user_name']);	
			}else{
				\Org\Response::show(4001,'注册失败',$rs);
			}
		}else{
			\Org\Response::show(4000,$user->getError());
		}
	}
	
	//忘记密码
	public function forgetPassword(){
		$user = D('DtManager');	
		$mobile_phone 	   = I('mobile_phone');	
		$data['user_pwd']  = I('user_pwd');
		$code   		   = I("code");//验证码		
		if(empty($mobile_phone) || empty($data['user_pwd'])|| empty($code))\Org\Response::show(4000,'数据缺失!');	
		//验证验证码		
        $virify = M("sms")->field("code,send_time")->where("mobile = '{$mobile_phone}'")->find();		
        if($virify){
            if(trim($virify["code"]) != $code){
                \Org\Response::show(4002,'验证码错误！');
            }else{
                //if(time() > ($virify["send_time"]+180))\Org\Response::show(4007,'验证码已过期请您重新发送!');// 180过后就过期
            }
        }else{
            \Org\Response::show(4004,'您还没有点击发送验证码！');
        }
		//修改环信密码		
		$rs = changePwdToken($mobile_phone,$data['user_pwd']);
		if($rs == 400){
			\Org\Response::show(4001,'修改失败1');	
		}
		$map['mobile_phone'] = array("eq",$mobile_phone);
		$rs = $user->where($map)->save($data);	
		if($rs){
			\Org\Response::show(2000);	
		}else{
			\Org\Response::show(4001,'修改失败');
		}
		
	}
	
	public function testPwd(){
		$rs = changePwdToken("18801214077","123456");
		dump($rs);	
	}
	
	//获取用户信息
	public function getUserInfo(){
		//header("Access-Control-Allow-Origin:*");
		$id = I('id');
		if(empty($id))\Org\Response::show(4000,'参数缺失!');	
		$data = D('dt_manager')->field('user_name,work_name,word_corporation,real_name,area,work_add,telephone')->where("id = $id")->find();		
		\Org\Response::show(2000,'',$data);	
	}
	//设置用户信息
	public function setUserInfo(){
		$id   	   = I('id');
		//$data['area'] 	   = I('area');	
		$work_name 		  = I('work_name');
		$real_name 		  = I('real_name');
		$word_corporation = I('word_corporation');
		$work_add 		  = I('work_add');
		$telephone 		  = I('telephone');	
			
		$sql = "update dt_manager set work_name='{$work_name}',real_name='{$real_name}',word_corporation='{$word_corporation}',work_add='{$work_add}',telephone='{$telephone}' where id={$id}";
		
		$rs = D('dt_manager')->execute($sql);
		
		if($rs){
			\Org\Response::show(2000,'');
		}else{
			\Org\Response::show(4001,'设置失败！');	
		}
	}
	
	/*发送验证短信*/
	public function sendVerCode() {
		$info = array('status' => false, 'info' => '');		
        $data['mobile']     = I('phone');
        $data["code"]  	   = mt_rand(1000,9999);//生成随机的四位数
        $data["send_time"] = time();//短信发送时间
        $res = M('sms') -> create($data);	
		if (!$res) {//数据对象创建错误
			$this->error(M('sms') -> getError());			
		}
        $is_sucess = false; 
		$rs = M('sms')->where("mobile = '".$data['mobile']."'")->find();
        if($rs){//当前手机号存在短信表里
            if(time() < ($rs["send_time"]+60)){
            	$this->error('60秒内不能重复发送!');// 60秒不能重复发送            	
            }
            $is_sucess = M('sms')->where("mobile = '".$data['mobile']."'")->save($data);
        }else{//当前手机号不存在短信表里            
            $is_sucess = M('sms')->add($data);
        }		
        if($is_sucess){
        	$user          = 'BJLK096';//获取用户名
	        $key           = 'asdzxc111@';//获取key        
		  	$str = "您的验证码为{$data['code']}有效时间3分钟";
			$str .= "【蛋鸡管家】";
			$str = rawurlencode(mb_convert_encoding($str, "gb2312", "utf-8"));
			$url =  "http://yzm.mb345.com/ws/BatchSend.aspx?CorpID=BJLK096&Pwd=asdzxc111@&Mobile={$data['mobile']}&Content={$str}&Cell=&SendTime=";
            $rs = file_get_contents($url);//获取短信返回值
			
			if($rs>0){
				\Org\Response::show(2000,'发送成功');
				}			       
			$info['status'] = true;
        }else{
            $this->error('发送失败!');
        }
		//echo json_encode(array('retcode' => 2000));
	}
	
	//聊天用户备注设置备注
	public function setFreNote(){
		
	}
	
	//获取用户备注
	public function getFreNote(){
			
	} 
	
	
	public function error($info){
		echo json_encode(array('retcode' => 4000, 'info' => $info));
		die();
	}
	
}