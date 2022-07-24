<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth:sanctum');
    }

    public function file()
    {
        // return file stream from storage;
        return Storage::download(request('file'));
    }
}
