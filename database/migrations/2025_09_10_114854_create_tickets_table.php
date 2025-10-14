<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('It\'s consider as a short_description');
            $table->text('description')->nullable();
            $table->enum('syncing_status',['Synced','Not Synced'])->default('Not Synced')->comment('When synced with third party');
            $table->string('status')->default('New')->comment('It\'s third partie\'s state or status'); 
            $table->string('service_now_id')->nullable()->comment('ServiceNow tickets number / ticket id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
