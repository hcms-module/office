<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:04
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service;

use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;
use App\Application\Office\Service\ExcelFilter\ExcelRowFilter;
use App\Exception\ErrorException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class OfficeExcelService
{
    protected string $file_name = '';
    protected array $header = [];
    protected array $data = [];
    protected int $row_height = 20;
    protected bool $auto_size = true;

    /**
     * ExcelService constructor.
     *
     * @param string $file_name
     * @param array  $header
     * @param array  $data
     */
    public function __construct(string $file_name = '', array $header = [], array $data = [])
    {
        $this->file_name = $file_name;
        $this->header = $header;
        $this->data = $data;
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

    /**
     * 获取默认配置
     *
     * @return array
     */
    private function getStyleArray(): array
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ]
        ];
    }

    /**
     * 打印表头信息
     *
     * @param $spreadsheet
     * @param $sheet
     * @return bool
     * @throws \Throwable
     */
    private function setPrintHeader(Spreadsheet $spreadsheet, $sheet): bool
    {
        $styleArray = $this->getStyleArray();
        $hasHeader = false;
        //打印表头
        if (!empty($this->header)) {
            $hasHeader = true;
            $i = 0;
            foreach ($this->header as $k => $v) {
                $spreadsheet->getActiveSheet()
                    ->getColumnDimension($this->getChar($i))
                    ->setAutoSize($this->auto_size);
                //设置默认高度
                $sheet->getRowDimension(1)
                    ->setRowHeight($this->row_height);
                $cellKey = $this->getChar($i) . '1';
                if (is_array($v)) {
                    throw new ErrorException("header value is can't be array");
                }
                $sheet->setCellValue($cellKey, $v);
                $sheet->getStyle($cellKey)
                    ->applyFromArray(array_merge([
                        'font' => [
                            'bold' => true,
                        ],
                    ], $styleArray));
                $i++;
            }
        }

        return $hasHeader;
    }

    /**
     * @return Spreadsheet
     * @throws \Throwable
     */
    private function setCellValue(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //设置表头
        $hasHeader = $this->setPrintHeader($spreadsheet, $sheet);
        $styleArray = $this->getStyleArray();
        //数据为空
        if (empty($this->data)) {
            throw new ErrorException('data is not empty');
        }
        //有标头第一个从2开始
        $row = $hasHeader ? 2 : 1;
        foreach ($this->data as $key => $value) {
            $i = 0;
            //设置默认高度
            if ($this->row_height > 0) {
                $sheet->getRowDimension($row)
                    ->setRowHeight($this->row_height);
            } else {
                $sheet->getRowDimension($row)
                    ->setZeroHeight(true);
            }

            $mergeRow = 1;
            if ($value instanceof ExcelRowFilter) {
                $mergeRow = $value->getRow();
                $value = $value->getData();
            }
            foreach ($value as $k => $v) {
                $cellKey = $this->getChar($i) . $row;
                $unmergeRow = 1;
                if ($v instanceof AbstractExcelValueFilter) {
                    $unmergeRow = $v->getRow();
                    $v = $v->getFilterValue();
                    //这是记录中多行显示的数据
                    if ($unmergeRow > 1 && is_array($v)) {
                        foreach ($v as $kk => $vv) {
                            $unmergeCellKey = $this->getChar($i) . ($row + $kk);
                            $sheet->setCellValue($unmergeCellKey, $vv);
                        }
                    } elseif (is_array($v)) {
                        $sheet->setCellValue($cellKey, json_encode($v));
                    } else {
                        $sheet->setCellValue($cellKey, $v);
                    }
                } else {
                    if (is_array($v)) {
                        $sheet->setCellValue($cellKey, json_encode($v));
                    } else {
                        $sheet->setCellValue($cellKey, $v);
                    }
                }
                // 如果记录整体占用行数>1,且非分行显示数据
                if ($mergeRow > 1 && $unmergeRow == 1) {
                    //如果行数大于1，则合并行数
                    $mergeCellKey = $this->getChar($i) . ($row + ($mergeRow - 1));
                    $sheet->mergeCells($cellKey . ':' . $mergeCellKey);
                }
                $sheet->getStyle($cellKey)
                    ->applyFromArray($styleArray);
                $i++;
            }
            $row += $mergeRow;
        }

        return $spreadsheet;
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function save(): array
    {
        if (!$this->file_name) {
            //如果没有传文件名称，默认
            $this->file_name = 'export-' . date("YmdHi");
        }
        $save_temp_path = BASE_PATH . "/temp/";
        if (!is_dir($save_temp_path)) {
            mkdir($save_temp_path, 755, true);
        }
        $file_name = $this->file_name . '.xls';

        $file_path = $save_temp_path . $file_name;
        $spreadsheet = $this->setCellValue();
        $writer = new Xls($spreadsheet);
        if (file_exists($save_temp_path . $file_name)) {
            // 已有文件删除覆盖
            unlink($save_temp_path . $file_name);
        }
        $writer->save($save_temp_path . $file_name);

        return [
            'file_name' => $file_name,
            'file_path' => $file_path,
        ];
    }


    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     */
    public function setFileName(string $file_name): void
    {
        $this->file_name = $file_name;
    }


    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header): void
    {
        $this->header = $header;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getRowHeight(): int
    {
        return $this->row_height;
    }

    /**
     * @param int $row_height
     */
    public function setRowHeight(int $row_height): void
    {
        $this->row_height = $row_height;
    }

    /**
     * @return bool
     */
    public function isAutoSize(): bool
    {
        return $this->auto_size;
    }

    /**
     * @param bool $auto_size
     */
    public function setAutoSize(bool $auto_size): void
    {
        $this->auto_size = $auto_size;
    }

}