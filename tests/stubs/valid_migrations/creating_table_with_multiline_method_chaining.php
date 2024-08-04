<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->create('users', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('email');
                $table->boolean('is_active');
            });
    }
};
