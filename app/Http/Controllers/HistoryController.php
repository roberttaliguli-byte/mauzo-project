<?php

namespace App\Http\Controllers;

use App\Models\History;

class HistoryController extends Controller
{
    public function index()
{
    $history = History::latest()->get();

    // create a default summary if not using UchambuziController logic
    $mwenendoSummary = [
        'date'          => now()->format('d M, Y'),
        'mapatoMauzo'   => 0,
        'mapatoMadeni'  => 0,
        'jumlaMapato'   => 0,
        'jumlaMatumizi' => 0,
        'faidaMauzo'    => 0,
        'fedhaDroo'     => 0,
        'faidaHalisi'   => 0,
    ];

    return view('uchambuzi.index', compact('mwenendoSummary', 'history'));
}



}
