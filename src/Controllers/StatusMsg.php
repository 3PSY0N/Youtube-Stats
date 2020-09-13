<?php

namespace App\Controllers;

class StatusMsg
{
    public function show($color, $message)
    {
        return '<div class="alert alert-' . $color . '" role="alert">' . $message . '</div>';
    }
}