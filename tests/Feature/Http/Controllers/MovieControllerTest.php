<?php

it('can list movie 1 - A New Hope', function () {
    $response = $this->get('/movies/1');
    $response->assertStatus(200);
    $response->assertSee('A New Hope');
});
