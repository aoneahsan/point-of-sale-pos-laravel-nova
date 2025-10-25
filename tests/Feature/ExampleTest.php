<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    // Root redirects to Nova, which requires authentication
    $response->assertRedirect();
});
