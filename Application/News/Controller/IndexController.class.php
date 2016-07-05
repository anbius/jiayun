<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-28
 * Time: 上午11:30
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace News\Controller;


use Think\Controller;

class IndexController extends Controller{

    protected $newsModel;
    protected $newsDetailModel;
    protected $newsCategoryModel;

    function _initialize()
    {
        $this->newsModel = D('News/News');
        $this->newsDetailModel = D('News/NewsDetail');
        $this->newsCategoryModel = D('News/NewsCategory');

        $tree = $this->newsCategoryModel->getTree(0,true,array('status' => 1));
        $this->assign('tree', $tree);    
	}

    public function detail()
    {
        $aId=I('id',0,'intval');        
        $info=$this->newsModel->getData($aId);        
        /* 获取模板 */
        if (!empty($info['detail']['template'])) { //已定制模板
            $tmpl = 'Index/tmpl/'.$info['detail']['template'];
        } else { //使用默认模板
            $tmpl = 'Index/tmpl/detail';
        }
        /* 更新浏览数 */
        $map = array('id' => $aId);
        $this->newsModel->where($map)->setInc('view');
		
		
		//获取评论
		$comment = $this->getComment($aId);
		
		//获取收藏量
		$where['cate'] = 1;
		$where['coll_id'] = $aId;
		$collect = M('collect')->where($where)->count('id');
		
		//获取是否关注
		$is_collect = 0;		
		$token = I('token'); 
		if($token){
			$uid  = getUid($token);
			$this->assign('uid', $uid);
			$this->assign('user', getUserInfo($token));	
			$where['uid']  = $uid;			
			$is_collect = M('collect')->where($where)->field('id')->find()?1:0;				
		}
		
		
		
        /* 模板赋值并渲染模板 */
        $this->assign('author',$author);
        $this->assign('info', $info);
		$this->assign('collect', $collect);
		$this->assign('is_collect', $is_collect);
		$this->assign('comment', $comment);
        $this->display($tmpl);
    } 
	
	  
	public function getComment($id = 0){
		$where['news_id'] = $id?$id:I('id');
		$model 			 = M('comment');
		$field  		 = 'content,user_name,cover,c.create_time';
		$join			 = "__USER__ as u on u.id = c.uid";
		$total           = $model->alias('c')->where($where)->join($join)->count();//获得总数
		
		$listRows        = I("pages")?I("pages"):10;//每页数 
		$page            = new \Think\Page($total, $listRows);
		$limit           = $page->firstRow.','.$page->listRows;
		$data = $model->alias('c')->field($field)->where($where)->join($join)->limit($limit)->order($order)->select();
			
		foreach($data as $k=>$v){
			$data[$k]['cover'] = pic($v['cover']?$v['cover']:23);			
		}	
		$data['data']  = $data;
		$data['total'] = $total;
		return $data;
	}
	
	//上传评论
	public function submitComment(){
		$data['uid'] 		 = I('uid');
		$data['news_id']	 = I('news_id');
		$data['content'] 	 = I('content');
		$data['create_time'] = time();	
		$rs = M('comment')->add($data);
		$arr = array('rs'=>0);
		if($rs){
			$arr['rs'] = 1;	
		}
		echo json_encode($arr);
	}
	
	//是否关注
	public function submitCollect(){
		$data['uid'] 		 = I('uid');
		$data['coll_id']	 = I('coll_id');
		$data['cate'] 	 	 = 1;
		$data['create_time'] = time();	
		$rs = M('collect')->add($data);
		$arr = array('rs'=>0);
		if($rs){
			$arr['rs'] = 1;	
		}
		echo json_encode($arr);	
	}
	
	//是否关注
	public function cancelCollect(){
		$data['uid'] 		 = array('eq',I('uid'));
		$data['coll_id']	 = array('eq',I('coll_id'));
		$data['cate'] 	 	 = array('eq',1);	
		
		$rs = M('collect')->where($data)->delete();
		
		$arr = array('rs'=>0);
		if($rs){
			$arr['rs'] = 1;	
		}
		echo json_encode($arr);	
	}
} 












