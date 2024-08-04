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
        Schema::dropIfExists('some_other_users');
    }
};
