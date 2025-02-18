<?php
namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Evento;
class HomeController extends Controller
{
    public function index()
    {
        $eventos = Evento::latest()->take(5)->get(); // 5 eventos recientes
        return view('home', compact('eventos'));
    }
    public function index_api() {
        $eventos = Evento::all();
        return response()->json($eventos);
    }
}
