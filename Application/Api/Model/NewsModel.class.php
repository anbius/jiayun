<?php
namespace Api\Model;
use Think\Model;
/**
 * 资讯模型
 */
class NewsModel extends Model{
   
   /*
   * 获取一条资讯
   * @param $cate(1：资讯2：兽医，3：检测中心)
   * @output json
   */
   public function getOne($map='',$field=''){
	   $news = $this->field($field)->where($map)->limit(2)->select();
       $data = array();
       $data2 = array();
       if($news){
          foreach($news as $val){
              $data = haddleNews($val,$cate);
              array_push($data2,$data);
          }
       }
		return $data2;
   }
   
   
/*   private function haddleNews($data){
	    $id = $data['id'];
		//获取评论量
		$data['comments'] = M('comment')->where("news_id = $id")->count('id');
		//获取关注量
		$data['collects'] = M('collect')->where("cate = 1 and coll_id = $id")->count('id');
		//时间日期
		$data['create_time'] = date("Y-m-d",$data['create_time']);
		//获取图片链接
		$pics = M('picture')->field('path')->where("id in (".$data['cover'].")")->select();
		$data['cover'] = '';
		if($pics){
			$arr = array();
			$http = C('WEB_URL');
			foreach($pics as $v){
				$arr[] = $http.$v['path'];	
			}	
			$data['cover'] = implode(',',$arr);
		}
		return $data;
   }*/
   
   /*
   * 获取多条资讯
   * @param $cate(1：资讯2：兽医，3：检测中心)
   * @output json
   */
   public function getList($cate=2,$map='',$field=''){
	    $news = $this->field($field)->where($map)->select();
		foreach($news as $k=>$v){
			$news[$k] = haddleNews($news[$k]);	
		}
		return $news;
   }
   
   

}
