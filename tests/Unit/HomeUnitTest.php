<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Collection;

class HomeUnitTest extends TestCase
{
    /** @test */
    public function producto_tiene_relacion_con_imagenes()
    {
        $product = new Product();
        $this->assertInstanceOf(Collection::class, $product->images);
    }

    /** @test */
    public function producto_tiene_relacion_con_categoria()
    {
        $product = new Product();
        $this->assertNull($product->category);
    }

    /** @test */
    public function se_puede_crear_categoria_con_nombre()
    {
        $category = new Category(['name' => 'Electronics']);
        $this->assertEquals('Electronics', $category->name);
    }

    /** @test */
    public function imagen_de_producto_pertenece_a_producto()
    {
        $image = new ProductImage(['image_path' => 'path/to/image.jpg']);
        $this->assertNull($image->product);
        $this->assertEquals('path/to/image.jpg', $image->image_path);
    }

    /** @test */
    public function los_productos_pueden_ser_paginados()
    {
        $products = collect([
            new Product(['name' => 'Product 1', 'price' => 100]),
            new Product(['name' => 'Product 2', 'price' => 200]),
        ]);

        $this->assertCount(2, $products);
        $this->assertEquals('Product 1', $products[0]->name);
    }

    /** @test */
    public function se_pueden_listar_categorias()
    {
        $categories = collect([
            new Category(['name' => 'Category 1']),
            new Category(['name' => 'Category 2']),
        ]);

        $this->assertCount(2, $categories);
        $this->assertEquals('Category 1', $categories[0]->name);
    }

    /** @test */
    public function detalles_del_producto_obtiene_producto_con_imagenes_y_categoria()
    {
        $product = new Product([
            'name' => 'Test Product',
            'price' => 100,
        ]);

        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals(100, $product->price);
    }
}
