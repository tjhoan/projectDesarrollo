<?php

namespace Tests\Integration;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductIntegrationTest extends TestCase
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
    public function test_admin_puede_ver_productos()
    {
        // Crear una categoría y algunos productos de ejemplo
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        // Simular la solicitud a la ruta de administración de productos
        $response = $this->get(route('products.index'));

        // Verificar que la vista carga correctamente con los productos
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /** @test */
    public function test_admin_puede_crear_un_producto()
    {
        // Crear una categoría de ejemplo
        $category = Category::factory()->create();

        // Simular la creación de un nuevo producto
        $response = $this->post(route('products.store'), [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del producto',
            'price' => 100,
            'quantity' => 10,
            'brand' => 'Marca Ejemplo',
            'category_id' => $category->id,
            'target_audience' => 'general',
        ]);

        // Verificar que el producto fue creado en la base de datos
        $this->assertDatabaseHas('products', [
            'name' => 'Nuevo Producto',
            'price' => 100,
        ]);
    }

    /** @test */
    public function test_admin_puede_eliminar_un_producto()
    {
        // Crear una categoría y un producto de ejemplo
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        // Simular la eliminación del producto
        $response = $this->delete(route('products.destroy', $product->id));

        // Verificar que el producto fue eliminado de la base de datos
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);

        // Verificar que la respuesta JSON indica éxito
        $response->assertJson([
            'message' => 'Producto eliminado correctamente',
        ]);
    }
}
