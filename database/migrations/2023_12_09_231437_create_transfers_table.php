<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->integer("amount");
            $table->string("cod")->default("");

            //id pagador
            $table->foreignUuid('payer_id')
                ->references('id')
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');

            //id beneficiário
            $table->foreignUuid('payee_id')
                ->references('id')
                ->on('users')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->timestamps();
        });

        DB::statement("
            CREATE TRIGGER transf_cod_seq BEFORE INSERT ON transfers
            FOR EACH ROW
            BEGIN
              DECLARE total_items INT;
              SELECT COUNT(*) INTO total_items FROM transfers;
              SET NEW.cod = CONCAT('TRANSF', LPAD(total_items + 1, 4, '0'));
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
