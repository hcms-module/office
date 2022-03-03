<?php

declare (strict_types=1);

namespace App\Application\Office\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $task_id
 * @property string         $file_path
 * @property string         $data_md5
 * @property int            $import_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class OfficeImportTask extends Model
{
    protected $primaryKey = 'task_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'office_import_task';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['file_path', 'data_md5', 'import_status'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'task_id' => 'integer',
        'import_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    //执行导入失败
    const IMPORT_STATUS_FAILED = 0;
    //执行成功
    const IMPORT_STATUS_SUCCESS = 1;
    //部分执行成功
    const IMPORT_STATUS_PART = 2;
}