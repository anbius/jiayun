<?php
namespace Api\Controller;
use Think\Controller;


/* 用户管理 */
class UserController extends ApiController {  
    public function index(){
        
    }
	//用户登录
	public function login()
    {
	   $member    = M('user');
       $user_name  = I('user_name');
       $user_pwd  = I('user_pwd');      
	   if(empty($user_name) || empty($user_pwd))json(4000,'数据缺失!');
	   $map = "user_name = '{$user_name}'";
	   $user = $member->field('id,user_pwd')->where($map)->find();
	 if(empty($user))json(4001,'该用户不存在!');			
	   if($user['user_pwd'] != $user_pwd)	json(4002,'密码错误!');
	   $res['token'] = $user['token'];
	   json(2000,'登录成功',$res);		
   }
	//用户注册
	public function register(){
		//header("Access-Control-Allow-Origin:*");
		$user = D('user');
		$data['user_name'] 	  = I('user_name');	
		$data['user_pwd']  	  = I('user_pwd');
		$code       		  = I("code");//验证码
		
		$data['create_time']  = time();
		if(empty($code)){
			json('4002','验证码没有填写');
		}
		if(strlen($data['user_pwd'])<6 ||strlen($data['user_pwd'])>16 ){
			json('4003','密码不符合格式');
		}
		//验证验证码
		$virify = M("sms")->field("code,send_time")->where("mobile = '{$data['user_name']}'")->find();
		
		if($virify){
            if(trim($virify["code"]) != $code){
                json(4004,'验证码错误！');
            }else{
               if(time() > ($virify["send_time"]+360))json(4005,'验证码已过期请您重新发送!');// 180过后就过期
            }
        }else{
			
			 json(4005,'验证码错误！');
		}
		
	
		$rs = $user->where('user_name = '.$data['user_name'])->find();
		if($rs){
		  json(4006,'用户名已经注册');	
		}
		$data['token']        = md5($data['user_name']);
		if($user->create($data)){
			$rs = $user->add();
			registerToken($data['user_name'],$data['user_pwd']);	//环信注册用户	
			if($rs){
				json(2000,'注册成功');	
			}else{
				json(4001,'注册失败',$rs);
			}
		}else{
			json(4000,$user->getError());
		}
	}
    //忘记密码
	public function forgetPassword(){
		$user = M('user');	
		$mobile_phone 	   = I('phone');	
		$data['user_pwd']  = I('user_pwd');
		$code   		   = I("code");//验证码	
		$data['mobile_phone'] =  $mobile_phone;
		$data['code']         =  $code;	
		if(empty($mobile_phone) || empty($data['user_pwd'])|| empty($code))json(4000,'数据缺失!',$data);	
		//验证验证码
				
		
        $virify = M("sms")->field("code,send_time")->where('mobile = '."{$mobile_phone}")->find();
		
        if($virify){
            if(trim($virify["code"]) != $code){
                json('4002','验证码错误！');
            }
        }else{
            json('4004','您还没有点击发送验证码',$data);
        }
		//修改环信密码		
		$rs = changePwdToken($mobile_phone,$data['user_pwd']);
		if($rs == 400){
			json('4001','修改失败1');	
		}
		$map['user_name'] = array("eq",$mobile_phone);
		$rs = $user->where($map)->save($data);
		if($rs){
			json('2000');	
		}else{
			json('4001','修改失败');
		}		
	}   
	
	//上传
	 public function setImage()
     {
		$token = I('token');
        $file_name = base64_decode($_REQUEST['pic']);
		// 打开新建文件
        $esc = time().'.jpg';
        $arr = 'jk.jianong.com/Public/images/'.$esc;
        $file = fopen($arr,"w");
        fwrite($file,$file_name);
        fclose($file);
		// 判断
        if (!$file) {
			
			json('4000','上传失败');
		}else{
			$data['cover'] = $arr;
			$rs = M('user')->where('token = '.$token)->save($data);	
			if($rs){
			 	json('2000','上传成功',$data);
			}else{
				json('4001','上传失败',$data);
			}
		}
       }
	   //获取
	 public function getImage()
     {
		$token = I('token');
        $data  = M('user')->where('token = '.$token)->field('cover')->find();
		// 判断
        if ($data) {
			json('2000','获取成功',$data);
		}else{
			json('4001','获取失败');
		}
     }
	//获取用户信息
	public function getUserInfo(){
		//header("Access-Control-Allow-Origin:*");
		$token = I('token');
		if(empty($token))json(4000,'参数缺失!');	
		$field = "user_name as mobile,real_name,sex,wx_code,email,area,addr,animal_id,day_old,size,chicken_date,pig_date";
		$data = M('user') ->where("token = '".$token."'")->$field($filed)->find();
		
		if($data){
		 	json('2000','获取成功',$data);	
			
		}else{
			json('4000','获取失败');
		}	
	}
	//设置用户信息
	public function setUserInfo(){
		$token = I('token');
		if(empty($token))json(4000,'参数缺失!');	
		//$data['area'] 	   = I('area');	
		$data['real_name'] 		= I('real_name');
		$data['sex'] 		    = I('sex');
		$data['wx_code'] 		= I('wx_code');
		$data['email'] 		    = I('email');
		$data['area'] 		    = I('area');
		$data['addr'] 		    = I('addr');
		$data['animal_id'] 		= I('animal_id');
		$data['day_old'] 		= I('day_old');
		$data['chicken_date'] 	= I('chicken_date');
		$data['pig_date'] 		= I('pig_date');	
		$rs = M('user')->where('token = '.$token)->save($data);
		if($rs != false){
			\Org\Response::show(2000,'');
		}else{
			\Org\Response::show(4001,'设置失败！');	
		}
	}
    //聊天用户备注设置备注
	public function setFreNote(){
		$uid      = I('uid');
		$fname 	  = I('fname');
		$notename = I('notename');
		if(empty($uid) || empty($fname) || empty($notename))\Org\Response::show(4000,'参数缺失!');			
		$rs = D("Friend/Friend")->setOne($uid,$fname,$notename);
		if($rs){
			\Org\Response::show(2000,'',$rs);	
		}else{
			\Org\Response::show(4001,'设置失败！');
		}
	}
	
	//获取用户备注
	public function getFreNote(){
		$uid   = I('uid');
		$fname = I('fname');
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');				
		$rs = D("Friend/Friend")->getOne($uid,$fname);
		$data = '';
		if($fname){			
			$fname_arr = explode(',',$fname);				
			foreach($fname_arr as $k=>$fn){
				$data[$k]['fname'] = $fn;
				if($rs){
					foreach($rs as $n){					
						if($n['fname'] == $fn){
							$data[$k]['notename'] = $n['notename'];	
						}else{
							$data[$k]['notename'] = '';		
						}	
					}
				}else{
					$data[$k]['notename'] = '';
				}
			}
		}else{
			$data = $rs;	
		}
		
		\Org\Response::show(2000,'',$data);	
	} 
	
	//获取用户所有备注
	public function getAllFreNote(){
		$uid   = I('uid');		
		if(empty($uid))\Org\Response::show(4000,'参数缺失!');				
		$rs = D("Friend/Friend")->getALL($uid);
		\Org\Response::show(2000,'',$data);	
	}
	
	
	public function error($info){
		echo json_encode(array('retcode' => 4000, 'info' => $info));
		die();
	}
	
}