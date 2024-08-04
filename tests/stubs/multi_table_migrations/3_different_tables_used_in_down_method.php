<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function down(): void
    {
        Schema::dropIfExists('users');

        Schema::drop('other_users');

        Schema::dropIfExists('some_other_users');
    }
};
