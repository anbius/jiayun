<?php
namespace Api\Model;
use Think\Model;
/**
 * 兽医模型
 */
class VeterinaryModel extends Model{
   
   	/*
   	 *	获取用户的信息
	 */
   	public function user($ticket){
		$durl = 'http://www.jianong.com/api/user/getUserInfo/ticket/{$ticket}';
		$data = curl_file_get_contents($durl);
		$data = json_decode($data);
		if($data['retcode'] == 2000){
			$user_name = $data['data']['username'];
		}
		return $user_name;
   	}
	
	/*
	 *	判断兽医评价参数
	 */
	public function pram($data){
		if(empty($data['content'])){
			return json(4001,'评价内容不能为空!');
		}
		if(empty($data['score'])){
			return  json(4002,'评价分数不能为空!');
		}
		if(empty($data['con_id'])){
			return json(4003,'咨询id不能为空!');
		}
		if(empty($data['eval_id'])){
			return json(4004,'兽医id不能为空!');
		}
		if(empty($data['token'])){
			json(4000,'用户token不能为空!');
		}
		$data['category'] = 1;
		$res = M('evaluate')->create($data);
		if($res){
			M('evaluate')->create_time = time();
			$result = M('evaluate')->add();
			if($result){
				return json(2000,'评价成功');
			}
			return json(4006,'评价失败');
		}
		return json(4007,'评价失败'); 
	}
	
	
	/*
	 * 获取所有在线的兽医	
	 * 
	 * @return $data
	 */
	 public function getOnline(){
		 $map['status'] = array('eq',1);
		 $map['id'] 	= array('eq',1);
		 $field = 'id,vete_name,real_name,cover,info,level';
		 $data = $this->where($map)->field($field)->order("id desc")->select();
		 //$arr = array();
		 //$huanxin = new \Org\Huanxin();
		 /*foreach($data as $vo){
			 if($huanxin->isOnline($vo['vete_name'])=='online')$arr[] = $vo;
		 }*/
		 
		 return $data;
				 
	 }
	 
	 
	 
	 
	 
}
