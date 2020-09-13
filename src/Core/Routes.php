<?php
use \NoahBuscher\Macaw\Macaw;

Macaw::get('/', 'App\Controllers\Youtube@index');
Macaw::post('/', 'App\Controllers\Youtube@index');