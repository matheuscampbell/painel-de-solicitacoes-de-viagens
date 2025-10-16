<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
        });

        DB::table('users')->whereNull('uuid')->select('id')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                DB::table('users')->where('id', $user->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE users ALTER COLUMN uuid SET NOT NULL');
        }

        Schema::table('travel_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_orders', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
        });

        DB::table('travel_orders')->whereNull('uuid')->select('id')->orderBy('id')->chunkById(100, function ($orders) {
            foreach ($orders as $order) {
                DB::table('travel_orders')->where('id', $order->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE travel_orders ALTER COLUMN uuid SET NOT NULL');
        }

        Schema::table('travel_order_status_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('travel_order_status_histories', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            }
        });

        DB::table('travel_order_status_histories')->whereNull('uuid')->select('id')->orderBy('id')->chunkById(100, function ($histories) {
            foreach ($histories as $history) {
                DB::table('travel_order_status_histories')->where('id', $history->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE travel_order_status_histories ALTER COLUMN uuid SET NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::table('travel_order_status_histories', function (Blueprint $table) {
            if (Schema::hasColumn('travel_order_status_histories', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });

        Schema::table('travel_orders', function (Blueprint $table) {
            if (Schema::hasColumn('travel_orders', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
