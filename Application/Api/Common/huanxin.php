<?php
const URL = "https://a1.easemob.com/jianongzx/jiayunjiankang/";
/**
 * 批量删除用户
 * 描述：删除某个app下指定数量的环信账号。上述url可一次删除300个用户,数值可以修改 建议这个数值在100-500之间，不要过大
 *
 * @param $limit="300" 默认为300条          
 * @param $ql 删除条件
 *          如ql=order+by+created+desc 按照创建时间来排序(降序)
 */
function batchDeleteUser($limit = "300", $ql = '') {	
	$url = URL."users?limit=" . $limit;
	if (! empty ( $ql )) {
		$url = URL."users?ql=" . $ql . "&limit=" . $limit;
	}
	$header = array(_get_token());
	$result = _curl_request( $url, '', $header, $type = 'DELETE' );
	return $result;
}

/**
 * 用户加好友
 * @param  [string] $username [用户名称]
 * @param  [string] $vetname [兽医名称]
 *
 */
function becomeFriends($username,$vetname) {	
	$formgettoken = URL."users/" . $username.'/contacts/users/'.$vetname;
	dump($formgettoken);
	$header = array(_get_token());
	dump($header);
	$result = _curl_request( $formgettoken, '', $header, $type = 'PUT');
	return $result;
}


/**
 * 更改用户昵称
 * @param  [string] $username [用户名称]
 * @param  [string] $nickname [用户昵称]
 *
 */
function editNick($username,$nickname) {	
	$formgettoken = URL."users/" . $username;
	$body=array(
		"username"=>$username,
		"nickname"=>$nickname,
	);
	$patoken=json_encode($body);
	$header = array(_get_token());
	$result = _curl_request( $formgettoken, $patoken, $header, $type = 'PUT');
	return $result;
}
/**
 * 授权注册模式 || 批量注册
 *
 * @param $options['username'] 用户名          
 * @param $options['password'] 密码
 *          批量注册传二维数组
 */
function accreditRegister($options) {
	
	$formgettoken = URL."users";
	$header = array(_get_token());
	$result = _curl_request ( $formgettoken, json_encode($options), $header );
	return $result;
}
 
 
//授权注册模式 POST /{org_name}/{app_name}/users
function registerToken($username,$pwd,$nickname='')
{
    
    $formgettoken=URL."users";
    $body=array(
        "username"=>$username,
        "password"=>$pwd,
        'nickname'=>$nickname
    );
    $patoken=json_encode($body);
    $header = array(_get_token());
    $res = _curl_request($formgettoken,$patoken,$header);
 
    $arrayResult =  json_decode($res, true);    
    return $arrayResult ;
}
//重置用户密码 PUT /{org_name}/{app_name}/users/{username}/password
function changePwdToken($nikename,$newpwd)
{
    
    $formgettoken=URL."users/".$nikename."/password";
    $body=array(
        "newpassword"=>$newpwd,
    );
    $patoken = json_encode($body);
    $header = array(_get_token());
	
    $method = "PUT";
    $res = _curl_request($formgettoken,$patoken,$header,$method);	
    $arrayResult =  json_decode($res, true);    
    return $arrayResult ;
}
//删除 DELETE /{org_name}/{app_name}/users/{username}
function delUserToken($nikename)
{
    
    $formgettoken=URL."users/".$nikename;
    $body=array();
    $patoken=json_encode($body);
    $header = array(_get_token());
    $method = "DELETE";
    $res = _curl_request($formgettoken,$patoken,$header,$method);
    $arrayResult =  json_decode($res, true);    
    return $arrayResult ;
}
//先获取app管理员token POST /{org_name}/{app_name}/token
function _get_token()
{    
    $formgettoken=URL."token";
    $body=array(
    "grant_type"=>"client_credentials",
    "client_id"=>"YXA6J7AzcBQQEea53ZPdhD5ung",
    "client_secret"=>"YXA64LGFm_iJj-Nh_Mz_TO7859mg3IA"
    );
    $patoken=json_encode($body);
    $res = _curl_request($formgettoken,$patoken);
    $tokenResult = array();
   
    $tokenResult =  json_decode($res, true);   
    return "Authorization: Bearer ". S('huanxin_token');  
}
 
function _curl_request($url, $body, $header = array(), $method = "POST")
{
    array_push($header, 'Accept:application/json');
    array_push($header, 'Content-Type:application/json');
 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, $method, 1);
     
    switch ($method){ 
        case "GET" : 
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        break; 

        case "POST": 
            curl_setopt($ch, CURLOPT_POST,true); 
        break; 
        case "PUT" : 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
        break; 
        case "DELETE":
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); 
        break; 
    }
     
    curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    if (isset($body{3}) > 0) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    if (count($header) > 0) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
 
    $ret = curl_exec($ch);
    $err = curl_error($ch);
    $err = curl_getinfo($ch,CURLINFO_HTTP_CODE); //设置返回状态码
    curl_close($ch);
	
	//dump($header);
    //clear_object($ch);
    //clear_object($body);
    //clear_object($header); 
	if($ret){
		$tokenResult =  json_decode($ret, true);
		$token = $tokenResult["access_token"];
		if(!is_null($token))S('huanxin_token',$token);
	}
		
    if ($err) {
        return $err;
    }
 
    return $ret;
}