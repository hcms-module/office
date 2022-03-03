<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 13:51
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service\ExcelFilter;

class ExcelColumnFilter
{
    public string $key = '';
    public string $filter = '';

    /**
     * ExcelColumnFilter constructor.
     *
     * @param string $key
     * @param string $filter
     */
    public function __construct(string $key, string $filter = '')
    {
        $this->key = $key;
        $this->filter = $filter;
    }
}