<?php
namespace Api\Controller;
use Think\Controller;
header("Access-Control-Allow-Origin:*");

/* 检测中心管理 */
class SrtcController extends ApiController {  

    /*
	 * 获取检测中心列表
	 * @output json
	 */
	public function getList(){		
		$map['status']   = array('eq',1);				
		$list = $this->lists(D('srtc'),$map,'sort asc,id desc','id,srtc_name,cover,description,cate');
        
		if($list){
			foreach($list as $k=>$v){
				if($v['cover']){
					$list[$k]['cover'] = HTTP.pic($v['cover']);
				}else{
					$list[$k]['cover'] = '';
				}
			}
		}
		if(is_null($list))$list = array();
        json(2000,'获取成功!',$list);
		
	}
    /*
    * 获取检测中心详情
    * @output json
    */
    public  function getDetail(){
        $map['id'] = I('id');
		
		
        if(empty($map['id']))json(4000,'缺少检测中心id');
        $map['status']   = array('eq',1);
		$field = 'id,srtc_name,cover,mobile,description,cate,bus_line,addr,info,test_program';
		$data = M('srtc')->where($map)->field($field)->find($id);
		
		
        //获取图片
		$data['cover'] = HTTP.pic($data['cover']);
		//获取用户是否关注
		$token = I('token');
		if($token){
			//$map = array();
			//$map['cate'] 	= array('eq',3);
			//$map['coll_id'] = array('eq',$id);
			$map['uid'] 	= getUid($token);
			$datas = M('collect')->where('cate =3 and coll_id = '.$map['id'].' and uid='.$map['uid'])->find();
			//$datas = M('collect')->where('cate =3 and coll_id = '.$id.' and uid='.$map['uid'])->find();
			if($datas){
				$data['is_collect'] = 1;
			}else{
				
				$data['is_collect'] = 0;
			}
		  
		  
		}else{
			$data['is_collect'] = 0;
		}
        if(is_null($data))$data = array();
        json(2000,'获取成功!',$data);

    }




}