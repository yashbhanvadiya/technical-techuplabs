<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class IndexController extends Controller
{
    public function index(Request $request) {
        try{
            return view('auth.login');
        } catch(Exception $e){
            abort(500);
        }
    }   
}
