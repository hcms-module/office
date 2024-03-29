<?php
declare(strict_types=1);
// 菜单层级最多三级
//[
//    [
//        'parent_access_id' => 0,
//        'access_name' => '示例',
//        'uri' => 'demo/demo/none',
//        'params' => '',
//        'sort' => 100,
//        'is_menu' => 1,
//        'menu_icon' => 'el-icon-data-analysis',
//        'children' => []
//    ]
//]
return [
    [
        'parent_access_id' => 0,
        'access_name' => '文档操作',
        'uri' => 'office/office',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'line-icon-wenjian3',
        'children' => [
            [
                'access_name' => '操作示例',
                'uri' => 'office/office',
                'params' => '',
                'sort' => 100,
                'is_menu' => 1,
            ],
            [
                'access_name' => '导入任务',
                'uri' => 'office/office/task',
                'params' => '',
                'sort' => 100,
                'is_menu' => 1,
            ]
        ]
    ]
];