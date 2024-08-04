<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function down(): void
    {
        Schema::connection('foo')
            ->setConnection(DB::connection('bar'))
            ->dropIfExists('users');
    }
};
