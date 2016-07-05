<?php
namespace Api\Model;
use Think\Model;
/**
 * 收藏模型
 */
class CollectModel extends Model{
   
	/*
	 * 关注收藏	
	 * @param int $data 
	 * @output json
	 */
	 public function setCollect($data){
		$map['uid'] 		 = array('eq',$data['uid']);
		$map['cate'] 		 = array('eq',$data['cate']);
		$map['coll_id'] 	 = array('eq',$data['coll_id']);
		
		$collect = M("collect")->where($map)->find();
		if($collect){
			$data['create_time'] = time();
			$rs = M("collect")->add($data);
		}else{
			$rs = M("collect")->add($data);
		}
	 }
}
