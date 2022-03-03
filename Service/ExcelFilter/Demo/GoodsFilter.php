<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:22
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter\Demo;

use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;

class GoodsFilter extends AbstractExcelValueFilter
{
    public function getFilterValue()
    {
        return $this->value;
    }
}