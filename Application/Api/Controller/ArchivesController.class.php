<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

/* 档案管理 */
class ArchivesController extends ApiController {  
  
   //获取档案
	public function getOne()
    {   $id      = I('id');
		$token   = I('token');    //获取token值
		if(empty($token))json(4000,'数据缺失!');
		$uid = getUid($token);  //获取uid
		$rs      = M('archives')->where('uid ='.$uid .' and id='.$id)->order('create_time desc')->find(); 
		if($rs){
			$rs['animal'] =  getAnimalTitle($rs['animal_id']);		
			json('2000','查询成功',$rs);		
		}else{
		    json('4001','没有查询结果');
		}		
   }
   
	//获取档案
	public function getList()
    {   
		$token   = I('token');    //获取token值
		if(empty($token))json(4000,'数据缺失!');
		$uid = getUid($token);  //获取uid
		$rs      = M('archives')->field("id,animal_id,create_time")->where('uid ='.$uid)->order('create_time desc')->select();
		if($rs){
			$arr = array();
			foreach($rs as $k=>$v){
				$arr[$k]['id']	   = $v['id'];
				$arr[$k]['animal'] = getAnimalTitle($v['animal_id']);
				$arr[$k]['time']   = date("Y-m-d H:i:s",$v['create_time']);
			}	
			json('2000','查询成功',$arr);		
		}else{
		    json('4001','没有查询结果');	
		}		
   }
	//添加档案
	public function add(){
		$token         = I('token');    //获取token值
		$data['uid'] = getUid($token);  //获取uid
		if(empty($token))json(4000,'数据缺失!');
 		$data['animal_id']   = I('animal_id'); //动物种类
		$data['day_old']	 = I('coll_id');  //日龄
		$data['size']		 = I('size');        //规模
		$data['im_program']	 = I('im_program'); //免疫程序
		$data['dis_history'] = I('dis_history'); //发病史
		$data['med_record']  = I('med_record'); //用药记录 
		$data['create_time'] = time();
	    $rs = M('archives')->add($data);
		if($rs){
			json('2000','添加成功',array("id"=>$rs));
		}else{
			json('4002','添加失败');
		}		
	}
    //删除档案
	public function delete(){
		$token          = I('token');    //获取token值
		if(empty($token))json(4000,'数据缺失!');
		$id    = I('id');
		if(empty($id))json(4001,'数据缺失!');
		$where['uid'] 	= getUid($token);
		if(empty($token) || empty($where['uid']))json(4000,'数据缺失!');
		
		$where['id']    = array('in',$id);
		
		$res = M('archives')->where($where)->delete();
		//echo M('archives')->_sql();
		if($res){
		 	json('2000','删除成功');	
		}else{
			json('4003','删除失败');
		}
	}   
	
	  //删除档案
	public function delete1(){
		$token          = I('token');    //获取token值
		if(empty($token))json(4000,'数据缺失!');
		$id    = I('id');
		if(empty($id))json(4001,'数据缺失!');
		$where['uid'] 	= getUid($token);
		if(empty($token) || empty($where['uid']))json(4000,'数据缺失!');
		
		$where['id']    = array('in',$id);
		
		$res = M('archives')->where($where)->delete();
		echo M('archives')->_sql();
		if($res){
		 	json('2000','删除成功');	
		}else{
			json('4003','删除失败');
		}
	} 
}