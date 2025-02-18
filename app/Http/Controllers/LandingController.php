<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ponente;
use App\Models\Evento;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }
}
