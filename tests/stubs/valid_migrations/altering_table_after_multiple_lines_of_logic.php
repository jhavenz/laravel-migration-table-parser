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
            ->table('users', function (Blueprint $table) {
                $table->renameColumn('foo', 'bar');
            });
    }
};
