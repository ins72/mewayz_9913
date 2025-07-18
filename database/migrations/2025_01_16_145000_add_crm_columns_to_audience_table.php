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
        Schema::table('audience', function (Blueprint $table) {
            if (!Schema::hasColumn('audience', 'workspace_id')) {
                $table->bigInteger('workspace_id')->nullable()->after('user_id');
        
            if (!Schema::hasColumn('audience', 'email')) {
                $table->string('email')->nullable()->after('name');
        
            if (!Schema::hasColumn('audience', 'phone')) {
                $table->string('phone')->nullable()->after('email');
        
            if (!Schema::hasColumn('audience', 'company')) {
                $table->string('company')->nullable()->after('phone');
        
            if (!Schema::hasColumn('audience', 'position')) {
                $table->string('position')->nullable()->after('company');
        
            if (!Schema::hasColumn('audience', 'type')) {
                $table->string('type')->default('contact')->after('position');
        
            if (!Schema::hasColumn('audience', 'status')) {
                $table->string('status')->default('active')->after('type');
        
            if (!Schema::hasColumn('audience', 'source')) {
                $table->string('source')->nullable()->after('status');
        
            if (!Schema::hasColumn('audience', 'notes')) {
                $table->text('notes')->nullable()->after('source');
        
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audience', function (Blueprint $table) {
            $table->dropColumn([
                'workspace_id', 'email', 'phone', 'company', 'position', 
                'type', 'status', 'source', 'notes'
            ]);
        });

};