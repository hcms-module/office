<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:39
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter\Common;

use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;

class TrimFilter extends AbstractExcelValueFilter
{
    public function getFilterValue()
    {
        $value = trim($this->value);

        return str_replace(["/r/n", "/r", "/n"], "", $value);
    }
}