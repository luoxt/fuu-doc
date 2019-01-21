<?php
namespace App\Http\Controllers\Api\Shop;

use App\Models\Item;
use App\Models\Page;
use App\Models\PageHistory;
use App\Models\Catalog;
use App\Models\ItemMember;
use App\Models\UserToken;

/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class ItemController extends ShopController
{
    protected $item_model = null;
    protected $page_model = null;
    protected $catalog_model = null;
    protected $itemMember_model = null;
    protected $usertoken_model = null;

    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct(Item $item, Page $page, Catalog $catalog, ItemMember $itemMember, UserToken $userToken)
    {
        parent::__construct();

        $this->item_model = $item;
        $this->page_model = $page;
        $this->catalog_model = $catalog;
        $this->itemMember_model = $itemMember;
        $this->usertoken_model = $userToken;
    }

    //单个项目信息
    public function info()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'required|integer', 
            'item_domain' => '',
            'page' => '',
            'token' => '',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $item_id = $post_data['item_id'];
        $item_domain = def($post_data, 'item_domain', '');

        if (! is_numeric($item_id)) {
            $item_domain = $item_id ;
        }
        //判断个性域名
        if ($item_domain) {
            $item = $this->item_model->getRow('*', ['item_domain'=>$item_domain]);
            if ($item['item_id']) {
                $item_id = $item['item_id'] ;
            }
        }

        //项目详情
        $item = $this->item_model->getRow('*', ['item_id'=>$item_id]);
        if (!$item) {
            return reJson('false', '501', '项目不存在或者已删除');
        }

        //项目权限
        $item['is_login'] = 0;
        $item['ItemPermn'] = 0;
        $item['ItemCreator'] = 0;

        //获取登录信息
        $token = def($post_data, 'token', '');
        if($token) {
            $login_user = $this->usertoken_model->getToken($token);
            if(isset($login_user["uid"]) && $login_user["uid"]){
                $uid = $login_user["uid"];

                //登录状态
                $item['is_login'] = 1;

                //创建者
                if($item['uid'] == $uid){
                    $item['ItemCreator'] = 1;
                    $item['ItemPermn'] = 1;
                } else {
                    //项目参与者
                    $items_uids = $this->itemMember_model->getList('uid', ['item_id'=>$item_id]);
                    if($items_uids){
                        foreach ($items_uids as $val) {
                            if(isset($val['uid']) && $val['uid'] && $val['uid']==$uid){
                                $item['ItemPermn'] = 1;
                            }
                        }
                    }
                }
            }
        }

        if ($item['item_type'] == 2 ) {
            return $this->_show_single_page_item($item);
        } else {
            return $this->_show_regular_item($item);
        }
    }

    //展示常规项目
    private function _show_regular_item($item)
    {
        $item_id = $item['item_id'];
        $keyword = $this->request->input("keyword");

        //是否有搜索词
        if ($keyword) {

            /////////////////////
            $pages = D("Page")->where("item_id = '$item_id' and ( page_title like '%{$keyword}%' or page_content like '%{$keyword}%' ) ")->order(" `s_number` asc  ")->field("page_id,author_uid,cat_id,page_title,addtime")->select();

        } else {
            //获取所有父目录id为0的页面
            $pages = $this->page_model->getList('*', ['cat_id' => '0', 'item_id' =>$item_id], 0,0, ['s_number'=>'asc']);

            //获取所有二级目录
            $catalogs = $this->catalog_model->getList('*', ['item_id' =>$item_id, 'level'=>2], 0,0, ['s_number'=>'asc']);

            if ($catalogs) {
                foreach ($catalogs as $key => &$catalog) {
                    //该二级目录下的所有子页面
                    $temp = $this->page_model->getList('*', ['cat_id' => $catalog['cat_id']], 0,0, ['s_number'=>'asc']);
                    $catalog['pages'] = $temp ? $temp: array();

                    //该二级目录下的所有子目录
                    $temps = $this->catalog_model->getList('*', ['parent_cat_id' => $catalog['cat_id']]);

                    $catalog['catalogs'] = $temps ? $temps: array();
                    if($catalog['catalogs']){
                        //获取所有三级目录的子页面
                        foreach ($catalog['catalogs'] as $key3 => &$catalog3) {
                            //该二级目录下的所有子页面
                            $temp = $this->page_model->getList('*', ['cat_id' => $catalog3['cat_id']], 0,0, ['s_number'=>'asc']);
                            $catalog3['pages'] = $temp ? $temp: array();
                        }
                    }
                }
            }
        }

        //如果带了默认展开的页面id，则获取该页面所在的二级目录和三级目录
        $default_cat_id2 = $default_cat_id3 = 0 ;
        $default_page_id = $this->request->input("default_page_id", 0);
        if ($default_page_id) {
            $page = $this->page_model->getRow('*', ['page_id' => $default_page_id]);
            if ($page) {
                $default_cat_id3 = $page['cat_id'] ;
                $cat2 = $this->catalog_model->getRow('*', ['cat_id' => $default_cat_id3, ['parent_cat_id', '>', 0]]);
                if ($cat2) {
                    $default_cat_id2 = $cat2['parent_cat_id'];
                }else{
                    $default_cat_id2 = $default_cat_id3;
                    $default_cat_id3 = 0 ;
                }
            }
        }

        $menu =array(
            "pages" => $pages ,
            "catalogs" => $catalogs ,
        ) ;
        $unread_count = '';

        $return = array(
            "item_id"=>$item_id ,
            "item_domain"=>$item['item_domain'] ,
            "is_archived"=>def($item, 'is_archived', ''),
            "item_name"=>$item['item_name'] ,
            "default_page_id"=>(string)$default_page_id ,
            "default_cat_id2"=>$default_cat_id2 ,
            "default_cat_id3"=>$default_cat_id3 ,
            "unread_count"=>$unread_count ,
            "item_type"=>1 ,
            "menu"=>$menu ,
            "is_login"=>$item['is_login'],
            "ItemPermn"=>$item['ItemPermn'] ,
            "ItemCreator"=>$item['ItemCreator'],
        );
        return reJson('true', '200', '获取成功', $return);
    }

    //展示单页项目
    private function _show_single_page_item($item)
    {
        $item_id = $item['item_id'];
        $current_page_id = $this->request->input("page_id", 0);;

        //获取页面
        $page = $this->page_model->getRow('*', ['item_id' => $item_id]);
        $unread_count = '';

        $menu = array() ;
        $menu['pages'] = $page ;
        $return = array(
            "item_id"=>$item_id ,
            "item_domain"=>$item['item_domain'] ,
            "is_archived"=>$item['is_archived'] ,
            "item_name"=>$item['item_name'] ,
            "current_page_id"=>$current_page_id ,
            "unread_count"=>$unread_count ,
            "item_type"=>2 ,
            "menu"=>$menu ,
            "is_login"=>$item['is_login'],
            "ItemPermn"=>$item['ItemPermn'] ,
            "ItemCreator"=>$item['ItemCreator'],

        );
        return reJson('true', '200', '获取成功', $return);
    }

    //我的项目列表
    public function myList()
    {
        $uid = def($this->data_info, 'uid', 0);
        if (!$uid) {
            return reJson('true', '200', '请登录', []);
        }

        //自己创建
        $items = $this->item_model->getList(['item_id', 'item_name', 'last_update_time', 'item_description'], ['uid' => $uid], 0, 0, ['item_id'=>'asc']);
        $my_ids = [];
        if ($items) {
            foreach ($items as $item_val) {
                if(isset($item_val['item_id']) && $item_val['item_id']) {
                    $my_ids[] = $item_val['item_id'];
                }
            }
        }

        //自己参与
        $my_item = [];
        $items_idres = $this->itemMember_model->getList('item_id', ['uid'=>$uid]);
        if($items_idres){
            $items_id_arr = [];
            foreach ($items_idres as $ik => $ival){
                if(isset($ival['item_id']) && $ival['item_id']){
                    if(!in_array($ival['item_id'], $my_ids)){
                        $items_id_arr[] = $ival['item_id'];
                    }
                }
            }
            if($items_id_arr) {
                $my_item = $this->item_model->whereIn('item_id', $items_id_arr)->get();
                $my_item = json_decode($my_item,true);
            }
        }

        $items_new = array_merge($items, $my_item);

        //读取需要置顶的项目
//        $top_items = D("ItemTop")->where("uid = '$login_user[uid]'")->select();
//        if ($top_items) {
//            $top_item_ids = array() ;
//            foreach ($top_items as $key => $value) {
//                $top_item_ids[] = $value['item_id'];
//            }
//            foreach ($items as $key => $value) {
//                $items[$key]['top'] = 0 ;
//                if (in_array($value['item_id'], $top_item_ids) ) {
//                    $items[$key]['top'] = 1 ;
//                    $tmp = $items[$key] ;
//                    unset($items[$key]);
//                    array_unshift($items,$tmp) ;
//                }
//            }
//
//            $items = array_values($items);
//        }

        return reJson('true', '200', '获取成功', $items_new);
    }

    //项目详情
    public function detail()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = $post_data['item_id'];

        $items  = $this->item_model->getRow('*', ['item_id' => $item_id]);
        return reJson('true', '200', '获取成功', $items);
    }

    //更新项目信息
    public function update()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'required|integer',
            'item_name' => 'required',
            'item_description' => '',
            'item_domain' => '',
            'password' => '',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = $post_data['item_id'];
        unset($post_data['item_id']);

        $this->item_model->up($post_data, ['item_id'=>$item_id]);
        return reJson('true', '200', '获取成功', $params);
    }

    //转让项目
    public function attorn()
    {
        $login_user = $this->checkLogin();

        $username = I("username");
        $item_id = I("item_id/d");
        $password = I("password");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        if(! D("User")-> checkLogin($item['username'],$password)){
            $this->sendError(10208);
            return ;
        }

        $member = D("User")->where(" username = '%s' ",array($username))->find();

        if (!$member) {
            $this->sendError(10209);
            return ;
        }

        $data['username'] = $member['username'] ;
        $data['uid'] = $member['uid'] ;


        $id = D("Item")->where(" item_id = '$item_id' ")->save($data);

        $return = D("Item")->where("item_id = '$item_id' ")->find();

        if (!$return) {
            $this->sendError(10101);
        }

        $this->sendResult($return);
    }

    //删除项目
    public function delete()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $this->item_model->del($post_data);
        $this->page_model->del($post_data);
        $this->catalog_model->del($post_data);
        $this->itemMember_model->del($post_data);

        $pageHistory = new PageHistory();
        $pageHistory->del($post_data);

        return reJson('true', '200', '删除成功');
    }
    //归档项目
    public function archive()
    {
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");
        $password = I("password");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        if(! D("User")-> checkLogin($item['username'],$password)){
            $this->sendError(10208);
            return ;
        }

        $return = D("Item")->where("item_id = '$item_id' ")->save(array("is_archived"=>1));

        if (!$return) {
            $this->sendError(10101);
        }else{
            $this->sendResult($return);
        }


    }
    public function getKey()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = $post_data['item_id'];

        $items  = $this->item_model->getRow('*', ['item_id' => $item_id]);

        //$item_token  = D("ItemToken")->getTokenByItemId($item_id);

        //return reJson('false', '10101', '失败');
    }

    public function resetKey(){

        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");

        $item  = D("Item")->where("item_id = '$item_id' ")->find();

        if(!$this->checkItemCreator($login_user['uid'] , $item['item_id'])){
            $this->sendError(10303);
            return ;
        }

        $ret = D("ItemToken")->where("item_id = '$item_id' ")->delete();

        if ($ret) {
            $this->getKey();
        }else{
            $this->sendError(10101);
        }
    }

    public function updateByApi(){
        //转到Open控制器的updateItem方法
        R('Open/updateItem');
    }

    //置顶项目
    public function top(){
        $login_user = $this->checkLogin();

        $item_id = I("item_id/d");
        $action = I("action");

        if ($action == 'top') {
            $ret = D("ItemTop")->add(array("item_id"=>$item_id,"uid"=>$login_user['uid'],"addtime"=>time()));
        }
        elseif ($action == 'cancel') {
            $ret = D("ItemTop")->where(" uid = '$login_user[uid]' and item_id = '$item_id' ")->delete();
        }
        if ($ret) {
            $this->sendResult(array());
        }else{
            $this->sendError(10101);
        }
    }

    //验证访问密码
    public function pwd(){
        $item_id = I("item_id/d");
        $password = I("password");
        $v_code = I("v_code");
        $refer_url = I('refer_url');

        //检查用户输错密码的次数。如果超过一定次数，则需要验证 验证码
        $key= 'item_pwd_fail_times_'.$item_id;
        if(!D("VerifyCode")->_check_times($key,10)){
            if (!$v_code || $v_code != session('v_code')) {
                $this->sendError(10206,L('verification_code_are_incorrect'));
                return;
            }
        }

        $item = D("Item")->where("item_id = '$item_id' ")->find();
        if ($item['password'] == $password) {
            session("visit_item_".$item_id , 1 );
            $this->sendResult(array("refer_url"=>base64_decode($refer_url)));
        }else{
            D("VerifyCode")->_ins_times($key);//输错密码则设置输错次数

            if(D("VerifyCode")->_check_times($key,10)){
                $error_code = 10307 ;
            }else{
                $error_code = 10308 ;
            }
            $this->sendError($error_code,L('access_password_are_incorrect'));
        }

    }

    public function itemList()
    {
        $login_user = $this->checkLogin();
        $items  = D("Item")->where("uid = '$login_user[uid]' ")->select();
        $items = $items ? $items : array();
        $this->sendResult($items);
    }

    //新建项目
    public function add()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_name' => 'required',
            'item_description' => '',
            'item_domain' => '',
            'password' => '',
            'item_type' => '',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

