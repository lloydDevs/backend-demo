<?php

use App\Modules\Product\Models\Product;

test('unauthorized product access renders the shared Inertia error page', function () {
    $viewer = userWithRole('viewer');

    $this->actingAs($viewer)
        ->get('/products/create')
        ->assertForbidden()
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 403)
        );
});

test('a missing product renders the 404 error page', function () {
    $admin = userWithRole('admin');

    $this->actingAs($admin)
        ->get('/products/999999')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 404)
        );
});

test('viewing an existing product succeeds and does not hit the error page', function () {
    $admin = userWithRole('admin');
    $product = Product::factory()->create();

    $this->actingAs($admin)
        ->get("/products/{$product->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Product/Show'));
});