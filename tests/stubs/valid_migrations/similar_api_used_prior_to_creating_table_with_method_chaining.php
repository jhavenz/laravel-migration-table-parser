<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Traits\Conditionable;

return new class extends Migration
{
    use Conditionable;

    public function up(): void
    {
        $this->when('foo is bar', function () {
            //
        });

        Schema::connection('foo')
            ->create('users', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('email');
                $table->boolean('is_active');
            });
    }
};
