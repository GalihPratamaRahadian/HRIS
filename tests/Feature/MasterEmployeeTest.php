<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MasterEmployeeTest extends TestCase
{

	public function testDepartment()
	{
		$this->withoutMiddleware(VerifyCsrfToken::class);
		$user = \App\User::where('role', \App\User::ROLE_ADMIN)->first();

		$response = $this->actingAs($user)
					 ->post(route('department.create'), [
						 'department_name'   => 'Wow',
					 ], \Setting::ajaxTest());

		$response->assertStatus(200);
	}
}
