<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('report_type'); // gdpr, soc2, iso27001, etc.
            $table->string('status'); // pending, in_progress, completed, failed
            $table->json('config');
            $table->json('findings')->nullable();
            $table->text('report_url')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['workspace_id', 'report_type']);
            $table->index(['status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('compliance_reports');
    }
};