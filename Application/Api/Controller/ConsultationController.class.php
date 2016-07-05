<?php
namespace Api\Controller;
use Think\Controller;

/* 咨询接口管理 */
class ConsultationController extends ApiController {  
    
	/*
	 * 添加咨询
	 * @param string @token 用户token
	 * @param int @arc_id 档案ID
	 * @param int @vet_id 兽医ID
	 * @output json
	 */
	public function submitContent(){
		$token   = I('token');    //获取token值
		if(empty($token))json(4000,'数据缺失!');		
		$data['arc_id'] = I('arc_id');
		if(empty($data['arc_id']))json(4001,'数据缺失!');		
		//$data['vet_id'] = I('vet_id');
		if(empty($token))json(4002,'数据缺失!');		
		$data['uid'] = getUid($token);  //获取uid		
		$data['create_time'] = time();
		
				//获取所有在线的兽医
		$vets = D('veterinary')->getOnline();
		if(empty($vets))json(4003,'没有在线的医生');
		$vets = $vets[0];//获取排名第一的医生
		//把用户和兽医在环信添加成好友
		//1、获取用户和兽医的手机号
		$user_name = M("user")->where("id = ".$data['uid'])->getField("user_name");
		//$vet_phone  = M("veterinary")->where("id = ".$vets['vet_id'])->getField("phone");
		
		//加为好友
		$huanxin = new \Org\Huanxin();
		$huanxin->addFriend($user_name,$vets['vete_name']);
		
		$data['vet_id '] = $vets['vet_id'];
		$rs = M("consultation")->add($data);
		
		if($token){
			//获取当前用户是否关注
			$map['cate'] 	= array('eq',2);
			$map['coll_id'] = array('eq',$vets['id']);
			$map['uid'] 	= $data['uid'];
			$data['is_collect'] = M('collect')->where($map)->getField("id")?1:0;	
		}else{
			$data['is_collect'] = 0 ;
		}
		
		$rs = array(
					'id'=>$rs,
					'hx_vet_id'	=> $vets['vete_name'],
					'vet_id'	=> $vets['id'],
					'real_name'	=> $vets['real_name'],
					'level'	=> $vets['level'],
					'info'	=> $vets['info'],
					'cover'		=> getPic($vets['cover']),
					'is_collect'=> $data['is_collect'], 
		);			
		//返回兽医环信id
		json(2000,'成功',$rs); 
	}
	
	/*
	 * 获取我的咨询
	 * @param string @token 用户token	
	 * @output json
	 */
	public function getList(){
		$rs = $this->isUsers();
		if($rs){
			$data = array();
			foreach($rs as $k=>$v){
				$data[$k]['id']             = $v['id'];
				//获取咨询的品种名称
				$animal_id = M('archives')->where('id = '.$v['arc_id'])->field('animal_id')->find();	
			
				$data[$k]['title'] 	   = getAnimalField($animal_id['animal_id']);
				//获取咨询的医生
				$vete_name = M('veterinary')->where('id='.$v['vet_id'])->getField('vete_name');
				$data[$k]['vete_name']      = $vete_name;
				$data[$k]['time']    		= date('Y-m-d H:i:s',$v['create_time']);
				$data[$k]['is_finish']	    = $v['is_finish'];
				
			}
			json('2000','获取成功',$data);
		}else{
			json('2000','没有咨询');	
		}
	}
	
	//获取我的兽医（咨询过的医生）
	 public function getVeterinary(){
		$rs = $this->isUsers();
		if($rs){
			foreach($rs as $k=>$v){
				//获取咨询的医生
				$data[$k]['id'] = $v['vet_id'];    //获取兽医的id
				$vete  = M('veterinary')->where('id='.$v['vet_id'])->field('vete_name,description,cover,level')->find();
				$data[$k]['vete_name']      = $vete['vete_name']; //获取兽医的名字
				$data[$k]['description']    = $vete['description']; //获取兽医的描
				$data[$k]['level']      	= $vete['level']; //获取兽医的级别
				$data[$k]['cover'] 			= HTTP.pic($vete['cover']);
				$res = M('Collect')->where('uid = '.$v['uid'].' and cate = 2 and coll_id='.$v['vet_id'])->find();
				if($res){
					$data[$k]['is_gz'] = "1";	
				}else{
					$data[$k]['is_gz'] = "0";
				}
			}
			json('2000','获取成功',$data);
		}else{
			json('2000','没有咨询');	
		}
	}
	
	//获取我的检测
	 public function getSrtc(){
		$rs = $this->isUsers();
		
		if($rs){
			foreach($rs as $k=>$v){
				//获取咨询的检测中心
				$data[$k]['id'] = $v['srtc_id'];    //获取检测中心
				$srtc  = M('srtc')->where('id='.$v['srtc_id'])->field('srtc_name,mobile')->find();
				$data[$k]['srtc_name']      = $srtc['srtc_name']; 
				$data[$k]['title']    		= "采样单"; 
				$data[$k]['mobile']    		= $srtc['mobile']; 
				$data[$k]['time']    		= date('Y-m-d H:i:s',$v['create_time']);
				$data[$k]['is_finish']	    = $v['is_finish'];
		}
			json('2000','获取成功',$data);
		}else{
			json('2000','没有检测');	
		}
	}
	/*
	 * 确认咨询完成
	 * @param int @id 咨询id
	 * @param string @token 用户token
	 * @output json
	 */
	public function setFinished(){
		$id      = I('id');
		if(empty($id))	 json(4000,'数据缺失!');
		$rs = $this->isUser($id);
		//判断是否是合法
		if($rs){
			$data['id'] 	   = $id;
			$data['is_finish'] = 1;
			$rs = M('consultation')->save($data);
			json(2000,'设置成功！');
		}else{
			json(4002,'非法用户！');	
		}
	}	
	
	
	/*
	 * 获取咨询状态
	 * @param int @id 咨询id
	 * @param string @token 用户token
	 * @output json
	 */
	public function getStatus(){
		$id      = I('id');	
		if(empty($id))	 json(4000,'数据缺失!');
		$rs = $this->isUser($id);
		//判断是否是合法
		if($rs){
			$is_finish = M('consultation')->where("id = $id")->getField('is_finish');
			json(2000,'',array('is_finish'=>$is_finish));
		}else{
			json(4002,'非法用户！');
		}
	}
	
	/*
	 * 是否为该用户
	 * @param int @id 咨询id
	 * @param string @token 用户token
	 * @output json
	 */
	 private function isUser($id){			
		$token   = I('token');    //获取token值
		if(empty($token))json(4001,'数据缺失!');
		$uid = getUid($token);  //获取uid
		$map['id']  = array('eq',$id);
		$map['uid'] = array('eq',$uid);
		$rs = M('consultation')->where($map)->field('id')->find();
		return $rs?true:false;
	 }
	 private function isUsers($id){			
		$token   = I('token');    //获取token值
		if(empty($token))json(4001,'数据缺失!');
		$uid = getUid($token);  //获取uid
		$map['uid'] = array('eq',$uid);
		$rs = A('Api')->lists(M('consultation'),$map,$order='create_time desc',$field="id,uid,arc_id,srtc_id,vet_id,is_finish,create_time");
		return $rs;
	 }
		
	
}