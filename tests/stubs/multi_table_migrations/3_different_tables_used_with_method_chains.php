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

        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->table('users', function (Blueprint $table) {
                $table->renameColumn('foo', 'bar');
            });

        Schema::create('some_other_users', function (Blueprint $table) {
            $table->renameColumn('snap', 'crackle');
        });

        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->table('some_other_users', function (Blueprint $table) {
                $table->renameColumn('foo', 'bar');
            });
    }

    public function down(): void
    {
        Schema::drop('other_users');

        Schema::dropIfExists('some_other_users');
    }
};
