<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class MessController extends AdminController {
    protected $MessageModel;

    function _initialize() {
        parent::_initialize();
        $this->model = D('Mess/Message');
    }

    public function index($page = 1, $r = 10) {
        $map['status'] = array('neq', -1);
        $builder = new AdminListBuilder();
        //消息中心的列表
        $message = M('message');
        $list = $message->page($page, $r)->where($map)->select();
        foreach ($list as &$value) {
            if ($value['type'] == 0) {
                $value['type'] = '系统消息';
            }
            if ($value['is_read'] == 0) {
                $value['is_read'] = '未读取';
            } else {
                $value['is_read'] = '已读取';
            }
            if($value['uid']&&$value['vuid']==0) {
                $values = M('user')->field('user_name')->where('id=' . $value['uid'])->find();
                $value['uid'] = $values['user_name'];
                $value['vuid'] = ' ';
            }
            if($value['vuid']&&$value['uid']==0){
                $values = M('veterinary')->field('vete_name')->where('id=' . $value['vuid'])->find();
                $value['vuid'] = $values['vete_name'];
                $value['uid']  = ' ';
            }
        }
        $totalCount = $message->where($map)->count();
        $builder->title('消息中心列表')
            ->data($list)
            ->buttonNew(U('Mess/editMess'))
            ->setStatusUrl(U('Mess/setMessStatus'))
            ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Mess/index'))
            ->keyId()
            ->keyText('type', '消息回复来源')
            ->keyText('vuid', '推送消息给医生的')
            ->keyText('uid', '推送消息给用户的')
            ->keyText('content', '消息内容')
            ->keyText('is_read', '是否读取')
            ->keyCreateTime()
            ->setSearchPostUrl(U('Admin/Mess/index'))
            // ->search(L('检测中心名称'), 'message_name')
            // ->search(L('_CONTENT_'), 'content')
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setMessStatus($ids, $status = 1) {
        !is_array($ids) && $ids = explode(',', $ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Message', $ids, $status);
    }

    /**
     * 编辑
     */
    public function editMess() {
        $aId = I('id', 0, 'intval');
        $title = $aId ? "编辑" : "新增";
        $lists = array(0=>'所有的用户',1=>'所有的兽医');
        if (IS_POST) {
            //新增加uid
            $aId && $data['id'] = $aId;
            $data['uid'] = I('post.uid');

            if($data['uid']==0){
                 //发送给所有的用户
                $user = M('user')->where('status=1')->field('id')->select();
                foreach ($user as $value){
                    $data['uid']     = $value['id'];
                    $data['content'] = I('post.content', '', 'text');
                    $data['type'] = I('post.type', 0, 'intval');
                    $data['status'] = I('post.status', 1, 'intval');
                    $result = $this->model->editData($data);
                }

            }else if($data['uid']==1){
                //发送给所有的兽医
                $Vuser = M('veterinary')->where('status=1')->field('id')->select();
                foreach ($Vuser as $value){
                    unset($data['uid']);
                    $data['vuid']     = $value['id'];
                    $data['content'] = I('post.content', '', 'text');
                    $data['type'] = I('post.type', 0, 'intval');
                    $data['status'] = I('post.status', 1, 'intval');
                    $result = $this->model->editData($data);
                }

            }

            if ($result) {
                $aId = $aId ? $aId : $result;
                $this->success($title . '成功！', U('Mess/index'));
            } else {
                $this->error($title . '失败！', $this->model->getError());
            }
        } else {
            //编辑模式下
            if ($aId) {
                $data = $this->model->find($aId);
            }
            $builder = new AdminConfigBuilder();
            $builder->title($title . '消息')
                ->data($data)
                ->keyId()
                ->keySelect('uid', "选择回复人", '', $lists)
                ->keyTextArea('content', '系统回复消息内容')
                ->keyDefault('type', 0)
                ->keyDefault('status', 1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }

    public function singleMessage($page = 1, $r = 10) {
        $map['status'] = array('neq', -1);
        $builder = new AdminListBuilder();
        //消息中心的列表
        $message = M('message');
        $list = $message->page($page, $r)->where($map)->select();
        foreach ($list as &$value) {
            if ($value['type'] == 0) {
                $value['type'] = '系统消息';
            }
            if ($value['is_read'] == 0) {
                $value['is_read'] = '未读取';
            } else {
                $value['is_read'] = '已读取';
            }
            if($value['uid']&&$value['vuid']==0) {

                $values = M('user')->field('user_name')->where('id=' . $value['uid'])->find();
                $value['uid'] = $values['user_name'];
                $value['vuid'] = ' ';
            }
            if($value['vuid']&&$value['uid']==0){
                $values = M('veterinary')->field('vete_name')->where('id=' . $value['vuid'])->find();
                $value['vuid'] = $values['vete_name'];
                $value['uid']  = ' ';
            }

        }
        $totalCount = $message->where($map)->count();
        $builder->title('消息中心列表')
            ->data($list)
            ->buttonNew(U('Mess/editSingleMess'))
            ->setStatusUrl(U('Mess/setMessStatus'))
            ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Mess/singleMessage'))
            ->keyId()
            ->keyText('type', '消息回复来源')
            ->keyText('vuid', '推送消息给医生的')
            ->keyText('uid', '推送消息给用户的')
            ->keyText('content', '消息内容')
            ->keyText('is_read', '是否读取')
            ->keyCreateTime()
            ->setSearchPostUrl(U('Admin/Mess/singleMessage'))
            ->pagination($totalCount, $r)
            ->display();
    }

    public function editSingleMess() {
        $aId = I('id', 0, 'intval');
        $title = $aId ? "编辑" : "新增";
        $lists = array(0=>'普通用户',1=>'兽医用户');
        if (IS_POST) {
            //新增加uid
            $aId && $data['id'] = $aId;
            $data['uid'] = I('post.uid');
            $ids=I('ids');
            $ids = explode(',',$ids);
            if($data['uid']==0){
                //发送给用户
                foreach ($ids as $value){
                    $data['uid']     = $value;
                    $data['content'] = I('post.content', '', 'text');
                    $data['type'] = I('post.type', 0, 'intval');
                    $data['status'] = I('post.status', 1, 'intval');
                    $result = $this->model->editData($data);
                }
            }else if($data['uid']==1){
                //发送给兽医
                foreach ($ids as $value){
                    unset($data['uid']);
                    $data['vuid']     = $value;
                    $data['content'] = I('post.content', '', 'text');
                    $data['type'] = I('post.type', 0, 'intval');
                    $data['status'] = I('post.status', 1, 'intval');
                    $result = $this->model->editData($data);
                }

            }

            if ($result) {
                $aId = $aId ? $aId : $result;
                $this->success($title . '成功！', U('Mess/singleMessage'));
            } else {
                $this->error($title . '失败！', $this->model->getError());
            }
        } else {
            //编辑模式下
            if ($aId) {
                $data = $this->model->find($aId);
            }
            $builder = new AdminConfigBuilder();
            $builder->title($title . '消息')
                ->data($data)
                ->keyId()
                ->keySelect('uid', "选择回复人", '', $lists)
                ->keyText('ids','用户id（多个用户请用 ， 隔开）')
                ->keyTextArea('content', '系统回复消息内容')
                ->keyDefault('type', 0)
                ->keyDefault('status', 1)
                ->buttonSubmit()
                ->buttonBack()
                ->display();
        }
    }

} 