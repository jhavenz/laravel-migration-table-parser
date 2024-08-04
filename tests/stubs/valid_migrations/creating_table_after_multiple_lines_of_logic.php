<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $builder = Schema::connection('foo');

        $builder->getTables();

        $builder->hasTable('bar');

        $builder
            ->create('users', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('email');
                $table->boolean('is_active');
            });
    }
};
