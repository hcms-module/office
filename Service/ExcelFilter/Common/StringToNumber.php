<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 11:40
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter\Common;

use App\Application\Office\Service\ExcelFilter\AbstractExcelValueFilter;

class StringToNumber extends AbstractExcelValueFilter
{
    public function getFilterValue()
    {
        if (is_string($this->value)) {
            $res = floatval($this->value);

            return $res ?: 0;
        } else {
            return 0;
        }
    }
}