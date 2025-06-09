<?php

it('redirects from / to /login', function () {
    $response = $this->get('/');
    $response->assertRedirect('/login');
});
