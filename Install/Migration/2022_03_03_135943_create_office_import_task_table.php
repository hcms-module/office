<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateOfficeImportTaskTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('office_import_task', function (Blueprint $table) {
            $table->bigIncrements('task_id');
            $table->string('file_path', 256)
                ->nullable(false)
                ->default('')
                ->comment('导入文件地址');
            $table->string('data_md5', 128)
                ->nullable(false)
                ->default('')
                ->comment('数据摘要');
            $table->tinyInteger('import_status')
                ->nullable(false)
                ->default(0)
                ->comment('导入状态,0失败、1成功、2部分成功');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_import_task');
    }
}
