<?php

namespace Tests\Integration;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
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
    public function test_admin_puede_ver_categorias()
    {
        // Crear algunas categorías de ejemplo
        Category::factory()->count(3)->create();

        // Simular la solicitud a la ruta de administración de categorías
        $response = $this->get(route('categories.index'));

        // Verificar que la vista carga correctamente con las categorías
        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    /** @test */
    public function test_admin_puede_crear_una_categoria()
    {
        // Simular la creación de una nueva categoría
        $response = $this->post(route('categories.store'), [
            'name' => 'Nueva Categoría',
        ]);

        // Verificar que la categoría fue creada en la base de datos
        $this->assertDatabaseHas('categories', [
            'name' => 'Nueva Categoría',
        ]);

        // Verificar que el usuario es redirigido a la página de categorías con éxito
        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success', 'Categoría creada con éxito');
    }

    /** @test */
    public function test_admin_puede_eliminar_una_categoria()
    {
        // Crear una categoría de ejemplo
        $category = Category::factory()->create();

        // Simular la eliminación de la categoría
        $response = $this->delete(route('categories.destroy', $category->id));

        // Verificar que la categoría fue eliminada de la base de datos
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);

        // Verificar que la respuesta JSON indica éxito
        $response->assertJson([
            'success' => true,
        ]);
    }
}
