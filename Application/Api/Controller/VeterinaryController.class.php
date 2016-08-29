<?php
namespace Api\Controller;
use Think\Controller;

class VeterinaryController extends ApiController {  
    protected $model;	
    protected function _initialize()
    {       
        $this->model = D('veterinary');      
    }
	
	/*
	 * 获取兽医信息  今天是2016年7月5号  萧亚轩 最熟悉的陌生人haah
	 * 今天是 2016 年8月29号
	 */
	public function getList(){
		$map['status'] = array('eq',1);

        /*我就是试试*/
        $map['test'] =  222;
        /*我真是试试*/

		$list = $this -> lists($this->model,$map,'sort asc,id desc','id,vete_name,real_name,cover,description,level,create_time as time');	
		if($list){
			$huanxin = new \Org\Huanxin();//环信类	
			foreach($list as $key=>$value){			
				$list[$key]['cover'] = HTTP.pic($value['cover']?$value['cover']:23);				
				$list[$key]['is_online'] = $huanxin->isOnline($value['vete_name']);			
			}
		}
		
		$data['list'] = $list?$list:array();
		$data['total']['people_num'] 	  = $this->model->where($map)->count('id');
		$map['work_status'] = 0;
		$data['total']['free_people_num'] = $this->model->where($map)->count('id');		
		echo json(2000,'',$data);
	}
	
	
	/*
	 * 获取我咨询的兽医信息
	 */
	public function getLists(){
		$rs = $this->isUsers();
		if($rs){
			foreach($rs as $k=>$v){
				//获取咨询的医生
				$data[$k]['id'] = $v['vet_id'];    //获取兽医的id
				$vete  = M('veterinary')->where('id='.$v['vet_id'])->field('vete_name,description,cover,level')->find();
				$data[$k]['vete_name']      = $vete['vete_name']; //获取兽医的名字
				$data[$k]['description']    = $vete['description']; //获取兽医的描
				$data[$k]['level']      	= $vete['level']; //获取兽医的级别
				$data[$k]['cover'] 			= HTTP.pic($vete['cover']?$vete['cover']:23);
				$res = M('Collect')->where('uid = '.$v['uid'].' and cate = 2 and coll_id='.$v['vet_id'])->find();
				if($res){
					$data[$k]['is_gz'] = "1";	
				}else{
					$data[$k]['is_gz'] = "0";
				}
			}
			json('2000','获取成功',$data);
		}else{
			json('4001','没有咨询');	
		}
	}
	/*
	 * 获取兽医详情
	 */
	public function getDetail(){		
		$id = I('id');
		if(empty($id))json(4000,'数据缺失!');	
		$token = I('token');
		//if(empty($token))json(4001,'token丢失');	
		$field = 'id,real_name,level,description,info,speciality,cover';
		$data = $this->model->field($field)->find($id);
		//获取图片
		$data['cover'] = HTTP.pic($data['cover']);
		
		if($token){
			//获取当前用户是否关注
			$map['cate'] 	= array('eq',2);
			$map['coll_id'] = array('eq',$id);
			$map['uid'] 	= getUid($token);
			$data['is_collect'] = M('collect')->where($map)->getField("id")?1:0;	
		}else{
			$data['is_collect'] = 0 ;
		}
			
		//获取好评率
		$map_eval['category'] = array('eq',1);
		$map_eval['eval_id']  = array('eq',$id);
		//非常满意所占的比重		
		$total = M('evaluate')->where($map_eval)->count('id');//获取总数				
		$map_eval['score']  = array('eq',5);//五分的数量
		$score = M('evaluate')->where($map_eval)->count('id');
		$data['score']  = $score?ceil(($score/$total)*100):100;
		
		//获取回答数
		$map_con['vet_id'] = array("eq",$id);
		$data['answers'] = M('consultation')->where($map_con)->count("id");		
		echo json(2000,'',$data);
	}
	
	
	
