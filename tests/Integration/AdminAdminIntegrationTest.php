<?php

namespace Tests\Integration;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAdminIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear y autenticar un administrador
        $admin = Admin::factory()->create()->first();
        $this->actingAs($admin, 'admin'); // Simular autenticación como administrador
    }

    /** @test */
    public function test_admin_puede_ver_administradores()
    {
        // Crear algunos administradores de ejemplo
        Admin::factory()->count(3)->create();

        // Simular la solicitud a la ruta de administración de administradores
        $response = $this->get(route('admins.index'));

        // Verificar que la vista carga correctamente con los administradoresD
        $response->assertStatus(200);
        $response->assertViewHas('admins');
    }

    /** @test */
    public function test_admin_puede_crear_un_administrador()
    {
        // Simular la creación de un nuevo administrador
        $response = $this->post(route('admins.store'), [
            'name' => 'Nuevo Administrador',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Verificar que el administrador fue creado en la base de datos
        $this->assertDatabaseHas('admins', [
            'email' => 'admin@example.com',
        ]);
    }

    /** @test */
    public function test_admin_puede_eliminar_un_administrador()
    {
        // Crear un administrador de ejemplo
        $admin = Admin::factory()->create();

        // Simular la eliminación del administrador
        $response = $this->delete(route('admins.destroy', $admin->id));

        // Verificar que el administrador fue eliminado de la base de datos
        $this->assertDatabaseMissing('admins', [
            'id' => $admin->id,
        ]);

        // Verificar que la respuesta redirige correctamente y tiene un mensaje de éxito
        $response->assertRedirect(route('admins.index'));
        $response->assertSessionHas('success', 'Administrador eliminado con éxito');
    }
}
