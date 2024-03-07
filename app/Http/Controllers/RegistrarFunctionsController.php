<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegistrarFunctionsController extends Controller
{
    public function index()
    {
        return view('admin.functions');
    }
}
