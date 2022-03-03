<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:15
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter;

abstract class AbstractExcelValueFilter
{
    /**
     * @var mixed
     */
    protected $value;

    protected int $row;

    /**
     * ExcelFilter constructor.
     *
     * @param     $value
     * @param int $row
     */
    public function __construct($value, int $row = 1)
    {
        $this->value = $value;
        $this->row = $row;
    }

    /**
     * @return mixed
     */
    abstract public function getFilterValue();

    /**
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }

    /**
     * @param $row
     */
    public function setRow($row): void
    {
        $this->row = $row;
    }
}