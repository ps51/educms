<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/4/2
 * Time: 11:12
 */
namespace app\admin\controller;

use think\Controller;
use app\base\controller\Site;
use app\common\model\Admin;
use app\base\controller\Template;

class AdminBase extends Controller
{
    protected $site_id;
    protected $template_path;
    protected $site;

    /**
     * 管理员模板初始化数据
     * @throws \think\exception\DbException
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $login_url = '/admin/login/index'; // 后台登录网址
        // 当前网站信息
        $site_data = new Site();
        $site_info = $site_data->info();
        if(empty($site_info)) {
            $this->error('欢迎使用培训分销系统，您的网站还没有开通，请联系电话：13450232305',$login_url);
        }

        $this->assign('site',$site_info);
        $this->site = $site_info;

        // 获取site_id
        $site_id = $site_info['id'];
        $this->site_id = $site_id;
        $this->assign('site_id',$site_id);

        // 判断是否有管理网站的权限
        $admin_username = session('admin_username');
        $admin_data = Admin::get(['username'=>$admin_username]);
        if($site_info['id'] != $admin_data['site_id']){
            $this->error('您没有管理该网站的权限',$login_url);
        }

        // 后台模板路径
        $template = new Template();
        $template_path = $template->path();
        $this->template_path = $template_path;
    }
}