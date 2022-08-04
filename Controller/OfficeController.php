<?php

declare(strict_types=1);

namespace App\Application\Office\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Office\Model\OfficeImportTask;
use App\Application\Office\Service\ExcelFilter\Common\DateFilter;
use App\Application\Office\Service\ExcelFilter\Demo\GoodsFilter;
use App\Application\Office\Service\ExcelFilter\ExcelRowFilter;
use App\Application\Office\Service\ExcelImport\DemoImportService;
use App\Application\Office\Service\OfficeExcelService;
use App\Application\Office\Service\OfficeWordService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/office/office")
 */
class OfficeController extends AdminAbstractController
{

    /**
     * @RequestMapping(path="index/word")
     */
    function wordReplace()
    {
        $data = [
            'first_name' => '马',
            'last_name' => '云',
            'birth' => '2012/05/22',
            'phone' => '10086',
            'emergency_name' => '马化腾',
            'emergency_phone' => '10010',
            'allergy' => '微信',
            'family_doctor_name' => '雷军',
            'family_doctor_phone' => '13800138000',
            'family_doctor_clinic' => '小米手机',
            'health_precautions' => '客户经理',
            'creator_name' => '吴一平',
            'creator_phone' => '1588744',
        ];
        $word_service = new OfficeWordService();
        $template_path = BASE_PATH . '/app/Application/Office/Service/data/demo.docx';
        $res = $word_service->replace($template_path, $data);
        $file_path = $res['file_path'] ?? '';

        return $this->response->download($file_path);
    }

    /**
     * @RequestMapping(path="index/xls/demo")
     */
    function xlsDemo()
    {
        $file_path = BASE_PATH . '/app/Application/Office/Service/data/demo.xlsx';

        return $this->response->download($file_path);
    }

    /**
     * @Api()
     * @RequestMapping(path="index/import")
     */
    function xlsImport()
    {
        $file_path = $this->request->input('file_path', '');
        try {
            $excel_import_service = new DemoImportService($file_path);
            $res = $excel_import_service->importRecord();
            $import_data = $excel_import_service->getData();

            return compact('res', 'import_data');
        } catch (\Throwable $exception) {
            return $this->returnErrorJson($exception->getMessage());
        }
    }

    /**
     * @RequestMapping(path="index/export")
     */
    function xlsExport()
    {
        $fileName = '';
        $fileHeader = ["订单号", "订单总价", "订单商品", "下单时间"];
        $exportData = [
            new ExcelRowFilter([
                'a' => 1,
                'b' => 1.1,
                'c' => new GoodsFilter(['hello', 'world', 'excel'], 3), //有做分行显示
                'd' => new DateFilter(time())
            ], 3),
            [
                'a' => 2,
                'b' => 2.1,
                'c' => ['hello', 'world', 'excel'], //不做分行显示
                'd' => new DateFilter(time())
            ],
            [
                'a' => 3,
                'b' => 3.1,
                'c' => "hello 3",
                'd' => date('Y-m-d H:i:s', time())
            ]
        ];
        $ex = new OfficeExcelService($fileName, $fileHeader, $exportData);
        try {
            $res = $ex->save();
            $file_path = $res['file_path'] ?? '';

            return $this->response->download($file_path);
        } catch (\Throwable $exception) {
            return $this->returnErrorJson($exception->getMessage());
        }
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    public function index() { }

    /**
     * @Api()
     * @PostMapping(path="task/delete")
     */
    public function taskDelete()
    {
        $task_id = intval($this->request->post('task_id', 0));
        $task = OfficeImportTask::find($task_id);
        if (!$task) {
            return $this->returnErrorJson('抱歉，找不到该记录');
        }

        return $task->delete() ? [] : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="task/lists")
     */
    public function taskList()
    {
        $where = [];
        $lists = OfficeImportTask::where($where)
            ->orderBy('created_at', 'DESC')
            ->paginate();

        return compact('lists');
    }

    /**
     * @View()
     * @GetMapping(path="task")
     */
    public function task()
    {

    }
}
