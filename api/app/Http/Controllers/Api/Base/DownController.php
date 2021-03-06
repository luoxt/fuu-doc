<?php
namespace App\Http\Controllers\Api\Back\Base;

use App\Http\Controllers\Api\Back\BackController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * 基础接口
 * @package App\Http\Controllers\Api\Organize
 */
class DownController extends BackController
{
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param
     */
    public function __construct( )
    {
        parent::__construct();
    }

    public function down()
    {
        $cellData = [
            ['学号','姓名','成绩'],
            ['10001','AAAAA','99'],
            ['10002','BBBBB','92'],
            ['10003','CCCCC','95'],
            ['10004','DDDDD','89'],
            ['10005','EEEEE','96'],
        ];
        Excel::create('学生成绩',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');

        Excel::create('学生成绩',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->store('xls')->export('xls');

    }

}
