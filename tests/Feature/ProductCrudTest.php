<?php

use App\Modules\Product\Models\Product;

test('guests are redirected to login', function () {
    $this->get('/products')->assertRedirect('/login');
});

test('admin can view the product index', function () {
    $admin = userWithRole('admin');
    Product::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get('/products')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Product/Index')
            ->has('products.data', 3)
        );
});

test('viewer cannot see the create button data but can view the index', function () {
    $viewer = userWithRole('viewer');

    $this->actingAs($viewer)
        ->get('/products')
        ->assertOk();
});

test('viewer is forbidden from the create page', function () {
    $viewer = userWithRole('viewer');

    $this->actingAs($viewer)
        ->get('/products/create')
        ->assertForbidden();
});

test('viewer is forbidden from creating a product', function () {
    $viewer = userWithRole('viewer');

    $this->actingAs($viewer)
        ->post('/products', [
            'name' => 'Test Product',
            'sku' => 'TP-001',
            'price' => 10,
            'stock' => 5,
        ])
        ->assertForbidden();

    expect(Product::count())->toBe(0);
});

test('editor can create a product', function () {
    $editor = userWithRole('editor');

    $this->actingAs($editor)
        ->post('/products', [
            'name' => 'Wireless Mouse',
            'sku' => 'WM-100',
            'description' => 'A mouse without wires.',
            'price' => 29.99,
            'stock' => 50,
            'is_active' => true,
        ])
        ->assertRedirect('/products');

    $this->assertDatabaseHas('products', [
        'name' => 'Wireless Mouse',
        'sku' => 'WM-100',
    ]);
});

test('editor cannot delete a product', function () {
    $editor = userWithRole('editor');
    $product = Product::factory()->create();

    $this->actingAs($editor)
        ->delete("/products/{$product->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('products', ['id' => $product->id]);
});

test('admin can update a product', function () {
    $admin = userWithRole('admin');
    $product = Product::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin)
        ->put("/products/{$product->id}", [
            'name' => 'New Name',
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => $product->stock,
            'is_active' => true,
        ])
        ->assertRedirect('/products');

    expect($product->fresh()->name)->toBe('New Name');
});

test('admin can delete a product', function () {
    $admin = userWithRole('admin');
    $product = Product::factory()->create();

    $this->actingAs($admin)
        ->delete("/products/{$product->id}")
        ->assertRedirect('/products');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('creating a product requires name, sku, price and stock', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)
        ->post('/products', [])
        ->assertSessionHasErrors(['name', 'sku', 'price', 'stock']);

    expect(Product::count())->toBe(0);
});

test('creating a product requires a unique sku', function () {
    $admin = userWithRole('admin');
    Product::factory()->create(['sku' => 'DUPLICATE-SKU']);

    $this->actingAs($admin)
        ->post('/products', [
            'name' => 'Another Product',
            'sku' => 'DUPLICATE-SKU',
            'price' => 10,
            'stock' => 1,
        ])
        ->assertSessionHasErrors('sku');

    expect(Product::count())->toBe(1);
});

test('updating a product allows keeping its own sku', function () {
    $admin = userWithRole('admin');
    $product = Product::factory()->create(['sku' => 'KEEP-ME']);

    $this->actingAs($admin)
        ->put("/products/{$product->id}", [
            'name' => 'Updated Name',
            'sku' => 'KEEP-ME',
            'price' => $product->price,
            'stock' => $product->stock,
        ])
        ->assertSessionDoesntHaveErrors('sku')
        ->assertRedirect('/products');
});

test('price and stock must be non-negative', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)
        ->post('/products', [
            'name' => 'Bad Numbers',
            'sku' => 'BAD-001',
            'price' => -5,
            'stock' => -1,
        ])
        ->assertSessionHasErrors(['price', 'stock']);
});