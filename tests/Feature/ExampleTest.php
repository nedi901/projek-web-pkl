<?php

test('halaman awal mengarah ke halaman login', function () {
    $this->get('/')->assertRedirect(route('login'));
});
