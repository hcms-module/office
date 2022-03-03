<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:23
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter\Common;

use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;

class DateFilter extends AbstractExcelValueFilter
{
    public function getFilterValue()
    {
        if (is_numeric($this->value)) {
            return date("Y-m-d H:i:s", $this->value);
        } else {
            return date("Y-m-d H:i:s", 0);
        }
    }
}