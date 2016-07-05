<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

/* 检测中心管理 */
class TestingController extends ApiController {  

    /*
	 * 获取检测项目
	 * @output json
	 */
	public function getTestitem(){		
		$map['status']   = array('eq',1);				
		$testitem = M('testitem')->where($map)->field('id,title')->select();
		
        json(2000,'获取成功!',$testitem);
		
	}
    /*
    * 获取样品种类
    * @output json
    */
    public function getSampletype(){
        $map['status']   = array('eq',1);				
		$sampletype = M('sampletype')->where($map)->field('id,title')->select();
        json(2000,'获取成功!',$sampletype);

    }
	/*
    * 获取检测目的
    * @output json
    */
    public function getDetection(){
        $map['status']   = array('eq',1);				
		$detection = M('detection')->where($map)->field('id,title')->select();
        json(2000,'获取成功!',$detection);
    }
	/*
	 *	添加图片
	 */
	public function setImages(){		
		$id = $this->uploadImage();		
		if($id){
		 	json('2000','',array('id'=>$id));
		}else{
			json('4001','上传失败');
		}
	}	
	/*
	 *	提交表单
	 */
	public function setTesting(){
		$map['token'] = I('token');
		$user = M('user')->where($map)->field('id')->find();
		$data['uid'] = $user['id'];
		$data = $_POST;
		$res = M('testing')->create($data);
		if($res){
			$result = M('testing')->add();
			if($result){
				json('2000','提交成功');
			}else{
				json('4000','提交失败');
			}
		}else{
			json('4001','提交失败');
		}
	}
	/*
	 *	获取检测表单
	 */
	public function getTesting($token){
		$map['token'] = $token;
		if($token=='')json('4000','缺少参数');
		$user = M('user')->where($map)->field('id')->find();
		$data['uid'] = $user['id'];
		$list = M('testing')->where("status = 1")->order("update_time desc") -> select();
		foreach($list as $key=>$value){
			$check_picture = $value['check_picture'];
			$pic_id = explode(',',$check_picture);
			foreach($pic_id as $k=>$val){
				$list[$key]['path'][$k] = HTTP.pic($val);
			}
			$detection = $value['detection'];
			$detection_id = explode(',',$detection);
			foreach($detection_id as $kk=>$vall){
				$title = M("detection")->where("id=".$vall)->field("title,id")->find();
				$list[$key]['detection'][$kk] = $title['title'];
			}
		}
		json('2000','获取成功',$list);
	}
}