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
	   $user = $member->field('id,user_pwd,token')->where($map)->find();
	 
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
			json('2000','修改成功');	
		}else{
			json('4001','修改失败');
		}		
	}   
	
	//上传 
	 public function setImage(){
		$token = I('token');
		if(empty($token))json(4000,'参数缺失!');	
		$id = $this->uploadImage();		
		if($id){
		  $map['cover']   = $id;
		  $res1 = M('user')->where("token = '{$token}'")->save($map);
		  json('2000','上传成功',array('id'=>$id));
		}else{
		  json('4004','上传失败')	;
		}
	}
	 //获取
	 public function getImage()
     {
		$map['id'] = I('id');
		if(empty($map['id']))json(4000,'参数缺失!');	
       	//$res = getUserInfos($token);
		$path = M('picture')->where($map)->getField('path');		
		if($path){
		 	json('2000','获取成功',array('path'=>HTTP.$path));	
		}else{
			json('4002','获取失败');
		}
     }
	//获取用户信息
	public function getUserInfo(){
		//header("Access-Control-Allow-Origin:*");
		$token = I('token');
		if(empty($token))json(4000,'参数缺失!');	
		$field = "user_name as mobile,cover,real_name,sex,wx_code,email,area,addr,animal,size";
		$data = M('user') ->where("token = '".$token."'")->field($field)->find();
		
		foreach($data['animal'] as $k=>$v){
			
			  $data['title'][] = getAnimalField($v);
			
		}
		$data['animal'] = implode(',',$data['title']);
	  
	    if($data['sex']==1){			
			$data['sex']="男";
		}else{
			
			$data['sex'] = "女";
		}
		$data['user_pwd'] = '';
		if(is_null($data['real_name']))$data['real_name'] = "";
		if(empty($data['cover']))$data['cover'] = 23;
		if(is_null($data['wx_code']))$data['wx_code'] = "";
		if(is_null($data['email']))$data['email'] = "";
		if(is_null($data['area']))$data['area'] = "";
		if(is_null($data['animal']))$data['animal'] = "";	
		if(is_null($data['size']))$data['size'] = "";
		if(is_null($data['sex']))$data['sex'] = "";
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
		$data['animal'] 		= I('animal');		
		$data['size'] 			= I('size');
		$rs = M('user')->where("token = '{$token}'")->save($data);
		
		
		if($rs != false){
			\Org\Response::show(2000,'');
		}else{
			\Org\Response::show(4001,'设置失败！');	
		}
	}
	
}