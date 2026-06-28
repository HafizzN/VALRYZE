<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected $hrd;
    protected $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        Role::create(['name' => 'hrd']);
        Role::create(['name' => 'karyawan']);

        // Create users
        $this->hrd = User::factory()->create();
        $this->hrd->assignRole('hrd');

        $this->karyawan = User::factory()->create([
            'basic_salary' => 5000000,
            'allowance' => 1000000,
            'bpjs_deduction' => 150000,
            'tax_deduction' => 50000,
            'api_token' => 'karyawan-api-token',
        ]);
        $this->karyawan->assignRole('karyawan');
    }

    /**
     * Test HRD can update user's salary details.
     */
    public function test_hrd_can_update_user_salary_settings(): void
    {
        $response = $this->actingAs($this->hrd)->put("/payroll/{$this->karyawan->id}", [
            'basic_salary' => 6000000,
            'allowance' => 1200000,
            'bpjs_deduction' => 180000,
            'tax_deduction' => 60000,
        ]);

        $response->assertRedirect('/payroll');
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $this->karyawan->id,
            'basic_salary' => 6000000,
            'allowance' => 1200000,
            'bpjs_deduction' => 180000,
            'tax_deduction' => 60000,
        ]);
    }

    /**
     * Test payroll parameters numeric constraints.
     */
    public function test_payroll_validations(): void
    {
        $response = $this->actingAs($this->hrd)->put("/payroll/{$this->karyawan->id}", [
            'basic_salary' => -1000, // Invalid negative value
        ]);

        $response->assertSessionHasErrors(['basic_salary']);
    }
}
