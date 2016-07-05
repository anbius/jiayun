<?php
namespace Admin\Controller;
use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

class ArchivesController extends AdminController{

    protected $ArchivesModel;
    function _initialize()
    {
        parent::_initialize();
        $this->model = D('Archives/Archives');      
    }
   
    public function index($page=1,$r=10)
    {
        $map['status'] = array('neq',-1);		
        $builder=new AdminListBuilder();


        //检测中心的列表
        $Archives = M('Archives');
        $list = $Archives -> where($map) -> select();

        foreach($list as &$value){

            if($value['status'] ==1){
                $value['status']='关注';
            }else{
                $value['status']='未关注';
            }

            $user = M('user')->field('user_name') ->where('id='.$value['uid'])->find();
            $value['uid'] = $user['user_name'];
        }


        $totalCount = $Archives -> where($map) -> count();
        $builder->title('档案列表')
            ->data($list)
            ->setStatusUrl(U('Archives/setArchivesStatus'))
           ->buttonDelete()
            ->setSelectPostUrl(U('Admin/Archives/index'))            
            ->keyId()
            ->keyText('uid','用户名称')
            ->keyText('animal_id','动物种类')

            ->keyText('day_old','日龄')

            ->keyText('size','规模')
            ->keyText('im_program','免疫程序')
            ->keyText('dis_history','发病使')
            ->keyText('med_record','用药记录')
            ->keyText('sort','排序')
            ->keyText('status','是否可用')
            ->keyCreateTime()

          //  ->keyDoActionEdit('Archives/editArchives?id=###')
            ->setSearchPostUrl(U('Admin/Archives/index'))
           // ->search(L('检测中心名称'), 'Archives_name')
           // ->search(L('_CONTENT_'), 'content')
            ->pagination($totalCount,$r)
            ->display();
    }

    public function setArchivesStatus($ids,$status=1)
    {
        !is_array($ids)&&$ids=explode(',',$ids);
        $builder = new AdminListBuilder();
        $builder->doSetStatus('Archives', $ids, $status);
    }


} 