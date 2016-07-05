<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");
/* 热点资讯 */
class NewsController extends ApiController {  
    private function index(){}	
	
	//获取蛋鸡头条
	public function getBanner(){		
		if(!S('bannerData')){
			$map['cms_classid'] = array('eq',80);
			$banner = D('contents')->field('cms_id,cms_title,cms_pic')->where($map)->select();
			$data = '{"retcode":2000,"data":'.json_encode($banner).'}';
			S('bannerData',$data,3600);//每一个小时更新一次数据			
		}
		echo S('bannerData');	
	}
	
	//获取热点资讯五条
	public function getRedlineTop(){			
		$data = '';//数据
		//S('RedlineTopData',NULL);
		if(!S('RedlineTopData')){
			$map['cms_classid'] = array('eq',2);
			$field = 'cms_id,cms_title,cms_remark,cms_pic,cms_readTimes,cms_addDate';
			$news = D('contents')->field($field)->where($map)->limit(5)->order('cms_addDate desc')->select();
			$data = '{"retcode":2000,"data":'.json_encode($news).'}';			
			S('RedlineTopData',$data,3600);//每一个小时更新一次数据	
		}	
		echo S('RedlineTopData');		
	}
	
	//分页获取热点资讯
	public function getRedline(){
		$classid = I('classid')?I('classid'):2;//默认获取资讯热点		
		$model = D('contents');//模型
		$where['cms_classid'] = array('eq',$classid);//条件
		$order = 'cms_addDate desc';//排序
		$field = 'cms_id,cms_title,cms_remark,cms_readTimes,cms_addDate';
		$news = $this->lists($model,$where,$order,$field);
		\Org\Response::show(2000,'获取成功!', $news?$news:'');
	}
	
	//获取资讯详情
	public function getRedlineInfo(){
		$cms_id = I('id');
		if(empty($cms_id))\Org\Response::show(4000,'缺少必要的参数!');
		$map['cms_id'] = array('eq',$cms_id); 
		$field = 'cms_title,cms_content,cms_readtimes,cms_adddate';
		$info  = D('contents')->cache(true,60,'xcache')->field($field)->where($map)->find();
		
		$notedate = object2array($info['cms_adddate']);
		$time = strtotime($notedate['date']);
		$info['cms_adddate'] = date('Y-m-d H:i',$time);
		//更新阅读数据
		$sql   = "update [contents] set [cms_readtimes] = [cms_readtimes] + 1 where [cms_id] = '{$cms_id}'";		
		$rs = D('contents')->execute($sql);
		
		$info['cms_content'] = clearStyle($info['cms_content']);//清除style属性
		
		if(empty($info))\Org\Response::show(4001,'该文章不存在!');
		\Org\Response::show(2000,'获取成功!',$info);
	}	
	
	
}







