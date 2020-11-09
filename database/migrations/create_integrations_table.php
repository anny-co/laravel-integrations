<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('integrations', function (Blueprint $table) {
		    $table->id();
		    $table->uuid('uuid')->unique();
		    $table->string('name');
		    $table->string('key');
		    $table->morphs('model');
		    $table->string('version')->default('v1.0');
		    $table->boolean('active')->default(false);
		    $table->json('settings');
		    $table->text('error')->nullable();
		    $table->json('error_details')->nullable();
		    $table->softDeletes();
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
        Schema::dropIfExists('integrations');
    }
}
