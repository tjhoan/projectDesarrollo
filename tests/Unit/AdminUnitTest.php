<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;

class AdminUnitTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->artisan('route:clear');
    $this->artisan('route:cache');
  }

  /** @test */
  public function muestra_el_listado_de_productos()
  {
    // Crear algunas categorías y productos
    $category = Category::factory()->create();
    $products = Product::factory(3)->create(['category_id' => $category->id]);

    // Crear un usuario administrador para autenticar
    $admin = Admin::factory()->create()->first();

    // Autenticar al usuario antes de realizar la solicitud usando el guard 'admin'
    $response = $this->actingAs($admin, 'admin')->get(route('products.index'));

    // Verificar que la respuesta sea exitosa (código 200)
    $response->assertStatus(200);

    // Verificar que la vista se ha renderizado correctamente
    $response->assertViewIs('admin.products.index');

    // Verificar que los productos aparecen en la vista
    foreach ($products as $product) {
      $response->assertSee($product->name);
      $response->assertSee($product->price);
      $response->assertSee($product->quantity);
    }
  }

  /** @test */
  public function puede_eliminar_un_producto()
  {
    // Crear un producto
    $product = Product::factory()->create()->first();

    // Crear un usuario administrador
    $admin = Admin::factory()->create()->first();

    // Autenticar al usuario administrador usando el guard 'admin'
    $this->actingAs($admin, 'admin');

    // Hacer una solicitud DELETE para eliminar el producto
    $response = $this->delete(route('admin.products.destroy', $product));

    // Verificar que la respuesta JSON es la esperada
    $response->assertJson(['message' => 'Producto eliminado correctamente']);

    // Verificar que el producto se ha eliminado correctamente de la base de datos
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
  }

  /** @test */
  public function puede_crear_un_producto()
  {
    // Crear una categoría
    $category = Category::factory()->create();

    // Crear un administrador y autenticarlo
    $admin = Admin::factory()->create()->first();
    $this->actingAs($admin, 'admin');

    // Enviar una solicitud POST para crear un producto
    $response = $this->post(route('products.store'), [
      'name' => 'Nuevo Producto',
      'description' => 'Descripción del producto',
      'price' => 100.00,
      'category_id' => $category->id,
      'target_audience' => 'general',
      'brand' => 'Marca',
      'quantity' => 10,
    ]);

    // Verificar que el producto fue creado en la base de datos
    $this->assertDatabaseHas('products', [
      'name' => 'Nuevo Producto',
      'description' => 'Descripción del producto',
      'price' => 100.00,
      'category_id' => $category->id,
      'target_audience' => 'general',
      'brand' => 'Marca',
      'quantity' => 10,
    ]);

    // Verificar que la respuesta redirige al índice de productos
    $response->assertRedirect(route('products.index'));
  }
  /** @test */
  public function test_index_muestra_categorias()
  {
    // Crear un administrador
    $admin = Admin::factory()->create()->first();

    // Crear algunas categorías
    Category::factory()->create(['name' => 'Categoría 1']);
    Category::factory()->create(['name' => 'Categoría 2']);

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Realizar la solicitud
    $response = $this->get(route('categories.index'));

    // Verificar que la respuesta sea exitosa
    $response->assertStatus(200);

    // Verificar que las categorías aparecen en la vista
    $response->assertSee('Categoría 1');
    $response->assertSee('Categoría 2');
  }

  /** @test */
  public function muestra_la_pagina_de_gestion_de_categorias()
  {
    // Crear un administrador
    $admin = Admin::factory()->create()->first();

    // Crear algunas categorías
    $categories = Category::factory(3)->create();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Realizar una solicitud GET a la página de administración de categorías
    $response = $this->get(route('categories.index'));

    // Verificar que la respuesta sea exitosa
    $response->assertStatus(200);

    // Verificar que la vista se ha renderizado correctamente
    $response->assertViewIs('admin.categories.index');

    // Verificar que las categorías aparecen en la vista
    foreach ($categories as $category) {
      $response->assertSee($category->name);
    }
  }

  /** @test */
  public function puede_eliminar_una_categoria()
  {
    // Crear un administrador
    $admin = Admin::factory()->create()->first();

    // Crear una categoría
    $category = Category::factory()->create();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Hacer una solicitud DELETE para eliminar la categoría
    $response = $this->delete(route('categories.destroy', $category));

    // Verificar que la categoría se ha eliminado correctamente
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);

    // Verificar que la respuesta JSON sea correcta
    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
  }

  /** @test */
  public function test_crea_nueva_categoria()
  {
    // Crear un administrador
    $admin = Admin::factory()->create()->first();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    $categoryData = [
      'name' => 'Categoría de prueba'
    ];

    // Enviar la solicitud POST
    $response = $this->post(route('categories.store'), $categoryData);

    // Verificar que la redirección sea correcta
    $response->assertRedirect(route('categories.index'));

    // Verificar que la categoría se ha creado en la base de datos
    $this->assertDatabaseHas('categories', [
      'name' => 'Categoría de prueba',
    ]);
  }

  /** @test */
  public function test_requiere_campo_nombre()
  {
    // Crear un administrador
    $admin = Admin::factory()->create()->first();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Enviar la solicitud sin datos
    $response = $this->post(route('categories.store'), []);

    // Verificar que se generan errores de validación para el campo 'name'
    $response->assertSessionHasErrors(['name']);
  }

  /** @test */
  public function test_crea_nuevo_admin()
  {
    // Crear un administrador para autenticación
    $admin = Admin::factory()->create()->first();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Datos del nuevo administrador
    $adminData = [
      'name' => 'Nuevo Admin',
      'email' => 'admin@example.com',
      'password' => 'secretpassword',
      'password_confirmation' => 'secretpassword',
    ];

    // Enviar solicitud POST
    $response = $this->post(route('admins.store'), $adminData);

    // Verificar redirección
    $response->assertRedirect(route('admins.index'));

    // Verificar que el administrador se haya creado en la base de datos
    $this->assertDatabaseHas('admins', [
      'name' => 'Nuevo Admin',
      'email' => 'admin@example.com',
    ]);
  }

  /** @test */
  public function test_falla_cuando_las_contraseñas_no_coinciden()
  {
    // Crear un administrador para autenticación
    $admin = Admin::factory()->create()->first();

    // Autenticar al administrador
    $this->actingAs($admin, 'admin');

    // Datos con contraseñas no coincidentes
    $adminData = [
      'name' => 'Nuevo Admin',
      'email' => 'admin@example.com',
      'password' => 'secretpassword',
      'password_confirmation' => 'wrongpassword',
    ];

    // Enviar solicitud POST
    $response = $this->post(route('admins.store'), $adminData);

    // Verificar errores en la sesión
    $response->assertSessionHasErrors('password');
  }

  /** @test */
  public function puede_eliminar_un_admin()
  {
    // Crear un administrador para autenticación
    $authAdmin = Admin::factory()->create()->first();

    // Crear el administrador a eliminar
    $admin = Admin::factory()->create();

    // Autenticar al administrador
    $this->actingAs($authAdmin, 'admin');

    // Enviar solicitud DELETE
    $response = $this->delete(route('admins.destroy', $admin->id));

    // Verificar redirección
    $response->assertRedirect(route('admins.index'));

    // Verificar que el administrador se haya eliminado de la base de datos
    $this->assertDatabaseMissing('admins', [
      'id' => $admin->id,
    ]);
  }
}