//        $copy_item_id = $this->request->input("copy_item_id");
        //如果是复制项目
//        if ($copy_item_id > 0) {
//
//            $ret = D("Item")->copy($copy_item_id,$login_user['uid'],$item_name,$item_description,$password,$item_domain);
//            if ($ret) {
//                $this->sendResult(array());
//            }else{
//                $this->sendError(10101);
//            }
//            return ;
//        }

        $post_data['uid'] = $this->data_info['uid'];
        $post_data['username'] = $this->data_info['username'];
        $post_data['addtime'] = time();
        $item_id = $this->item_model->insert($post_data);

        if ($item_id) {
            //如果是单页应用，则新建一个默认页
            $item_type = def($post_data,'item_type',0);
            if ($item_type == 2 ) {
                $insert = array(
                    'author_uid' => $this->data_info['uid'],
                    'author_username' => $this->data_info['username'],
                    "page_title" => $post_data['item_name'] ,
                    "item_id" => $item_id ,
                    "cat_id" => 0 ,
                    "page_content" => '欢迎使用showdoc。点击右上方的编辑按钮进行编辑吧！' ,
                    "addtime" =>time()
                );
                $page_id = $this->page_model->insert($insert);
            }
        }

        return reJson('true', '200', '获取成功', $params);

    }


}
