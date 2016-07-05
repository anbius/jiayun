<?php
namespace Version\Model;
use Think\Model;
class VersionModel extends Model
{

    public function editData($data)
    {   
        if ($data['id']) {           
            $res = $this->save($data);           
        } else {
            $data['create_time'] = time();
            $res = $this->add($data);           
        }
        return $res;
    }

    public function getListByPage($map, $page = 1, $order = 'update_time desc', $field = '*', $r = 20)
    {
        $totalCount = $this->where($map)->count();
        if ($totalCount) {
            $list = $this->where($map)->page($page, $r)->order($order)->field($field)->select();
        }
        return array($list, $totalCount);
    }

    public function getList($map, $order = 'view desc', $limit = 5, $field = '*')
    {
        $lists = $this->where($map)->order($order)->limit($limit)->select();
        return $lists;
    }
} 