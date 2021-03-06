<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/11/14
 * Time: 9:42
 */
namespace app\index\controller;

use app\base\controller\Base;
use think\Request;
use app\common\model\Member;
use app\common\model\MemberWeixin;
use app\base\controller\BrowserCheck;
use app\base\controller\Weixin;
use app\common\model\WechatOfficialAccounts;

class Enroll extends Base
{
    /**
     * 佣金计算
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 微信jssdk
        $official_accounts_info = WechatOfficialAccounts::get(['site_id'=>$this->site_id]);
        $app_id = $official_accounts_info['app_id'];
        $app_secret = $official_accounts_info['app_secret'];
        $jssdk = new Jssdk();
        $signPackage = $jssdk->GetSignPackage($app_id,$app_secret);
        $this->assign('wx_share',$signPackage);

        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){
            $weixin_user_info = new Weixin();
            $openid = $weixin_user_info->info($this->site_id,session('mid'));
            $this->assign('openid',$openid);
            // 获取会员信息
            $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
            $member_weixin_id = $member_weixin_info['id'];

            $member_info = Member::get(['weixin_id'=>$member_weixin_id]);
            if(!empty($member_info)){
                $member_info['name'] = $member_info['real_name'];

                $this->assign('member_data',$member_info);
            }else{
                $member_weixin_info['name'] = $member_weixin_info['nickname'];
                $this->assign('member_data',$member_weixin_info);
            }

            $member_weixin_data = MemberWeixin::get(['mid'=>session('mid')]);
            $this->assign('member_weixin',$member_weixin_data);
        }

        return $this->fetch($this->template_path);
    }

    public function update(Request $request)
    {
        $data = $request->param();
        var_dump($data);
    }


}