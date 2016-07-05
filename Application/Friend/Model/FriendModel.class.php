<?php
namespace Friend\Model;
use Think\Model;

/**
 * 会员模型文档
 */
class FriendModel extends Model{    
    protected $_auto = array (         
       
    );
    
    protected $_validate = array(       
        
    );
	
	protected $model;	
	public function _initialize(){
		$this->model = 	M('friends','dj_','mysql://root:root@localhost/danjiguanjia'); 
	}
	
	//获取单个昵称	
	public function getOne($uid,$fname){
		$map['uid']   = array('eq',$uid);
		if($fname)$map['fname'] = array('in',$fname);
		$data = $this->model->field('fname,notename')->where($map)->select();				
		return $data;
	}
	
	//获取所有备注
	public function getAll($uid){
		$map['uid']   = array('eq',$uid);
		$data = $this->model->field('fname,notename')->where($map)->select();				
		return $data;	
	}
	
	
	//设置昵称
	public function setOne($uid,$fname,$notename){
		$data['uid']      = $uid;
		$data['fname']    = $fname;
		$data['notename'] = $notename;
		
		$map['uid'] = array('eq',$uid);
		$map['fname'] = array('eq',$fname);
		$friend = $this->model->field('id')->where($map)->find();	
		//是否存在该好友
		if($friend){//修改
			$data['id']   = $friend['id'];
			$rs = $this->model->save($data);	
		}else{//新增
			$rs = $this->model->add($data);	
		}			
		return $rs;
	}
	
	
	
	
}
