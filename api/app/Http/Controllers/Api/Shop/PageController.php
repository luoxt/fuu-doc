<?php
namespace App\Http\Controllers\Api\Shop;

use App\Models\Page;
use App\Models\PageHistory;
use App\Models\Item;

/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class PageController extends ShopController
{
    protected $page_model = null;
    protected $pagehistory_model = null;
    protected $item_model = null;
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct(Page $page, Item $item, PageHistory $pageHistory)
    {
        parent::__construct();
        $this->page_model = $page;
        $this->pagehistory_model = $pageHistory;
        $this->item_model = $item;
    }

    //页面详情
    public function info()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'page_id' => 'required|integer',  
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $page_id = $post_data['page_id'];
        $page = $this->page_model->getRow('*', ['page_id'=>$page_id]);

        if (!$page) {
            return reJson('false', '501', '没有数据');
        }

        $page = $page ? $page : array();
        if ($page) {
            $page['addtime'] = date("Y-m-d H:i:s",$page['addtime']);
        }
        return reJson('true', '200', '获取成功', $page);
    }

    //删除页面
    public function delete()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'page_id' => 'required|integer',  
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $page_id = $post_data['page_id'];
        $page = $this->page_model->del(['page_id'=>$page_id]);

        if (!$page) {
            return reJson('false', '501', '没有数据');
        }

        return reJson('true', '200', '获取成功');
    }

    //保存
    public function save()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'page_id' => 'integer',
            'page_title' => 'required',
            'page_comments' => '',
            'page_content' => 'required',  //
            'cat_id' => 'required|integer',  
            'item_id' => 'required|integer',  
            's_number' => 'required|integer',  
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $page_id = def($post_data, 'page_id', 0);
        $item_id = def($post_data, 'item_id', 0);
        $cat_id = def($post_data, 'cat_id', 0);
        $page_title = def($post_data, 'page_title', '测试');
        $page_comments = def($post_data, 'page_comments', '');

        $data = [
            'page_title' => $page_title,
            'page_content' => $post_data['page_content'],
            'page_comments' => $page_comments,
            's_number' => $post_data['s_number'],
            'item_id' => $item_id,
            'cat_id' => $cat_id,
            'addtime' => time(),
            'author_uid' => def($this->data_info, 'uid', 0),
            'author_username' => def($this->data_info, 'username', '')
        ];

        //更新
        if ($page_id > 0 ) {

            //在保存前先把当前页面的版本存档
            $page = $this->page_model->getRow('*', ['page_id'=>$page_id]);;
            $insert_history = array(
                'page_id'=>$page['page_id'],
                'item_id'=>$page['item_id'],
                'cat_id'=>$cat_id,
                'page_title'=>$page_title,
                'page_comments'=>$page_comments,
                'page_content'=>base64_encode( gzcompress($page['page_content'], 9)),
                's_number'=>$page['s_number'],
                'addtime'=>$page['addtime'],
                'author_uid'=>$page['author_uid'],
                'author_username'=>$page['author_username'],
            );

            $this->pagehistory_model->insert($insert_history);

            unset($insert_history['page_id']);
            $this->page_model->up($data, ['page_id' => $page_id]);

            //统计该page_id有多少历史版本了
            $Count = $this->pagehistory_model->total(['page_id' => $page_id]);
            if ($Count > 20 ) {
                //每个单页面只保留最多20个历史版本
                $ret = $this->pagehistory_model->getList('*', ['page_id' => $page_id], 0, 20, ['page_history_id'=>'desc']);
                $this->pagehistory_model->del(['page_id' => $page_id, ['page_history_id', '<', $ret[19]['page_history_id']]]);
            }

            //如果是单页项目，则将页面标题设置为项目名
            $item_array = $this->item_model->getRow('*', ['item_id' => $item_id]);

            if (isset($item_array['item_type']) && $item_array['item_type'] == 2 ) {
                $this->item_model->up(["last_update_time"=>time(),"item_name"=>$page_title], ['item_id'=>$item_id]);
            }else{
                $this->item_model->up(array("last_update_time"=>time()), ['item_id'=>$item_id]);
            }

            $return = $this->page_model->getRow('*', ['page_id'=>$page_id]);

        }else{
            //创建
            $page_id = $this->page_model->insert($data);

            //更新项目时间
            $this->item_model->update(["last_update_time"=>time()], ['item_id' => $item_id]);

            $return = $this->page_model->getRow('*', ['page_id'=>$page_id]);
        }
        if (!$return) {
            return reJson('false', '501', '保存失败');
        }
        return reJson('true', '200', '获取成功', $return);
    }

    //历史版本列表
    public function history()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'page_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $PageHistory = $this->pagehistory_model->getList('*', $post_data, 0,10, ['addtime'=>'desc']);
        if ($PageHistory) {
            foreach ($PageHistory as $key => &$value) {
                $value['addtime'] = date("Y-m-d H:i:s" , $value['addtime']);
                $page_content = uncompress_string($value['page_content']);
                if (!empty($page_content)) {
                    $value['page_content'] = htmlspecialchars_decode($page_content) ;
                }
            }
        }
        return reJson('true', '200', '获取成功', $PageHistory);
    }

    //返回当前页面和历史某个版本的页面以供比较
    public function diff(){
        $page_id = I("page_id/d");
        $page_history_id = I("page_history_id/d");
        if (!$page_id) {
            return false;
        }
        $page = M("Page")->where(" page_id = '$page_id' ")->find();
        if (!$page) {
            sleep(1);
            $this->sendError(10101);
            return false;
        }
        $login_user = $this->checkLogin(false);
        if (!$this->checkItemVisit($login_user['uid'] , $page['item_id'])) {
            $this->sendError(10103);
            return;
        }

        $history_page = D("PageHistory")->where(" page_history_id = '$page_history_id' ")->find();
        $page_content = uncompress_string($history_page['page_content']);
        $history_page['page_content'] = $page_content ? $page_content : $history_page['page_content'] ;

        $this->sendResult(array("page"=>$page,"history_page"=>$history_page));
    }


    //上传图片
    public function uploadImg(){
        $login_user = $this->checkLogin();
        $item_id = I("item_id/d") ? I("item_id/d") : 0 ;
        $page_id = I("page_id/d") ? I("page_id/d") : 0 ;


        if ($_FILES['editormd-image-file']['name'] == 'blob') {
            $_FILES['editormd-image-file']['name'] .= '.jpg';
        }

        if (strstr(strtolower($_FILES['editormd-image-file']['name']), ".php") ) {
            return false;
        }

        $qiniu_config = C('UPLOAD_SITEIMG_QINIU') ;
        if (!empty($qiniu_config['driverConfig']['secrectKey'])) {
            //上传到七牛
            $Upload = new \Think\Upload(C('UPLOAD_SITEIMG_QINIU'));
            $info = $Upload->upload($_FILES);
            $url = $info['editormd-image-file']['url'] ;
            if ($url) {
                echo json_encode(array("url"=>$url,"success"=>1));
            }
        }else{
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './../Public/Uploads/';// 设置附件上传目录
            $upload->savePath = '';// 设置附件上传子目录
            $info = $upload->upload() ;
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
                return;
            }else{// 上传成功 获取上传文件信息
                $url = get_domain().__ROOT__.substr($upload->rootPath,1).$info['editormd-image-file']['savepath'].$info['editormd-image-file']['savename'] ;
                echo json_encode(array("url"=>$url,"success"=>1));
            }
        }

    }



}
