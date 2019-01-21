<?php
/**
 * Created by PhpStorm.
 * User: zoie
 * Date: 2018-11-27
 * Time: 16:37
 */
namespace App\Base\Plugins\Export;

class CsvExport
{
    public function __construct()
    {
    }

    /**
     * 导出csv文件
     * @param string $dir
     * @param string $file_name
     * @param array $csv_header
     * @param array $list
     * @param bool $save_local
     * @param bool $with_number
     * @param array $csv_top_text
     * @return bool
     */
    public function exportToCSV(string $dir,string $file_name,array $csv_header,array $list,bool $save_local = false,bool $with_number = false,array $csv_top_text = []) {
        set_time_limit(3000);
        if (count($list) < 1 || count($csv_header) < 1) {
            return false;
        }
        if ($save_local) {
            $header = '';
            mkdirs($dir);
            $fp = fopen($dir.$file_name.'.csv','w');
            $with_number ? array_unshift($csv_header,'序号') : '';
            // 处理顶行文字
            if (array_filter($csv_top_text)) {
                foreach ($csv_top_text as $top_text) {
                    $header .= getSafeStr($top_text) . PHP_EOL;
                }
            }
            // 处理头部标题
            $header .= getSafeStr(implode(',', $csv_header)) . PHP_EOL;
            // 处理内容
            $content = '';
            foreach ($list as $key => &$row) {
                $with_number ? array_unshift($row,$key) : '';
                $content .= getSafeStr(implode(',', $row)) . PHP_EOL;
            }
            // 拼接
            $csv = $header.$content;
            // 写入并关闭资源
            fwrite($fp, $csv);
            fclose($fp);
            return true;
        }else{
            header('Content-Type: application/vnd.ms-excel');
            header("Content-type:text/csv;charset=GBK");
            header('Content-Disposition: attachment;filename='.getSafeStr($file_name.'.csv'));
            header('Cache-Control: max-age=0');
            echo chr(0xEF).chr(0xBB).chr(0xBF);
            // 页面直出
            $fp = fopen('php://output', 'a');

            // 将标题名称通过fputcsv写到文件句柄
            $with_number ? array_unshift($csv_header,'序号') : '';
            $csv_header = array_filter($csv_header,function($k,$v){
                return getSafeStr($v);
            },ARRAY_FILTER_USE_BOTH);
            fputcsv($fp, $csv_header);

            $content = '';
            foreach ($list as $key => &$row) {
                $with_number ? array_unshift($row,$key) : '';
                $row = array_filter($row,function($k,$v){
                    return getSafeStr($v);
                },ARRAY_FILTER_USE_BOTH);
                fputcsv($fp, $row);
            }
            fclose($fp);
            exit;
        }
    }
}