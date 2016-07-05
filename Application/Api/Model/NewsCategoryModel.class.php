<?php
namespace Api\Model;
use Think\Model;
/**
 * 资讯分类模型
 */
class NewsCategoryModel extends Model{ 
    
   /*
   * 获取子类a
   * @param $pid
   * @output string
   */
	public function getSonStr($pid = 1){
		$cate_arr = $this->field('id')->where("pid = $pid")->select();
		$cate_ids = $pid;
		if($cate_arr){
			foreach($cate_arr as $v){
				$cate_ids .= ','.$v['id'];
			}
		}
		return $cate_ids;	
	}
	
	/*
   * 获取子类
   * @param $pid 文章分类：1：案例分析 2：新闻资讯	 
   * @output string
   */
	public function getSon($pid = 1){
		$cate_arr = $this->field('id,title')->where("pid = $pid")->select();
		return $cate_arr;	
	}
	
	
	
}
