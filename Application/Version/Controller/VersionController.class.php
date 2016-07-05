<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;
use Common\Model\ContentHandlerModel;
class VersionController extends AdminController
{
	protected $versionModel;

    function _initialize()
    {
        parent::_initialize();
        $this->versionModel = D('Version/Version');
    }
    public function index($page = 1, $r = 10)
    {
        $builder = new AdminListBuilder();
		$map['status'] = 1;
		$data = $this->versionModel -> where($map) ->select();
		$totalCount = count($data);
		$list = $this->versionModel -> where($map) -> order('id desc') -> limit(($page - 1) * $r, $r) ->select();
		$builder -> title('版本控制') 
				 -> data($list) 
				 -> setSelectPostUrl(U('Admin/Version/index')) 
				 -> setStatusUrl(U('Version/setVersionStatus')) 
				 -> buttonNew(U('Version/edit'))
				 -> buttonDelete() -> keyId() 
				 -> keyText('device','平台名称')
				 -> keyText('versionName', '版本名称') 
				 -> keyText('versionCode', '版本号') 
				 -> keyText('content', '版本更新内容') 
				 -> keyText('download', '上传文件') 
				 -> keyText('size', '大小') 
				 -> keyText('length', '长度') 
				 -> keyText('create_time', '添加时间')
				 -> keyText('update_time','修改日期')
				 -> keyDoActionEdit('edit?id=###') 
				 -> pagination($totalCount, $r)
				 -> display();
    }
	public function setVersionStatus($ids, $status) {
		$builder = new AdminListBuilder();
		$builder -> doSetStatus('version', $ids, $status);
	}
	public function edit()
    {
        $aId=I('id',0,'intval');
        $title=$aId?"编辑":"新增";
        if(IS_POST){
            $aId&&$data['id']=$aId;
			$data['device'] = I('post.device');
            $data['versionName']=I('post.versionName');
            $data['versionCode']=I('post.versionCode');
            $data['content']=I('post.content');
            $data['download']=I('post.download');
            $data['size']=I('post.size');
            $data['length']=I('post.length');
            $data['create_time']=I('post.create_time');
			$time = time();
			$data['update_time']  = date('Y-m-d',$time);
            $result=$this->versionModel->editData($data);
            if($result){
                $aId=$aId?$aId:$result;
                $this->success($title.'成功！',U('Version/index'));
            }else{
                $this->error($title.'失败！',$this->versionModel->getError());
            }
        }else{
            if($aId){
                $data = $this -> versionModel -> find($aId);
			}
			$option = array("android"=>"android","ios"=>"ios");
            $builder=new AdminConfigBuilder();
            $builder->title($title.'版本')
                ->data($data)
                ->keyId()
                ->keyText('versionName','版本名称')
                ->keyText('versionCode','版本号')
                ->keyTextArea('content','版本更新内容')
                ->keySingleFile('download','文件上传')
				->keySelect('device','平台名称','',$option)
                ->keyText('size','文件大小(字节)')
                ->keyText('length','文件大小(M)')
                ->buttonSubmit()->buttonBack()
                ->display();
        }
    }
	private function _checkOk($data = array()) {
		return true;
	}

}
