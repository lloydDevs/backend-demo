<?php

use App\Modules\Product\Models\Product;
use App\Modules\Product\Policies\ProductPolicy;

beforeEach(function () {
    $this->policy = new ProductPolicy;
});

test('viewAny allows a user with products.view', function () {
    $user = userWithRole('viewer');

    expect($this->policy->viewAny($user))->toBeTrue();
});

test('viewAny denies a user without products.view', function () {
    seedRolesAndPermissions();
    $user = \App\Models\User::factory()->create();

    expect($this->policy->viewAny($user))->toBeFalse();
});

test('create allows admin and editor but not viewer', function () {
    $admin = userWithRole('admin');
    $editor = userWithRole('editor');
    $viewer = userWithRole('viewer');

    expect($this->policy->create($admin))->toBeTrue()
        ->and($this->policy->create($editor))->toBeTrue()
        ->and($this->policy->create($viewer))->toBeFalse();
});

test('delete only allows admin', function () {
    $admin = userWithRole('admin');
    $editor = userWithRole('editor');
    $product = Product::factory()->make();

    expect($this->policy->delete($admin, $product))->toBeTrue()
        ->and($this->policy->delete($editor, $product))->toBeFalse();
});