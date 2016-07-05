<?php
namespace Api\Controller;
use Think\Controller;

class MessageController extends ApiController {
    protected $model;

    protected function _initialize() {
        $this->model = D('Message');
    }

    //获取后台系统发送给用户的信息
    public function sendMessage(){
        $map['status'] = array('eq',1);
        $map['uid'] = array('gt',0);
        $map['type'] = 0;
        $list = $this ->lists($this->model,$map,'','content,uid,is_read,type,create_time');
      /*  foreach($list as &$value){
            $value['user_name']=M('user')->where('id='.$value['uid'])->getField('user_name');
            $content= $value['content'];
        }*/
      //  $list    = jpush_user($content,$type,$receive,$txt);
        $data['content'] = $list[0]['content'];
        $time = $list[0]['create_time'];
        $data['create_time'] = date('Y-m-d',$time);
        if($data){
         json(2000,'', $data);
        }else{
            json(4000,'没有数据');
        }
    }

    //获取给医生发送的系统信息
    public function sendDoc(){
        $map['status'] = array('eq',1);
        $map['vuid'] = array('gt',0);
        $list = $this ->lists($this->model,$map,'','content,vuid,is_read,type,create_time');
       /* foreach($list as &$value){
            $value['vuid']=M('veterinary')->where('id='.$value['vuid'])->getField('vete_name');
            $content= $value['content'];
        }*/
    //    $list    = jpush_doc($content,$type,$receive,$txt);
        $data['content'] = $list[0]['content'];
        $time = $list[0]['create_time'];
        $data['create_time'] = date('Y-m-d',$time);
        if($list){
            json(2000,'',  $data);
        } else{
            json(4000,'没有数据');
        }

    }
}