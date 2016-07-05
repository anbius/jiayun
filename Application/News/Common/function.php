<?php

function getUid($token){
	if(empty($token))return '';
	$map['token'] = array('eq',$token);
	$uid = M('user')->where($map)->getField('id');	
	return $uid;
}

function getUserInfo($token,$field="user_name,cover"){
	if(empty($token))return false;
	$user = M('user')->where("token = '{$token}'")->field($field)->find();
	$user['cover'] = HTTP.pic($user['cover']);
	return $user;
}


