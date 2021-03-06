<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/29
 * Time: 22:32
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\ApiCategory as ApiCategoryModel;


class ApiCategory extends AdminBase
{
    /**
     * API接口分类
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $site_id = $this->site_id;
        // 找出列表数据
        $post_title = $request->param('title');
        $data = new ApiCategoryModel;
        if(!empty($post_title)){
            $data_list = $data->where(['status' => 1, 'title' => ['like','%'.$post_title.'%']])->select();
        }else{
            $data_list = $data->where(['site_id'=>$site_id,'status'=>1])->select();
        }
        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增API接口分类
     * @return mixed
     */
    public function create()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存API接口分类
     * @param Request $request
     */
    public function save(Request $request)
    {
        $post_title = $request->param('title');
        $post_status = $request->param('status');
        if($post_title==''){
            $this->error('分类名称不能为空');
        }
        $data = new ApiCategoryModel;
        $data['site_id'] = $this->site_id;
        $data['title'] = $post_title;
        $data['status'] = $post_status;
        if ($data->save()) {
            $this->success('保存成功','/admin/api_category/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 编辑API接口分类
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取网站信息
        $data_list = ApiCategoryModel::get($id);
        $this->assign('data',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新API接口分类
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function update(Request $request)
    {
        $post_id = $request->post('id');
        $post_title = $request->post('title');
        $post_status= $request->post('status');
        if(empty($post_title)){
            $this->error('分类名称不能为空');
        }

        $user = ApiCategoryModel::get($post_id);
        $user['site_id'] = $this->site_id;
        $user['title'] = $post_title;
        $user['status'] = $post_status;
        if ($user->save()) {
            $this->success('保存成功', '/admin/api_category/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 删除API接口分类
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = ApiCategoryModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除成功', '/admin/api_category/index');
        } else {
            $this->error('删除失败');
        }
    }

}