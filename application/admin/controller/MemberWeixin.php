<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:25
 */
namespace app\admin\controller;

class MemberWeixin extends AdminBase
{
    /**
     * QQ 授权接口
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_title = $this->request->param('title');
        $data = new OauthQqModel;
        if(!empty($post_title)){
            $data_list = $data->where(['site_id'=>$this->site_id,'status' => 1])
                ->where('title','like','%'.$post_title.'%')
                ->select();
        }else{
            $data_list = $data->where(['site_id'=>$this->site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存
     */
    public function save()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if(empty($post_data['title'])){
            $this->error('标题不能为空');
        }

        $data = new OauthQqModel;
        $data_array = array('site_id','title','app_id','app_key','url','callback','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/oauth_qq/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        $data_list = OauthQqModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        $post_data['site_id'] = $this->site_id;
        if(empty($post_data['title'])){
            $this->error('标题不能为空');
        }

        $data = OauthQqModel::get($post_data['id']);
        $data_array = array('site_id','title','app_id','app_key','url','callback','sort','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/oauth_qq/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = OauthQqModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/oauth_qq/index');
        } else {
            $this->error('删除失败');
        }
    }
}