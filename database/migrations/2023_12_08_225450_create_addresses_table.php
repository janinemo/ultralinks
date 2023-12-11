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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid("id")->primary();

            // User Input
            $table->string("zip_code");
            $table->string("complement")->default("");
            $table->integer("number");

            // API integration
            $table->string("street")->nullable();
            $table->string("neighborhood")->nullable();
            $table->string("city")->nullable();
            $table->string("uf")->nullable();

            $table->boolean("is_complete")->default(false);

            // User relationship
            $table->foreignUuid('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
