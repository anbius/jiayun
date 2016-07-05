<?php
namespace Api\Controller;
use Think\Controller;

class NewsController extends ApiController {  
    private function index(){}	
	
	/*
	 * 获取首页资讯
	 * @output json
	 */
	public function getIndexNews(){
		if(!S('IndexNewsData')){
			$map['status'] = array('eq',1);
			$banner = M('banner')->field('id,title,cover,url')->where($map)->select();
			$url = C('WEB_URL');
			if($banner){
				foreach($banner as $k=>$v){
					$banner[$k]['cover'] = $url.pic($v['cover']);
					if(empty($v['url']))$banner[$k]['url'] = $url.U("news/index/banner",array('id'=>$v['id']));
				}				
			}
			$data['Banner'] = $banner?$banner:array();
			
			//获取案例的类别ID
			$cate_ids = D('NewsCategory')->getSonStr();
			$map['categroy'] = array('in',$cate_ids);			
			$data['IndexCase'] = D('news')->getOne($map,'id,title,description,author,create_time,cover');
			
			$data = '{"retcode":2000,"data":'.json_encode($data).'}';
			S('IndexNewsData',$data,3600);//每一个小时更新一次数据	缓存的数据
		}
		echo S('IndexNewsData');
	}
	
	/* 
	 * 获取首页banner
	 * @output json
	 */
	public function getBanner(){
		if(!S('bannerData')){
			$map['status'] = array('eq',1);
			$banner = M('banner')->field('id,title,cover,url')->where($map)->select();
			$url = C('WEB_URL');
			if($banner){
				foreach($banner as $k=>$v){
					$banner[$k]['cover'] = $url.pic($v['cover']);
					if(empty($v['url']))$banner[$k]['url'] = $url.U("news/index/banner",array('id'=>$v['id']));
				}
				
			}	
			$data = '{"retcode":2000,"data":'.json_encode($banner).'}';
			S('bannerData',$data,3600);//每一个小时更新一次数据			
		}
		echo S('bannerData');
	}
	
	/*
	 * 获取首页案例
	 * @output json
	 */
	public function getIndexCase(){
		if(!S('IndexCase')){
			//获取案例的类别ID
			$cate_ids = D('NewsCategory')->getSonStr();
			$map['status']   = array('eq',1);
			$map['categroy'] = array('in',$cate_ids);			
			$data = D('news')->getOne($map,'id,title,description,author,create_time,cover');
			$data = '{"retcode":2000,"data":'.json_encode($data).'}';
			S('IndexCase',$data,3600);//每一个小时更新一次数据			
		}
		echo S('IndexCase');
	}	
	
	/*
	 * 获取资讯分类	
	 * @param int $category 文章分类：1：案例分析 2：新闻资讯	 
	 * @output json
	 */
	public function getCategory(){
		$category = I('cate');
		if(empty($category))json(4000,'不存在分类ID');		
		$tag = 'NewsCategory_'.$category;
		if(!S($tag)){
			$data = D('NewsCategory')->getSon($category);			
			$data = '{"retcode":2000,"data":'.json_encode($data).'}';
			S($tag,$data);			
		}
		echo S($tag);
	}
	
	
	/*
	 * 获取资讯列表	
	 * @param int $category 分类ID
	 * @param int $page 页码,默认为1
	 * @output json
	 */
	public function getList(){
		$category = I('category');
		if(!empty($id))$map['id'] = $id;
		if(empty($category))json(4000,'不存在分类ID');		
		$map['status']   = array('eq',1);
		$map['category'] = array('eq',$category);					
		$list = $this->lists(M('news'),$map,'sort asc,id desc','id,title,description,author,create_time,cover');
	
		if($list){
			foreach($list as $k=>$v){
				$list[$k] = haddleNews($v);	
			}	
		}
		if(is_null($list))$list = array();
		echo json(2000,'',$list);
	}
	/*
	 * 获取收藏资讯列表	
	 * @param int $category 分类ID
	 * @param int $page 页码,默认为1
	 * @output json
	 */
	 public function getCollect($model,$id,$fileds){
		if(!empty($id))$map['id'] = array('in',$id);		
		$map['status']   = array('eq',1);
		$list = $this->lists($model,$map,'sort asc,id desc',$fileds);
	
		if($list){
			foreach($list as $k=>$v){
				$list[$k] = haddleNews($v);	
			}	
		}
		if(is_null($list))$list = array();
		echo json(2000,'',$list);
	}
}







