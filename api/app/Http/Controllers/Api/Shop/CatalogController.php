<?php
namespace App\Http\Controllers\Api\Shop;

use App\Models\Catalog;
use App\Models\Page;
use App\Models\PageHistory;
/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class CatalogController extends ShopController
{
    protected $catalog_model = null;
    protected $page_model = null;
    protected $pagehistory_model = null;

    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct(Catalog $catalog, Page $page, PageHistory $pageHistory)
    {
        parent::__construct();
        $this->catalog_model = $catalog;
        $this->page_model = $page;
        $this->pagehistory_model = $pageHistory;
    }

    //获取目录列表
    public function catList()
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

        $ret = $this->catalog_model->getList('*', ['item_id' => $item_id], 0,0, ['s_number'=>'asc', 'addtime'=>'asc']);
        return reJson('true', '200', '获取成功', $ret);

    }

    //获取二级目录列表
    public function secondCatList()
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

        $ret = $this->catalog_model->getList('*', ['item_id' => $item_id, 'level' =>2], 0,0, ['s_number'=>'asc', 'addtime'=>'asc']);
        return reJson('true', '200', '获取成功', $ret);

    }

    //获取目录列表
    public function catListGroup()
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

        $ret = $this->catalog_model->getList('*', ['item_id' => $item_id, 'level' =>2], 0,0, ['s_number'=>'asc']);
        if ($ret) {
            foreach ($ret as $key => &$value) {
                $value['addtime'] = date("Y-m-d H:i:s", $value['addtime']) ;

                $ret2 = $this->catalog_model->getList('*', ['parent_cat_id' => $value['cat_id']], 0,0, ['s_number'=>'asc']);

                if (empty($ret2)) {
                    $value['sub'] = array() ;
                }else{
                    foreach ($ret2 as $key2 => $value2) {
                        $ret2[$key2]['addtime'] = date("Y-m-d H:i:s", $value2['addtime']) ;
                    }
                    $value['sub'] = $ret2 ;
                }
            }
        }
        return reJson('true', '200', '获取成功', $ret);

    }

    //获取二级目录的子目录列表，即三级目录列表（如果存在的话）
    public function childCatList()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'cat_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $cat_id = def($post_data, 'cat_id', 0);

        $ret =  $this->catalog_model->getList('*', ['parent_cat_id' => $cat_id], 0,0, ['s_number'=>'asc']);
        return reJson('true', '200', '获取成功', $ret);

    }

    //保存目录
    public function save()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'cat_name' => 'required',
            's_number' => 'integer',
            'cat_id' => '',
            'parent_cat_id' => 'integer',
            'item_id' => 'required|integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $cat_id = def($post_data,'cat_id', 0);
        $parent_cat_id = def($post_data,'parent_cat_id', 0);

        if ($parent_cat_id &&  $parent_cat_id == $cat_id) {
            return reJson('false', '500', '上级目录不能选择自身');
        }

        $data['cat_name'] = $post_data['cat_name'] ;
        $data['s_number'] = def($post_data, 's_number', 99) ;
        $data['item_id'] = $post_data['item_id'];
        $data['parent_cat_id'] = $parent_cat_id ;
        if ($parent_cat_id > 0 ) {
            $data['level'] = 3;
        }else{
            $data['level'] = 2;
        }

        if ($cat_id > 0 ) {
            $ret = $this->catalog_model->up($data, ['cat_id' =>$cat_id]);
        }else{
            $data['addtime'] = time();
            $cat_id = $this->catalog_model->insert($data);
        }
        $return = $this->catalog_model->getRow('*', ['cat_id' => $cat_id]);
        if (!$return) {
            return reJson('false', '500', '保存失败');
        }
        return reJson('true', '200', '保存成功', $return);
    }

    //删除目录
    public function delete()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'cat_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $cat_id = $post_data['cat_id'];

        $cat = $this->catalog_model->getRow('*', ['cat_id' => $cat_id]);
        $item_id = $cat['item_id'];

        if ($this->page_model->getRow('*', ['cat_id' => $cat_id]) ||
            $this->catalog_model->getRow('*', ['parent_cat_id' =>$cat_id])) {

            return reJson('false', '500', 'no_delete_empty_catalog');
        }

        $ret = $this->catalog_model->del(['cat_id' => $cat_id]);
        if ($ret) {
            return reJson('true', '200', '删除成功');
        }else{
            return reJson('false', '500', '删除失败');
        }
    }

    //编辑页面时，自动帮助用户选中目录
    //选中的规则是：编辑页面则选中该页面目录，复制页面则选中目标页面目录;
    //如果是恢复历史页面则使用历史页面的目录，如果都没有则选中用户上次使用的目录
    public function getDefaultCat()
    {
        $uid = $this->data_info['uid'];
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'page_id' => 'integer',
            'item_id' => 'integer',
            'page_history_id' => 'integer',
            'copy_page_id' => 'integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);

        $page_id = def($post_data,'page_id', 0);
        $item_id = def($post_data,'item_id', 0);
        $page_history_id = def($post_data,'page_history_id', 0);
        $copy_page_id = def($post_data,'copy_page_id', 0);

        if ($page_id > 0 ) {
            if ($page_history_id) {
                $page = $this->pagehistory_model->getRow('*',['page_history_id' => $page_history_id]);
            }else{
                $page = $this->page_model->getRow('*', ['page_id' => $page_id]);
            }
            $default_cat_id = $page['cat_id'];
        } elseif ($copy_page_id) {

            //如果是复制接口
            $copy_page = $this->page_model->getRow('*', ['page_id' => $copy_page_id]);

            $page['item_id'] = $copy_page['item_id'];
            $default_cat_id = $copy_page['cat_id'];

        }else{
            //查找用户上一次设置的目录
            $last_page = $this->page_model->getRow('*', ['author_uid' => $uid, 'item_id'=>$item_id]);
            $default_cat_id = $last_page['cat_id'];
        }

        $default_cat_id3 = $default_cat_id;
        $default_cat_id2= $default_cat_id;

        if($default_cat_id){
            $Catalog = $this->catalog_model->getRow('*', ['cat_id' => $default_cat_id]);
            if (isset($Catalog['parent_cat_id']) && $Catalog['parent_cat_id']) {
                $default_cat_id2 = $Catalog['parent_cat_id'];
                $default_cat_id3 = $default_cat_id;
            }else{
                $default_cat_id2 = $default_cat_id;
            }
        }
        $data = [
            'default_cat_id2'=>$default_cat_id2,
            'default_cat_id3'=>$default_cat_id3
        ];

        return reJson('true', '200', '成功', $data);
    }

}
