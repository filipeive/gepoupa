<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('interest_rates')) {
            Schema::create('interest_rates', function (Blueprint $table) {
                $table->id();
                $table->decimal('rate', 5, 2);
                $table->date('effective_date');
                $table->string('description')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_rates');
    }
};
