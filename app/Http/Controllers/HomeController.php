<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
class HomeController extends Controller
{
    public function index()
    {
        // Ambil token login dari session
        $token = Session::get('token');

        // Ambil data user dari API
        $response = Http::withToken($token)->get('http://pdu-dms.my.id/api/user');

        $user = $response->json(); // hasil JSON API

        return view('home', compact('user'));
    }
}
