<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('home');
    }
        public function lastOpen()
    {
        return view('last-opened');
    }
    public function mySpace()
    {
        return view('myspace');
    }
    public function sharedWithMe()
    {
        return view('shared');
    }
        public function uploadFile()
    {
        return view('upload-file');
    }
}
