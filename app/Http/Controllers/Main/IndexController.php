<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
    	return 'Welcome to your classcruiser account';
    }

    public function test()
    {
        return redirect('/');
    }
}
