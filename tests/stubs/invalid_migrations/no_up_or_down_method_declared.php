<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function foo(): void
    {
        DB::connection('foo')->statement(<<<'SQL'
        CREATE VIEW users as SELECT * FROM users
        SQL);
    }
};
