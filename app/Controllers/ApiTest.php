<?php

namespace App\Controllers;

class ApiTest extends BaseController
{
    public function index()
    {
        return "API Test Root Success";
    }

    public function sub()
    {
        return "API Test Sub Success";
    }
}
