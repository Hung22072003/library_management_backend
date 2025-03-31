<?php

namespace App\Http\Controllers;


class ControllerWithGuard extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
