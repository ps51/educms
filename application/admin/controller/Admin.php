<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/26
 * Time: 15:16
 */
namespace app\admin\controller;

use app\common\model\Admin as AdminModel;
use app\common\model\AdminCategory;
use app\base\controller\Upload;

class Admin extends AdminBase
{
    /**
     * 管理员列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $post_username = $this->request->param('username');

        $data = new AdminModel;
        $admin_data = $data->get(['username'=>session('username')]);
        if($admin_data['id'] == 1){
            if(!empty($post_username)){
                $data_list = $data->where(['status' => 1])
                    ->where('username','like','%'.$post_username.'%')
                    ->select();
            }else{
                $data_list = $data->where(['status'=>1])->select();
            }
        }else{
            if(!empty($post_username)){
                $data_list = $data->where(['site_id'=>$this->site_id, 'status' => 1])
                    ->where('username','like','%'.$post_username.'%')
                    ->select();
            }else{
                $data_list = $data->where(['site_id'=>$this->site_id,'status'=>1])->select();
            }
        }

        foreach ($data_list as $data){
            $category_id = $data['category_id'];
            $admin_category = AdminCategory::get($category_id);
            $category_title = $admin_category['title'];
            $data['category_title'] = $category_title;
        }

        $data_count = count($data_list);
        $this->assign('data_count',$data_count);

        $this->assign('data_list',$data_list);

        return $this->fetch($this->template_path);
    }

    /**
     * 新增管理员
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function create()
    {
        // 获取分类列表
        $category_data = new AdminCategory();
        $category = $category_data->where(['status'=>1])
            ->where('level','>',0)
            ->select();
        $this->assign('category',$category);

        return $this->fetch($this->template_path);
    }

    /**
     * 保存管理员数据
     * @throws \think\exception\DbException
     */
    public function save()
    {
        $post_data = $this->request->param();
        $upload = new Upload();
        // 获取 略缩图 icon文件
        $post_data['icon'] = $upload->qcloud_file('icon');
        if(!empty($post_data['password'])){
            $post_data['password'] = md5($post_data['password']);
        }
        if(empty($post_data['username'])){
            $this->error('用户名不能为空');
        }
        $admin_username = AdminModel::get(['username'=>$post_data['username']]);
        if(!empty($admin_username)){
            $this->error('您填写的用户名已经被注册，请更换');
        }
        if(empty($post_data['tel'])){
            $this->error('手机号码不能为空');
        }
        $admin_tel = AdminModel::get(['tel'=>$post_data['tel']]);
        if(!empty($admin_tel)){
            $this->error('您填写的手机号码已经被注册，请更换');
        }


        $data = new AdminModel;
        $data_array = array('site_id','category_id','username','password','nickname','tel','qq','weixinhao','email','icon','ip','status');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('保存成功','/admin/admin/index');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 编辑管理员
     * @param $id
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($id)
    {
        // 获取当前分类id
        $category_id_info = AdminModel::get($id);
        $category_id = $category_id_info['category_id'];

        // 获取信息
        $data_list = AdminModel::get($id);
        $this->assign('data',$data_list);

        // 获取网站分类列表
        $category_data = new AdminCategory();
        $category = $category_data->where(['status'=>1])
            ->where('level','>', 0)
            ->select();
        $this->assign('category',$category);

        $my_category_data = AdminCategory::get($category_id);
        $my_category_title = $my_category_data['title'];
        $this->assign('my_category_id',$category_id);
        $this->assign('my_category_title',$my_category_title);

        return $this->fetch($this->template_path);
    }

    /**
     * 更新管理员
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $post_data = $this->request->param();
        if(!empty($post_data['password'])){
            $post_data['password'] = md5($post_data['password']);
            $data_array = array('site_id','category_id','password','nickname','tel','qq','weixinhao','email','icon','ip','status');
        }else{
            $data_array = array('site_id','category_id','nickname','tel','qq','weixinhao','email','icon','ip','status');
        }
        $upload = new Upload();
        // 获取 略缩图 icon文件
        $post_data['icon'] = $upload->qcloud_file('icon');
        if(empty($post_data['icon'])){
            unset($data_array[8]);
        }
        if(empty($post_data['password'])){
           unset($data_array[2]);
        }


        $data = AdminModel::get($post_data['id']);

        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            $this->success('更新成功', '/admin/admin/index');
        } else {
            $this->error('操作失败');
        }

    }

    /**
     * 删除管理员数据
     * @param $id
     * @throws \think\exception\DbException
     */
    public function delete($id)
    {
        $data = AdminModel::get($id);
        if ($data) {
            $data->delete();
            $this->success('删除管理员成功', '/admin/admin/index');
        } else {
            $this->error('您要删除的管理员不存在');
        }
    }

}