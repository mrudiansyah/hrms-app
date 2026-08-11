<?php

namespace Tests\Feature;

use Tests\TestCase;

class HcmisEmployeeViewTest extends TestCase
{
    public function test_hcmis_employee_page_can_be_rendered()
    {
        $response = $this->get('/hcmis/employees');

        $response->assertStatus(200);
        $response->assertSee('HCMIS Employees');
    }
}
