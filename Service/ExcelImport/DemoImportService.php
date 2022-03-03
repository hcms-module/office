<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 14:26
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelImport;

use App\Application\Office\Model\OfficeImportTask;
use App\Application\Office\Service\AbstractExcelImport;
use App\Application\Office\Service\ExcelFilter\Common\TrimFilter;
use App\Application\Office\Service\ExcelFilter\ExcelColumnFilter;

class DemoImportService extends AbstractExcelImport
{
    protected int $start_row = 4;
    protected int $column_count = 5;

    public function __construct(string $file_path)
    {
        parent::__construct($file_path);
        /**
         * 定义导入列
         */
        $this->keys = [
            'id',
            'company_name',
            'company_description',
            'work_name',
            new ExcelColumnFilter('work_level', TrimFilter::class)
        ];
    }

    protected function executeImport(): int
    {
        return OfficeImportTask::IMPORT_STATUS_SUCCESS;
    }
}