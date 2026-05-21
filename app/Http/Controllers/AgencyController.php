<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        return view('agencies.index');
    }

    public function create()
    {
        return view('agencies.create');
    }

    public function edit(Agency $agency)
    {
        return view('agencies.edit', compact('agency'));
    }
}