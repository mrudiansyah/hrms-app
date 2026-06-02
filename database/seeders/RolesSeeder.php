<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert(['name' => 'dashboard']);
        DB::table('roles')->insert(['name' => 'admin_shift']);
        DB::table('roles')->insert(['name' => 'admin_employee']);
        DB::table('roles')->insert(['name' => 'admin_calendar']);
        DB::table('roles')->insert(['name' => 'admin_checktime']);
        DB::table('roles')->insert(['name' => 'admin_department']);
        DB::table('roles')->insert(['name' => 'spl_create']);
        DB::table('roles')->insert(['name' => 'spl_sign']);
        DB::table('roles')->insert(['name' => 'spl_approval']);
        DB::table('roles')->insert(['name' => 'spl_verification']);
        DB::table('roles')->insert(['name' => 'info_employee']);
        DB::table('roles')->insert(['name' => 'info_overtime']);
        DB::table('roles')->insert(['name' => 'role']);
        DB::table('roles')->insert(['name' => 'contract']);
        DB::table('roles')->insert(['name' => 'changeday']);
        DB::table('roles')->insert(['name' => 'leave']);
    }
}