	/*
	 * 获取兽医评价信息
	 */
	public function getComment(){
		$id = I('id');
		if(empty($id))json(4000,'数据缺失!');	
		$map['category'] = 1;
		$map['eval_id']  = I('id');
		$list = $this -> lists(M('evaluate'),$map,'','id,content,create_time,uid,score');		
		if($list){
			foreach($list as $key=>$value){
				$user = getUserInfo($value['uid']);
				$arr['user_name'] 	= $user['user_name']?$user['user_name']:'匿名'; 
				$arr['cover'] 		= $user['cover'];
				$arr['content'] 	= $value['content']; 
				$arr['score'] 		= $value['score']; 				
				$arr['create_time'] = $value['create_time'];
				$list[$key] = $arr;
			}		
		}
		echo json(2000,'',$list);
	}
	
	/*
	 * 添加兽医评价信息
	 */
	public function setComment(){		
		$data['token']   = I('token');
		$data['eval_id'] = I('id');
		$data['content'] = I('content');
		$data['score']   = I('score');
		$data['con_id']  = I('con_id');
		D('Veterinary')->pram($data);	
	}
	

	
	public function test(){
		json_test(200,'',false);	
	}
	
	
	//---兽医登录注册管理-----------------------------------------------------------------	
	
	//用户登录
	public function login()
    {	   
       $user_name  = I('vete_name');
       $user_pwd   = I('vete_pwd');
	   if(empty($user_name) || empty($user_pwd))json(4000,'数据缺失!');
	   $user_name  = C('DOC_PREFIX').$user_name;
	   $map = "vete_name = '{$user_name}'";
	   $user = $this->model->field('id,vete_pwd')->where($map)->find();
	   if(empty($user))json(4001,'该用户不存在!');	
	   if($user['vete_pwd'] != $user_pwd)json(4002,'密码错误!');
	   $res['token'] = $user['token'];
	   json(2000,'登录成功',$res);
   }
	//用户注册
	public function register(){	
		$data['vete_name'] 	  = I('vete_name');
		$data['vete_pwd']  	  = I('vete_pwd');
		$code       		  = I("code");//验证码
		$data['mobile']  	  = I('vete_name');
		$data['create_time']  = time();
		
		if(empty($data['vete_name'] ))json('4000','参数缺失');
		if(empty($code))json('4002','验证码没有填写');
		if(strlen($data['vete_pwd'])<6 ||strlen($data['vete_pwd'])>16 )json('4003','密码不符合格式');
		//验证验证码
		$virify = M("sms")->field("code,send_time")->where("mobile = '{$data['vete_name']}'")->find();		
		if($virify){
            if(trim($virify["code"]) != $code){
                json(4004,'验证码错误！');
            }else{
               if(time() > ($virify["send_time"]+360))json(4005,'验证码已过期请您重新发送!');// 180过后就过期
            }
        }else{			
			 json(4005,'验证码错误！');
		}	
		$data['vete_name'] = C('DOC_PREFIX').$data['vete_name'];//用户名添加兽医标识
		$rs = $this->model->where('user_name = '.$data['vete_name'])->find();
		if($rs)json(4006,'用户名已经注册');
		$data['token']        = md5($data['vete_name']);
		if($this->model->create($data)){
			$rs = $this->model->add();
			registerToken($data['vete_name'],$data['vete_pwd']);	//环信注册用户	
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
		$mobile_phone 	   = I('phone');
		$data['vete_pwd']  = I('vete_pwd');
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
		$map['vete_name'] = C('DOC_PREFIX').$mobile_phone;
		//修改环信密码		
		$rs = changePwdToken($map['vete_name'],$data['vete_pwd']);
		if($rs == 400){
			json('4001','修改失败1');	
		}
		$rs = $this->model->where($map)->save($data);
		/*logDebug("sql：".$user->_sql());
		logDebug("rs结果：".json_encode($rs));*/
		if($rs){
			json('2000','修改成功');	
		}else{
			json('4001','修改失败');
		}		
	}  
	
	 private function isUsers($id){			
		$token   = I('token');    //获取token值
		if(empty($token))json(4001,'数据缺失!');
		$uid = getUid($token);  //获取uid
		$map['uid'] = array('eq',$uid);
		
		$rs = A('Api')->lists(M('consultation'),$map,$order='create_time desc',$field="id,uid,arc_id,vet_id,is_finish,create_time");
		return $rs;
	 }
	
}







