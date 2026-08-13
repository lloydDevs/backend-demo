<?php

test('the application redirects the root to the login or dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/dashboard');
});