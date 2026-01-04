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
        Schema::create('analytics_progress', function (Blueprint $table) {
            $table->id();

            $table->date('day');

            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->integer('planned')->unsigned()->default(0);
            $table->integer('checked_planned')->unsigned()->default(0);
            $table->integer('checked_unplanned')->unsigned()->default(0);
            $table->decimal('total_progress', 8, 2)->unsigned()->default(0.00);
            $table->decimal('deviation', 8, 2)->unsigned()->default(0.00);
            $table->integer('median_time')->unsigned()->default(0);
            $table->integer('work_time')->unsigned()->default(0);
            $table->integer('dispersion')->unsigned()->default(0);
            $table->integer('concentration')->unsigned()->default(0);

            $table->unique(['day', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_progress');
    }
};
