<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('foo')->create('users', function (Blueprint $table) {
            $table->renameColumn('foo', 'bar');
        });

        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->table('users', function (Blueprint $table) {
                $table->renameColumn('foo', 'bar');
            });

        Schema::create('other_users', function (Blueprint $table) {
            $table->renameColumn('snap', 'crackle');
        });

        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->table('other_users', function (Blueprint $table) {
                $table->renameColumn('foo', 'bar');
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('other_users');
    }
};
