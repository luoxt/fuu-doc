<?php
namespace App\Http\Controllers\Api\Shop;

use App\Models\User;
use App\Models\ItemMember;

/**
 *
 * @package App\Http\Controllers\Api\Organize
 */
class MemberController extends ShopController
{
    protected $user_model = null;
    protected $itemmember_model = null;

    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct(User $user, ItemMember $itemMember)
    {
        parent::__construct();
        $this->user_model = $user;
        $this->itemmember_model = $itemMember;
    }

    //保存
    public function save()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'member_group_id' => 'required|integer',
            'item_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = $post_data['item_id'];
        $member_group_id = $post_data['member_group_id'];

        $uid = def($this->data_info, 'uid', 0);

        $username = $this->request->input('username');
        $member = $this->user_model->getRow('*', ['username' =>$username]);
        if (!$member) {
            return reJson('false', '501', '用户不存在或者已删除');
        }

        $if_exit = $this->itemmember_model->getRow('*', ['uid' => $member['uid'], 'item_id' => $item_id]);
        if ($if_exit) {
            return reJson('false', '501', '该用户已经是项目成员');
        }
        $data['username'] = $member['username'] ;
        $data['uid'] = $member['uid'] ;
        $data['item_id'] = $item_id ;
        $data['member_group_id'] = $member_group_id ;
        $data['addtime'] = time() ;

        $id = $this->itemmember_model->insert($data);
        $return = $this->itemmember_model->getRow('*', ['item_member_id' => $id]);

        if (!$return) {
            return reJson('false', '501', '保存失败');
        }else{
            return reJson('true', '200', '保存成功', $return);
        }
    }

    //获取成员列表
    public function getList()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_id' => 'integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = def($post_data, 'item_id', 0);

        if ($item_id > 0 ) {
            $ret = $this->itemmember_model->getList('*', ['item_id' => $item_id], 0, 0, ['addtime'=> 'asc']);
        }
        if ($ret) {
            foreach ($ret as $key => &$value) {
                $value['addtime'] = date("Y-m-d H:i:s",$value['addtime']);
                $value['member_group'] = $value['member_group_id'] == 1 ? "编辑" :"只读";
            }
        }
        return reJson('true', '200', '保存成功', $ret);
    }

    //删除成员
    public function delete()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'item_member_id' => 'required|integer',
            'item_id' => 'required|integer'
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }
        $post_data = $this->postData($params, $rule);
        $item_id = $post_data['item_id'];
        $item_member_id = $post_data['item_member_id'];

        if ($item_member_id) {
            $ret = $ret = $this->itemmember_model->del(['item_id' => $item_id, 'item_member_id' =>$item_member_id]);
        }
        if ($ret) {
            return reJson('true', '200', '删除成功', $ret);
        }else{
            return reJson('false', '400', '删除失败');
        }
    }

}
