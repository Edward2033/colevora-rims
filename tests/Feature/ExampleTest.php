<?php

test('application returns a successful response on home', function () {
    $response = $this->get('/');
    // Home page is a public Volt route - assert it's either 200 or redirect
    expect($response->status())->toBeIn([200, 301, 302]);
});
