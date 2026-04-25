<?php

namespace App\Http\Controllers;

use App\Models\History;

class HistoryController extends Controller
{
    public function index()
{
    $history = History::latest()->get();

    // create a default summary if not using UchambuziController logic


    return view('uchambuzi.index', compact('mwenendoSummary', 'history'));
}



}
