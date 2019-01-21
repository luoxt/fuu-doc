<?php
/**
 * @brief: 模型基类
 * @author: luoxt
 * @date：2017-07-18
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        //初始化
    }

    //查询对象
    protected function querys()
    {
        return DB::table($this->table);
    }

    /**
     * @brief 获取记录数量
     * @param null $filter
     * @return int
     */
    public function total($filter=null)
    {
        $where = [];
        if(is_array($filter)){
            $where = $filter;
        }
        $total = $this->where($this->querys(), $where)->count();
        return $total ? $total : 0;
    }

    /**
     * @brief 获取多条记录
     * @param string $row
     * @param array $filter
     * 1、直接参数模式
     * $filter = [
     *       ['client_id', '=', $client_id],
     *       ['status', '=', def($post, 'status', '')],
     *       ['login_account', '=', def($post, 'login_account', '')]
     * ];
     *
     * 2、混合参数模式
     * $filter = [
     *       'client_id'=>$client_id,
     *       ['status', '<>', 0],
     *       'login_account'=>def($post, 'login_account', '')
     * ];
     *
     * @param int $offset
     * @param int $limit
     * @param array $orderBy
     * @return bool|mixed
     */
    public function getList($row='*', $filter=array(), $offset=0, $limit=0, $orderBy=[])
    {
        $query = $this->select($this->querys(), $row);
        $query = $this->where($query, $filter);

        $query = $this->offset($query, $offset);
        $query = $this->limit($query, $limit);

        $query = $this->orderBy($query, $orderBy);

        $res = $query->get();
        return json_decode($res, true);
    }

    /**
     * @brief 获取单条记录
     * @param string $row
     * @param array $filter
     * @return bool|mixed
     */
    public function getRow($row='*', $filter=[])
    {
        $query = $this->select($this->querys(), $row);
        $query = $this->where($query, $filter);
        $res = $query->first();

        return obj2arr($res);
    }

    /**
     * @brief 插入记录
     * @params $postdata 插入数据
     * @return bool|mixed
     */
    public function insert($postdata=[])
    {
        if (empty($postdata)){
            return false;
        }
        $res = $this->querys()->insertGetId($postdata);
        return $res;
    }

    /**
     * @brief 更新单条记录
     * @params $postdata 更新数据
     * @params $filter 条件
     *
     */
    public function up($postdata=[], $filter=[])
    {
        if (empty($postdata)){
            return false;
        }
        $query = $this->where($this->querys(), $filter);
        $res = $query->update($postdata);
        return $res;
    }

    /**
     * @brief 删除单条记录
     * @params $filter 条件
     *
     */
    public function del($filter=[])
    {
        if (empty($filter)){
            return false;
        }
        $query = $this->where($this->querys(), $filter);
        $res = $query->delete();
        return $res;
    }

    protected function select($query, $row='*')
    {
        return $query->select($row);
    }

    protected function where($query, $filter=[])
    {
        $filter_new = [];
        if(is_array($filter)){
            foreach ($filter as $key =>$value) {
                if(is_string($key) && strlen($value)){
                    $filter_new[] = [$key, '=', $value];
                }
                if(is_array($value)){
                    list($col, $eq, $val) = $value;
                    if($col && strlen($val)){
                        $filter_new[] = [$col, $eq, $val];
                    }
                }
            }
        }

        if(!count($filter_new)){
            return $query;
        }

        return $query->where($filter_new);

    }

    protected function offset($query, $start=0)
    {

        if($start) {
            return $query->offset($start);

        }
        return $query;
    }

    protected function limit($query, $len=0)
    {
        if(!$len){
            return $query;
        }
        return $query->limit($len);
    }

    protected function orderBy($query, $orderBy=[])
    {
        if(empty($orderBy) || !is_array($orderBy)){
            return $query;
        }

        foreach ($orderBy as $key => $value) {
            if($value){
                $query = $query->orderBy($key, $value);
            }
        }
        return  $query;
    }


}
