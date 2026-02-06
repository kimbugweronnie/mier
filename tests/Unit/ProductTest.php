<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\Currency\CurrencyConverterInterface;
use App\Services\Product\ProductService;
use App\Services\Product\ProductServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_product_can_be_created()
    {
        $service = app(ProductServiceInterface::class);
        $product = $service->create([
            'name' => 'Laptop',
            'price' => 2500,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_update()
    {
        $product = Product::factory()->create();
        $service = app(ProductService::class);
        $service->update($product->id, [
            'name' => 'Updated name',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated name',
        ]);
    }

    // Override binding in a test
    public function test_price_conversion()
    {
        $this->app->bind(
            CurrencyConverterInterface::class,
            FakeCurrencyConverter::class
        );
        // OR BETTER
        $this->app->when(ProductService::class)
            ->needs(CurrencyConverterInterface::class)
            ->give(FakeCurrencyConverter::class);

        $service = app(ProductService::class);
        $price = $service->getPriceInCurrency(1, 'UGX');
        $this->assertEquals(1000, $price);
    }

    public function test_product_can_be_deleted()
    {
        $product = Product::factory()->create();
        $service = app(ProductServiceInterface::class);
        $service->delete($product->id);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_can_be_retrieved()
    {
        $product = Product::factory()->create();
        $service = app(ProductServiceInterface::class);
        $retrievedProduct = $service->find($product->id);
        $this->assertEquals($product->id, $retrievedProduct->id);
    }

    public function test_all_products_can_be_retrieved()
    {
        Product::factory()->count(5)->create();
        $service = app(ProductServiceInterface::class);
        $products = $service->all();
        $this->assertCount(5, $products);
    }

    public function test_product_cache_is_cleared_on_update()
    {
        $product = Product::factory()->create();
        $service = app(ProductServiceInterface::class);
        $service->update($product->id, [
            'name' => 'Updated name',
        ]);
        // Assuming the cache is tagged with 'products' and "tenant:{$product->tenant_id}"
        $cacheKey = "tenant:{$product->tenant_id}:products:{$product->id}";
        $this->assertFalse(cache()->has($cacheKey));
    }

    public function test_product_cache_is_cleared_on_delete()
    {
        $product = Product::factory()->create();
        $service = app(ProductServiceInterface::class);
        $service->delete($product->id);
        // Assuming the cache is tagged with 'products' and "tenant:{$product->tenant_id}"
        $cacheKey = "tenant:{$product->tenant_id}:products:{$product->id}";
        $this->assertFalse(cache()->has($cacheKey));
    }

    public function test_product_cache_is_cleared_on_create()
    {
        $service = app(ProductServiceInterface::class);
        $product = $service->create([
            'name' => 'New Product',
            'price' => 100,
        ]);
        // Assuming the cache is tagged with 'products' and "tenant:{$product->tenant_id}"
        $cacheKey = "tenant:{$product->tenant_id}:products:{$product->id}";
        $this->assertFalse(cache()->has($cacheKey));
    }

    public function test_check_it()
    {
        Queue::fake();

        event(new UpdateProduct($product));

        Queue::assertPushed(SyncWarehouseStock::class);

    }
}
