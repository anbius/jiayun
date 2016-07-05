<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

/* 收藏管理 */
class CollectController extends ApiController {  
  
	//收藏显示
	public function getList()
    {   $cate    = I('cate');
		$token   = I('token');    //获取token值
		if(empty($token) || empty($cate))json(4000,'数据缺失!');
		$uid = getUid($token);
		if($cate == 4)A('Consultation')->getList();
		$rs      = M('collect')->where('uid ='.$uid.' and cate='.$cate)->order('create_time desc')->select();
		if($rs){
			foreach($rs as $v)$ids[] = $v['coll_id']; 
			switch($cate){
				case 1:
					A('News')->getCollect(M('news'),$ids,'id,title,description,author,create_time,cover');
					break;
				case 2:
					A('News')->getCollect(M('veterinary'),$ids,'id,vete_name,description,level,speciality,create_time,cover');
					break;
				case 3:
					A('News')->getCollect(M('srtc'),$ids,'id,srtc_name,cate,description,addr,create_time,cover ');
					break;
				
			}			
		 }else{		 
		    json('4001','没有查询结果');	
		}		
   }
   
   
   
	//收藏添加
	public function add(){
		$token          = I('token');    //获取token值
		$uid		    = getUid($token);
		$data['uid']    = $uid;
 		$data['cate']   = I('cate');
		$data['coll_id']= I('coll_id');
		if(empty($token) || empty($data['cate'])|| empty($data['coll_id']))json(4000,'数据缺失!');	
		$map['uid']     = $data['uid'];
		$map['cate']    = $data['cate'];
		$map['coll_id'] = $data['coll_id'];
		$res            = M('collect')->where($map)->find();
		if($res){
		   json('4001','已收藏');
		}else{
			$data['create_time'] = time();
			$rs = M('collect')->add($data);
			if($rs){
				 json('2000','收藏成功');
			}else{
				 json('4002','收藏失败');
			}
		}
	}
    //删除收藏
	public function delete(){
		$token         = I('token');    //获取token值
		//$where['cate']   = I('cate');
		$where['coll_id']= I('id');
		$uid = getUid($token);
		$where['cate'] = I('cate');
		$where['uid']  = I('uid');
		if(empty($token) || empty($where['coll_id']) || empty($where['cate']))json(4000,'数据缺失!');
		$where['uid']    = $uid;
		$res = M('collect')->where($where)->delete();
		
		//echo M('collect')->_sql();
		if($res){
		 	json('2000','取消收藏成功');	
		}else{
			json('4000','取消收藏失败');
		}
		
	}   	
	
}