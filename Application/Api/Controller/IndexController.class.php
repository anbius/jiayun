<?php
namespace Api\Controller;
use Think\Controller;


class IndexController extends Controller {
	public function index(){
		
	}	
	
	
	//获取动物种类的
	public function animal(){
		//$pid = I('pid')?I('pid'):1;			
		$data = M('animal')->field('id,title,pid')->order('sort,id asc')->select();
		if($data)$data = get_tree($data);			
		if($data){
			json('2000','获取成功',$data);	
			
		}else{
			json('2000','没有值',$data);	
		}
	}	
	
	/*反馈意见*/
	public function feedback(){		
		$data['contact'] = I('contact'); 
		$data['content'] = I('content'); 
		$token = I("token");
		if($token)$data['uid'] = getUid($token);
		if(empty($data['contact']))json(4001,'标题不能为空!');
		if(empty($data['content']))json(4002,'内容不能为空!');
		
		//图片上传
		
		
		$data['creat_time'] = time();
		$rs = D('feedback')->add($data);
		if($rs){
			json(2000,'提交成功!');	
		}else{
			json(4000,'提交失败!');		
		}
	}
	
	
	
	
	//获取嘉云检测数据
	public function submitTesting(){		
		$Vetins = M('vetins');
		$Vetins->create($_POST);
		$Vetins->add();
		json(2000,'提交成功!');
	}	
	
	
	//获取版本号
	public function getVersion(){
		$map['device'] = I('device')?I('device'):'android';
		$sign   = I('sign')?I('sign'):'jianong';
		$field = "versionName,versionCode,download,content,size,length";
		$data = M("version")->where($map)->field($field)->order('versionCode desc')->find();		
		json(2000,'',$data);	
	}
	
}