<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('foo', 'bar');
        });
    }

    public function down(): void
    {
        Schema::drop('users');
        
        Schema::dropIfExists('users');
    }
};
