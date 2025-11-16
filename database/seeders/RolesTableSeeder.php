<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("roles")->insert([
            "role_name" => "Admin",
            "role_slug" => "admin",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);

        DB::table("roles")->insert([
            "role_name" => "User",
            "role_slug" => "user",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);
    }
}
