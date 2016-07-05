<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

/* 意见反馈 */
class FeedbackController extends ApiController {  
  
   //意见反馈
	 public function uploadImg(){
		$base64_string = I('base64_string');
		$id = A('Api')->uploadImage($base64_string);
		$datas['id'] = $id;
		if($id){
		    json('2000','上传成功',$datas);
		}else{
			json('4001','上传失败');
		}
	}

	//添加反馈
	public function add(){
		$token         = I('token');    //获取token值
		$data['uid'] = getUid($token);  //获取uid
		$data['content']     = I('content'); //内容
		$data['pictures']	 = I('pictures'); //图片
		$data['contact']	 = I('contact');  //联系方式
		if(empty($data['content']) || empty($data['contact']))json(4000,'参数缺失!');	
		$data['create_time'] = time();
	    $rs = M('feedback')->add($data);
		if($rs){
			json('2000','添加成功',array("id"=>$rs));
		}else{
			json('4001','添加失败');
		}		
	}
    
}