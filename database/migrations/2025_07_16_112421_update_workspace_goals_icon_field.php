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
        Schema::table('workspace_goals', function (Blueprint $table) {
            $table->text('icon')->change(); // Change from string to text
        });
        
        Schema::table('features', function (Blueprint $table) {
            $table->text('icon')->change(); // Change from string to text
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspace_goals', function (Blueprint $table) {
            $table->string('icon')->change();
        });
        
        Schema::table('features', function (Blueprint $table) {
            $table->string('icon')->change();
        });

}
};
