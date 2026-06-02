<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RolesSeeder::class);
        $this->call(tb_user::class);
        $this->call(tb_userrole::class);

        $this->call(tb_group::class);
        $this->call(tb_group_shift::class);
        $this->call(tb_cycle::class);

        $this->call(tb_department::class);
        $this->call(tb_position::class);

        $this->call(tb_employee::class);
        $this->call(tb_employee_shift::class);

        $this->call(tb_cutoff::class);
        $this->call(tb_freeday::class);
        $this->call(tb_approval::class);
        $this->call(tb_email::class);
        $this->call(tb_subdept::class);
        $this->call(tb_admin::class);
        $this->call(tb_status::class);
        //$this->call(tb_employee_freeday::class);
        //$this->call(tb_changeday::class);
        //$this->call(tb_overtime::class);
        $this->call(tb_address::class);
        //$this->call(tb_education::class);
        //$this->call(tb_experience::class);
        //$this->call(tb_skill::class);
    }
}
