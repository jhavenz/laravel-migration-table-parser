<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function down(): void
    {
        $builder = Schema::connection('foo');

        $builder->getTables();

        $builder->hasTable('bar');

        $builder->dropIfExists('users');
    }
};
