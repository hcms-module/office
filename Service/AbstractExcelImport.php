<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 13:42
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service;

use App\Application\Office\Model\OfficeImportTask;
use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;
use App\Application\Office\Service\ExcelFilter\ExcelColumnFilter;
use App\Exception\ErrorException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

abstract class AbstractExcelImport
{
    /**
     * 导入数据开始的行
     *
     * @var int
     */
    protected int $start_row = 0;

    /**
     * 文件导入行数
     *
     * @var int
     */
    protected int $column_count = 0;
    protected array $data = [];
    protected array $keys = [];

    protected Spreadsheet $spreadsheet;
    protected Worksheet $sheet;
    protected string $file_path;


    /**
     * ExcelImportService constructor.
     *
     * @param string $file_path
     */
    public function __construct(string $file_path)
    {
        $this->spreadsheet = IOFactory::load($file_path);
        $this->file_path = $file_path;
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     *  导入执行方法，每一个独立模块都想单独定义
     *
     * @return int
     */
    abstract protected function executeImport(): int;

    /**
     * @throws Throwable
     */
    public function importRecord(): OfficeImportTask
    {
        $status = $this->executeImport();
        $data_md5 = md5(json_encode($this->getData()));
        $file_path = $this->file_path;

        return OfficeImportTask::create([
            'file_path' => $file_path,
            'data_md5' => $data_md5,
            'import_status' => $status
        ]);
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function getData(): array
    {
        if (empty($this->data)) {
            return $this->importData();
        }

        return $this->data;
    }

    /**
     * 导入数据处理
     *
     * @throws Throwable
     */
    private function importData(): array
    {
        $cells = $this->sheet->getCellCollection();
        $currentRow = $cells->getCurrentRow();
        $currentColumn = $this->col2Int($cells->getCurrentColumn()) + 1;
        if ($currentRow < $this->start_row) {
            //如果规定行数大于读取的行数，则返回错误
            throw new ErrorException('gets the number of rows less than the start-row');
        }
        if ($this->column_count > $currentColumn) {
            //如果规定列数大于读取的列数，则返回错误
            throw new ErrorException('need column not enough');
        }
        if ($this->column_count != 0) {
            $currentColumn = $this->column_count;
        }
        $data = [];
        for ($i = $this->start_row; $i <= $currentRow; $i++) {
            //执行单个导入
            $itemData = $this->importDataItem($i, $currentColumn);
            $data[] = $itemData;
        }
        $this->data = $data;

        return $data;
    }

    private function importDataItem($i, $currentColumn): array
    {
        $itemData = [];
        for ($j = 0; $j < $currentColumn; $j++) {
            $cellKey = $this->getChar($j) . $i;
            $mergeRange = $this->sheet->getCell($cellKey)
                ->getMergeRange();
            if ($mergeRange !== false) {
                //如果是合并的单元格，默认使用合并第一个单元的数据
                $cellKey = explode(':', $mergeRange)[0] ?? $cellKey;
            }
            $value = $this->sheet->getCell($cellKey)
                ->getFormattedValue();
            if (is_array($value)) {
                $value = json_encode($value);
            }
            if (!empty($this->keys[$j])) {
                if ($this->keys[$j] instanceof ExcelColumnFilter) {
                    $key = $this->keys[$j]->key;
                    //如果有过滤器，则使用过滤器
                    if ($this->keys[$j]->filter && class_exists($this->keys[$j]->filter)) {
                        $filterClass = $this->keys[$j]->filter;
                        //获取过滤器的值
                        $filter = new $filterClass($value);
                        if ($filter instanceof AbstractExcelValueFilter) {
                            $value = $filter->getFilterValue();
                        }
                    }
                    $itemData[$key] = $value;
                } else {
                    $itemData[$this->keys[$j]] = $value;
                }
            } else {
                $itemData[] = $value;
            }
        }

        return $itemData;
    }

    /**
     * 获取列数，获取A、B、C、AA
     *
     * @param $i
     * @return string
     */
    private function getChar($i): string
    {
        $y = ($i / 26);
        if ($y >= 1) {
            $y = intval($y);

            return chr($y + 64) . chr($i - $y * 26 + 65);
        } else {
            return chr($i + 65);
        }
    }

    private function col2Int(string $str): int
    {
        $num = 0;
        $strArr = str_split($str, 1);
        $length = count($strArr);
        foreach ($strArr as $k => $v) {
            $num += ((ord($v) - ord('A') + 1) * pow(26, $length - $k - 1));
        }

        return (int)$num - 1;
    }


    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }


    /**
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param array $keys
     */
    public function setKeys(array $keys): void
    {
        $this->keys = $keys;
    }


    /**
     * @return int
     */
    public function getStartRow(): int
    {
        return $this->start_row;
    }

    /**
     * @param int $start_row
     */
    public function setStartRow(int $start_row): void
    {
        $this->start_row = $start_row;
    }

    /**
     * @return int
     */
    public function getColumnCount(): int
    {
        return $this->column_count;
    }

    /**
     * @param int $column_count
     */
    public function setColumnCount(int $column_count): void
    {
        $this->column_count = $column_count;
    }
}