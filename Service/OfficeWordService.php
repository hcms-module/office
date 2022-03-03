<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/3 16:15
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Office\Service;

use PhpOffice\PhpWord\TemplateProcessor;

class OfficeWordService
{
    function replace($template_file_path, $data, string $file_name = ''): array
    {
        $template_document = new TemplateProcessor($template_file_path);
        foreach ($data as $key => $value) {
            $template_document->setValue($key, $value);
        }
        $save_temp_path = BASE_PATH . '/runtime/temp/';
        if (!is_dir($save_temp_path)) {
            mkdir($save_temp_path, 755, true);
        }
        if (!$file_name) {
            $explode_array = explode('.', $template_file_path);
            $file_ext = $explode_array[count($explode_array) - 1] ?? 'doc';
            $file_name = 'word-' . date('YmdHi') . '.' . $file_ext;
        }
        $file_path = $save_temp_path . $file_name;
        $template_document->saveAs($file_path);

        return [
            'file_path' => $file_path
        ];
    }
}