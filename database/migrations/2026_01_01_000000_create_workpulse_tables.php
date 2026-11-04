<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * WorkPulse - Clinical-grade Work Culture & Burnout Risk Assessment Schema
     */
    public function up(): void
    {
        // 1. Users Table (Startup Founders & Staff)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('startup_name');
            $table->string('lokasi', 100)->nullable();
            $table->string('sektor_teknologi')->nullable(); // EdTech, SaaS, FinTech, HealthTech, dsb.
            $table->string('pendanaan')->nullable();        // Bootstrapped, Pre-Seed, Seed, Series A, dsb.
            $table->enum('role', ['clevel', 'staf'])->default('staf');
            $table->rememberToken();
            $table->timestamps();

            $table->index('startup_name');
            $table->index('role');
        });

        // 2. Factor Definitions Table (6 Pilar Kesehatan Organisasi)
        Schema::create('factor_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('role_target', ['clevel', 'staf', 'both'])->default('both');
            $table->decimal('weight', 4, 2)->default(1.00);
            $table->integer('order_num')->default(1);
            $table->timestamps();
        });

        // 3. Question Definitions Table (Indikator Budaya Kerja & Burnout)
        Schema::create('question_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('factor_code', 10);
            $table->enum('role_target', ['clevel', 'staf']);
            $table->integer('question_index');
            $table->text('text');
            $table->decimal('weight', 4, 2)->default(1.00);
            $table->timestamps();

            $table->foreign('factor_code')
                  ->references('code')
                  ->on('factor_definitions')
                  ->onDelete('cascade');
            $table->index(['factor_code', 'role_target']);
        });

        // 4. Submissions Table (Hasil Asesmen)
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['clevel', 'staf']);
            
            // 6 Faktor Skor (1.00 - 5.00)
            $table->decimal('score_vt', 5, 2)->nullable(); // Visi & Transparansi
            $table->decimal('score_kk', 5, 2)->nullable(); // Kualitas Kepemimpinan
            $table->decimal('score_fi', 5, 2)->nullable(); // Fasilitas & Infrastruktur
            $table->decimal('score_bk', 5, 2)->nullable(); // Beban Kerja (Workload)
            $table->decimal('score_km', 5, 2)->nullable(); // Kesejahteraan Mental
            $table->decimal('score_kp', 5, 2)->nullable(); // Kompensasi & Karir
            
            $table->decimal('final_score', 5, 2);
            $table->string('burnout_status', 60); // Budaya Kerja Positif, Kondisi Stabil, Waspada Gejala Burnout, Risiko Burnout Tinggi
            $table->json('raw_scores')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();

            $table->index('user_id');
            $table->index('burnout_status');
            $table->index('submitted_at');
        });

        // 5. Answers Table (Detail Jawaban Tiap Indikator)
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
            $table->string('factor_key', 10);
            $table->integer('question_index');
            $table->unsignedTinyInteger('score'); // Skala Likert 1-5
            $table->timestamps();

            $table->index(['submission_id', 'factor_key']);
        });

        // 6. Validation / Audit Log Table
        Schema::create('validation_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('status', 50);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_log');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('question_definitions');
        Schema::dropIfExists('factor_definitions');
        Schema::dropIfExists('users');
    }
};

