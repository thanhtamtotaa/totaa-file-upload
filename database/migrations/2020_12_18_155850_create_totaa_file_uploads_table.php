<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTotaaFileUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('totaa_file_uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('drive', 100);
            $table->longText('local_path')->nullable()->default(null);
            $table->longText('google_drive_virtual_path')->nullable()->default(null);
            $table->longText('google_drive_display_path')->nullable()->default(null);
            $table->longText('google_drive_id')->nullable()->default(null);
            $table->boolean('active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->unsignedBigInteger('deleted_by')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('totaa_file_uploads');
    }
}
