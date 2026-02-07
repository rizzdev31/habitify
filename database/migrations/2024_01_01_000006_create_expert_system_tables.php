<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expert System Executions (Prevent Duplicate)
        Schema::create('expert_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->foreignId('report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rule_kode', 10);
            $table->string('diagnosis_kode', 10);
            $table->json('matched_conditions'); // Array of conditions that matched
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->text('counseling_notes')->nullable();
            $table->text('follow_up_notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->boolean('wali_notified')->default(false);
            $table->timestamp('wali_notified_at')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'rule_kode', 'report_id']);
            $table->index(['status', 'created_at']);
            $table->index(['santri_id', 'diagnosis_kode']);
        });

        // Konsekuensi Executions (When threshold reached)
        Schema::create('konsekuensi_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->string('konsekuensi_kode', 10);
            $table->integer('poin_saat_trigger');
            $table->text('tindakan');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('wali_notified')->default(false);
            $table->timestamps();

            $table->index(['santri_id', 'status']);
        });

        // Reward Executions (When threshold reached)
        Schema::create('reward_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri_profiles')->cascadeOnDelete();
            $table->string('reward_kode', 10);
            $table->integer('poin_saat_trigger');
            $table->text('reward');
            $table->enum('status', ['pending', 'given'])->default('pending');
            $table->foreignId('given_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('given_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['santri_id', 'status']);
        });

        // Notifications Log
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('santri_id')->nullable()->constrained('santri_profiles')->nullOnDelete();
            $table->string('type', 50); // whatsapp, email, push, database
            $table->string('channel', 50); // report_approved, diagnosis_new, etc.
            $table->string('recipient', 100); // Phone number or email
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['santri_id', 'created_at']);
        });

        // Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50); // create, update, delete, login, etc.
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications_log');
        Schema::dropIfExists('reward_executions');
        Schema::dropIfExists('konsekuensi_executions');
        Schema::dropIfExists('expert_executions');
    }
};