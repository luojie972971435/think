<?php

namespace app\common\model\admin;

use think\Model;
use think\facade\Config;

class AdminGroup extends Model
{
    /**
     * 获取分页列表
     * @param array $where 查询数据条件
     * @param int $page 一页显示N条数据
     * @param string $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public static function systemPage($where = [], $page = 0, $field = '*', $order = 'create_time asc')
    {
        $conf = Config::get("system.");
        $rows = empty($page) ? $conf['LIST_ROWS'] : $page;
        $list = self::field($field)
            ->where($where)
            ->order($order)
            ->paginate($rows, false, ['query' => $where])->each(function ($item) {
                $item['create_time'] = date('Y-m-d H:i', $item['create_time']);

            }) ?: [];
        return $list;
    }

    /**
     * 获取列表数据
     * @param array $where 查询数据条件
     * @param string $field 查询字段
     * @param string $order 排序条件
     * @return array
     */
    public static function getList($where = [], $field = '*', $order = 'create_time asc')
    {
        $list = self::field($field)->where($where)->order($order)->select();
        if (!empty($list)) {
            if (is_object($list)) $list = $list->toArray();
            return $list;
        } else {
            return [];
        }
    }

    /**
     * 添加数据，并返回数据ID
     * @param array $param 前端传递过来参数
     * @return int 返回数据ID
     */
    public static function insertGetIdTo($param)
    {
        $param['create_time'] = time();
        return self::strict(false)->field(true)->insertGetId($param);
    }

    /**
     * 添加数据
     * @param array $param 前端传递过来参数
     * @return boolean 返回true成功，false失败
     */
    public static function insertTo($param)
    {
        $param['create_time'] = time();
        return self::strict(false)->field(true)->insert($param);
    }

    /**
     * 获取单条数据详情
     * @param array  $where 查询数据条件
     * @param string $field 查询字段
     * @return array
     */
    public static function getInfo($where = [], $field = '*')
    {
        $detail = self::field($field)->where($where)->find();
        if (!empty($detail)) {
            if (is_object($detail)) $detail = $detail->toArray();
            return $detail;
        } else {
            return [];
        }
    }

    /**
     * 根据ID修改数据
     * @param array $param 前端传递过来参数
     * @return boolean 返回true成功，false失败
     */
    public static function updateIdTo($param)
    {
        $param['update_time'] = time();
        return self::where(['id' => $param['id']])->strict(false)->field(true)->update($param);
    }

    /**
     * 自定义条件修改数据
     * @param array $where 自定义修改条件
     * @param array $param 前端传递过来参数
     * @return boolean 返回true成功，false失败
     */
    public static function updateTo($where = [], $param)
    {
        $param['update_time'] = time();
        return self::where($where)->strict(false)->field(true)->update($param);
    }

    /**
     * 删除数据
     * @param array $where 自定义删除条件
     * @return boolean 返回true成功，false失败
     */
    public static function deleteTo($where = [])
    {
        return self::where($where)->strict(false)->field(true)->delete();
    }

    /**
     * 删除数据
     * @param int $id 根据ID为删除条件
     * @return boolean 返回true成功，false失败
     */
    public static function deleteIdTo($id)
    {
        return self::where(['id' => $id])->strict(false)->field(true)->delete();
    }

    /**
     * 获取数据的某字段
     * @param array $where 自定义查询条件
     * @param string $field 自定义查询字段
     * @return string
     */
    public static function getValue($where = [], $field = 'id')
    {
        return self::where($where)->value($field);
    }

    /**
     * 统计数据数量
     * @param array $where 自定义查询条件
     */
    public static function getCount($where = [], $field = '*')
    {
        $count = self::where($where)->count($field);
        return intval($count);
    }

    /**
     * 获取查询数据的ID
     * @param array $where 自定义查询条件
     * @param string $field 自定义查询字段
     * @return array
     */
    public static function getColumnId($where = [], $field = 'id')
    {
        return self::where($where)->column($field);
    }

    /**
     * 统计数据总和
     * @param array $where 自定义查询条件
     * @param string $field 自定义查询字段，字段值必须是数字
     */
    public static function getSum($where = [], $field = 'id')
    {
        $sum = self::where($where)->sum($field);
        return $sum;
    }


    /**
     * 获取数据的某字段
     * @param array $where 自定义查询条件
     * @param string $field 自定义查询字段
     * @return string
     */
    public static function getJoinValue($where = [], $field = 'group.id')
    {
        $value = self::alias('group')
            ->join('admin_group_access access', 'group.id = access.group_id')
            ->where($where)->value($field);
        if (!empty($value)) {
            return $value;
        } else {
            return '';
        }
    }
}
