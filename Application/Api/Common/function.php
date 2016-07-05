<?php
require_once(APP_PATH . '/Api/Common/huanxin.php');
/**
* 按json方式输出通信数据
* @param integer $code 状态码
* @param string $message 提示信息
* @param array $data 数据
* return string
*/
function json($code,$message = '', $data = array() ){	
	$result = array(
		'retcode' => (string)$code,	
		'msg' => urlencode($message),	
		'data' => $data		
	);
	echo urldecode(json_encode($result));
	exit; 
}
//base64转图片
function base64ToImg( $base64_string, $output_file ) {
   $ifp = fopen( $output_file, "wb" ); 
   fwrite( $ifp, base64_decode( $base64_string) ); 
   fclose( $ifp ); 
   return( $output_file ); 
}

//获取图片
function getPic($id){
	if(empty($id))$id = 23;
	return HTTP.pic($id);
}

function getUid($token){
	if(empty($token))return '';
	$map['token'] = array('eq',$token);
	$uid = M('user')->where($map)->getField('id');	
	return $uid;
}

//上传图片
function uploadImg($base64_string){
	$savename = uniqid().'.jpeg';		 
	$savepath = 'Uploads/Picture/'.date('Y-m-d'); 
	$filename = $savepath.'/'.$savename;
	if(!is_dir($savepath))mkdir($savepath, 0777, true);//目录不存在则创建			 
	//$base64_string = explode(',',$base64_string);			 
	$image = base64ToImg( $base64_string, $filename );	
	return $filename;
}



/*
* 获取用户信息包括名称头像等
* @param int $uid 用户id
* @return $data
*/
function getUserInfo($uid,$field="user_name,cover"){
	if(empty($uid))return false;
	$user = M('user')->field($field)->find($uid);
	$user['cover'] = HTTP.pic($user['cover']);
	return $user;
}

function getUserInfos($token,$field="user_name,cover"){
	if(empty($token))return false;
	$user = M('user')->where("token = '{$token}'")->field($field)->find();
	$user['cover'] = HTTP.pic($user['cover']);
	return $user;
}

/*
* 处理新闻(处理评论量、关注量、以及多张图片)
* @param int $data 新闻类型的数据
* @param int $page 页码,默认为1
* @return $data
*/
function haddleNews($data){
	$id = $data['id'];
	//获取评论量
	$data['comments'] = M('comment')->where("news_id = $id")->count('id');
	//获取关注量
	$data['collects'] = M('collect')->where("cate = 1 and coll_id = $id")->count('id');
	//时间日期
	$data['create_time'] = date("Y-m-d H:i",$data['create_time']);
	//获取图片链接
	$pics = M('picture')->field('path')->where("id in (".$data['cover'].")")->select();
	$data['cover'] = '';
	if($pics){
		$arr = array();		
		foreach($pics as $v){
			$arr[] = HTTP.$v['path'];	
		}	
		$data['cover'] = implode(',',$arr);
	}
	return $data;
}

/*
* 获取动物种类名称
* @param int $id 
* @output json
*/
function getAnimalField($id,$field='title'){
	if(empty($id))return '';
	$title = M('animal')->where('id = '.$id)->getField($field);
	return $title;
}

/*
* 根据动物ID获取整体名称
* @param int $id 
* @output json
*/
function getAnimalTitle($id){
	if(empty($id))return '';
	$animal = M('animal')->where('pid,title')->find($id);
	$title = getAnimalField($animal['pid']).','.$animal['title'];
	return $title;
}

/*
* 处理兽医信息
* @param int $data 兽医信息类型的数据
* @return json
*/
function haddleVeterinary($data){
	if($data['cover']){		
		$data['cover'] = HTTP.pic($data['cover']);	
	}	
	return $data;
}


/*
* 处理兽医评价信息
* @param int $data 兽医评价信息类型的数据
* @param int $page 页码,默认为1
* @output json
*/
function haddleComment($data){
	$id = $data['id'];
	
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
}

/*
* 获取树状结构的数据
* @param int $tree 树状结构数据
* @param int $rootId 父id
* @output json 
*/
function get_tree($tree, $rootId = 0) {  
    $return = array();  
    foreach($tree as $leaf) {  
        if($leaf['pid'] == $rootId) {  
            foreach($tree as $subleaf) {  
                if($subleaf['pid'] == $leaf['id']) {  
                    $leaf['children'] = get_tree($tree, $leaf['id']);  
                    break;  
                }  
            }  
            $return[] = $leaf;  
        }  
    }  
    return $return;  
}   


//生成错误日志
function logDebug($content){
    //记录delog---------------------------------
    $fileName = "error.log";
    $bottom   = "\r\n".date("Y-m-d H:i:s",time())."\r\n===========================================================================================\r\n";    
	$file_pointer = fopen($fileName,"a");
	fwrite($file_pointer,$content.$bottom);
	fclose($file_pointer);
}
