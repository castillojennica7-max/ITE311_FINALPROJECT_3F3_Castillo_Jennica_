<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return view('services.index', [
            'services' => Service::latest()->get(),
        ]);
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Service::create($data);

        return redirect()->route('services.index')
                         ->with('success', 'Service added!');
    }
}