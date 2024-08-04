<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->renameColumn('foo', 'bar');
        });

        Schema::connection('foo')->table('users', function (Blueprint $table) {
            $table->renameColumn('foo', 'bar');
        });
    }
};
