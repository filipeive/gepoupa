<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('interest_distributions')) {
            Schema::create('interest_distributions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cycle_id'); // Foreign key constraint added later if cycle table exists or created here if order permits. Assuming cycle_id refers to saving_cycles.
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->date('distribution_date');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_distributions');
    }
};
