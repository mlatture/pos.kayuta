<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       // 2025_01_01_000005_create_media_uploads_table.php

Schema::create('media_uploads', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id')->index();
    $table->unsignedBigInteger('uploaded_by')->nullable(); // user_id
    $table->string('path');            // S3 key
    $table->string('disk')->default('s3');
    $table->string('mime_type')->nullable();
    $table->json('tags')->nullable();  // ['event:pool-party','kids','weekend']
    $table->json('meta')->nullable();  // EXIF cleaned info if needed
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_uploads');
    }
};
