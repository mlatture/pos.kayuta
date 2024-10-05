<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = 'pages';

        if (Schema::hasTable($tableName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'metatitle')) {
                    $table->string('metatitle', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'metadescription')) {
                    $table->string('metadescription', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'canonicalurl')) {
                    $table->string('canonicalurl', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'opengraphimage')) {
                    $table->string('opengraphimage', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'opengraphtitle')) {
                    $table->string('opengraphtitle', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'opengraphdescription')) {
                    $table->string('opengraphdescription', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'schema_code_pasting')) {
                    $table->text('schema_code_pasting')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'title')) {
                    $table->string('title', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'slug')) {
                    $table->string('slug', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'description')) {
                    $table->text('description')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'attachment')) {
                    $table->string('attachment', 100)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'image')) {
                    $table->string('image', 255)->nullable();
                }

                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->tinyInteger('status')->default(1);
                }
            });
        } else {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('metatitle', 255)->nullable();
                $table->string('metadescription', 255)->nullable();
                $table->string('canonicalurl', 255)->nullable();
                $table->string('opengraphimage', 255)->nullable();
                $table->string('opengraphtitle', 255)->nullable();
                $table->string('opengraphdescription', 255)->nullable();
                $table->text('schema_code_pasting')->nullable();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('attachment', 100)->nullable();
                $table->string('image', 255)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
