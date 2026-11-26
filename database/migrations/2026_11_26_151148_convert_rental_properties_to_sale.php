<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('properties')
            ->where('status', 'Сдается')
            ->update(['status' => 'Продается']);
    }

    public function down(): void {}
};
