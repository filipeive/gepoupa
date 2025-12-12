<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saving_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('saving_cycles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_saved', 10, 2);
            $table->date('distribution_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_distributions');
    }
};
