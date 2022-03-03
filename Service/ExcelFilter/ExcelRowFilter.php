<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:14
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter;

class ExcelRowFilter
{
    protected array $data = [];

    protected int $row = 1;

    /**
     * ExcelRowFilter constructor.
     *
     * @param array $data
     * @param int   $row
     */
    public function __construct(array $data, int $row = 1)
    {
        $this->data = $data;
        $this->row = $row;
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
    public function getRow()
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