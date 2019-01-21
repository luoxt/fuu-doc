<?php
/**
 * Created by PhpStorm.
 * User: ttyun
 * Date: 2018/11/3
 * Time: 14:27
 */

namespace App\Models;

/**
 * c端用户信息(主)表
 */
class Item extends BaseModel
{
    protected $table = 'item';

    public $primaryKey = 'item_id';//主键

    public $incrementing = false;//是否自增主键

    public $timestamps = false;//不自动维护created_at 和 updated_at

    public $guarded = ['']; //字段批量赋值黑名单 为空表示不限制


    public function export($item_id){
        $item = D("Item")->where("item_id = '$item_id' ")->field(" item_type, item_name ,item_description,password ")->find();
        //获取所有父目录id为0的页面
        $pages = D("Page")->where("cat_id = '0' and item_id = '$item_id' ")->field(" page_title ,page_content,s_number,page_comments ")->order(" `s_number` asc  ")->select();
        //获取所有二级目录
        $catalogs = D("Catalog")->where("item_id = '$item_id' and level = 2  ")->field("cat_id, cat_name ,level,s_number ")->order(" `s_number` asc  ")->select();
        if ($catalogs) {
            foreach ($catalogs as $key => &$catalog) {
                //该二级目录下的所有子页面
                $temp = D("Page")->where("cat_id = '$catalog[cat_id]' ")->field(" page_title ,page_content,s_number,page_comments ")->order(" `s_number` asc  ")->select();
                $catalog['pages'] = $temp ? $temp: array();
                //该二级目录下的所有子目录
                $temp = D("catalog")->where("parent_cat_id = '$catalog[cat_id]' ")->field(" cat_id,cat_name ,level,s_number ")->order(" `s_number` asc  ")->select();
                $catalog['catalogs'] = $temp ? $temp: array();
                if($catalog['catalogs']){
                    //获取所有三级目录的子页面
                    foreach ($catalog['catalogs'] as $key3 => &$catalog3) {
                        //该二级目录下的所有子页面
                        $temp = D("Page")->where("cat_id = '$catalog3[cat_id]' ")->field(" page_title ,page_content,s_number,page_comments ")->order(" `s_number` asc  ")->select();
                        $catalog3['pages'] = $temp ? $temp: array();
                        unset($catalog3['cat_id']);
                    }
                }
                unset($catalog['cat_id']);
            }
        }
        $item['pages'] = array(
            "pages" =>$pages,
            "catalogs" =>$catalogs,
        );
        unset($pages);
        unset($catalogs);
        $item['members'] = D("ItemMember")->where("item_id = '$item_id' ")->field(" member_group_id ,uid,username ")->select();
        return  json_encode($item);

    }
    public function import($json,$uid,$item_name= '',$item_description= '',$item_password = '',$item_domain = ''){
        $userInfo = D("User")->userInfo($uid);
        $item = json_decode($json ,1 );
        unset($json);
        if ($item) {
            if ($item['item_domain']) {
                $item2 = D("Item")->where("item_domain = '%s'  ".array($item['item_domain']))->find();
                if ($item2) {
                    //个性域名已经存在
                    return false;
                }
                if(!ctype_alnum($item_domain) ||  is_numeric($item_domain) ){
                    //echo '个性域名只能是字母或数字的组合';exit;
                    return false;
                }
            }else{
                $item['item_domain'] = '';
            }
            $item_data = array(
                "item_name"=>$item_name ? $item_name :$item['item_name'],
                "item_domain"=>$item_domain ? $item_domain :$item['item_domain'],
                "item_type"=>$item['item_type'],
                "item_description"=>$item_description ? $item_description :$item['item_description'],
                "password"=>$item_password ? $item_password :$item['password'],
                "uid"=>$userInfo['uid'],
                "username"=>$userInfo['username'],
                "addtime"=>time(),
            );
            $item_id = D("Item")->add($item_data);
        }
        if ($item['pages']) {
            //父页面们（一级目录）
            if ($item['pages']['pages']) {
                foreach ($item['pages']['pages'] as $key => &$value) {
                    $page_data = array(
                        "author_uid"=>$userInfo['uid'],
                        "author_username"=>$userInfo['username'],
                        "page_title" =>$value['page_title'],
                        "page_content" =>$value['page_content'],
                        "s_number" =>$value['s_number'],
                        "page_comments" =>$value['page_comments'],
                        "item_id" => $item_id,
                        "cat_id" => 0 ,
                        "addtime" =>time(),
                    );
                    D("Page")->add($page_data);
                    unset($page_data);
                }
                unset($item['pages']['pages']);
            }
            //二级目录
            if ($item['pages']['catalogs']) {
                foreach ($item['pages']['catalogs'] as $key => &$value) {
                    $catalog_data = array(
                        "cat_name" => $value['cat_name'],
                        "level" => $value['level'],
                        "s_number" => $value['s_number'],
                        "item_id" => $item_id,
                        "addtime" =>time(),
                    );
                    $cat_id = D("Catalog")->add($catalog_data);
                    //二级目录的页面们
                    if ($value['pages']) {
                        foreach ($value['pages'] as $key2 => &$value2) {
                            $page_data = array(
                                "author_uid"=>$userInfo['uid'],
                                "author_username"=>$userInfo['username'],
                                "page_title" =>$value2['page_title'],
                                "page_content" =>$value2['page_content'],
                                "s_number" =>$value2['s_number'],
                                "page_comments" =>$value2['page_comments'],
                                "item_id" => $item_id,
                                "cat_id" => $cat_id ,
                                "addtime" =>time(),
                            );
                            D("Page")->add($page_data);
                            unset($page_data);
                            unset($value2);
                        }
                    }
                    //判断是否存在三级目录
                    if ($value['catalogs']) {
                        foreach ($value['catalogs'] as $key3 => &$value3) {
                            $catalog_data = array(
                                "cat_name" => $value3['cat_name'],
                                "level" => $value3['level'],
                                "s_number" => $value3['s_number'],
                                "parent_cat_id" => $cat_id,
                                "item_id" => $item_id,
                                "addtime" =>time(),
                            );
                            $cat_id2 = D("Catalog")->add($catalog_data);
                            //三级目录的页面们
                            if ($value3['pages']) {
                                foreach ($value3['pages'] as $key4 => &$value4) {
                                    $page_data = array(
                                        "author_uid"=>$userInfo['uid'],
                                        "author_username"=>$userInfo['username'],
                                        "page_title" =>$value4['page_title'],
                                        "page_content" =>$value4['page_content'],
                                        "s_number" =>$value4['s_number'],
                                        "page_comments" =>$value4['page_comments'],
                                        "item_id" => $item_id,
                                        "cat_id" => $cat_id2 ,
                                        "addtime" =>time(),
                                    );
                                    D("Page")->add($page_data);
                                    unset($page_data);
                                    unset($value4);
                                }
                            }
                            unset($value3);
                        }
                    }
                    unset($value);
                }

            }
        }

        if ($item['members']) {
            foreach ($item['members'] as $key => $value) {
                $member_data = array(
                    "member_group_id"=>$value['member_group_id'],
                    "uid"=>$value['uid'],
                    "username"=>$value['username'],
                    "item_id"=>$item_id,
                    "addtime"=>time(),
                );
                D("ItemMember")->add($member_data);
            }
        }
        return $item_id;
    }

    public function copy($item_id,$uid,$item_name= '',$item_description= '',$item_password = '',$item_domain){
        return $this->import($this->export($item_id),$uid,$item_name,$item_description,$item_password,$item_domain);
    }

    //清空项目内容，但不删除项目也不删除项目成员关系
    public function empty_content($item_id){
        D("Page")->where("item_id = '$item_id' ")->delete();
        D("Page")->partitionTable($item_id)->where("item_id = '$item_id' ")->delete();
        D("Catalog")->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(1)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(2)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(3)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(4)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(5)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(6)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(7)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(8)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(9)->where("item_id = '$item_id' ")->delete();
        D("PageHistory")->partitionTable(10)->where("item_id = '$item_id' ")->delete();
        //删除菜单缓存
        $redis = init_redis() ;
        $redis->delete('showdoc_menu_cache_item_id_'.$item_id);
    }
}