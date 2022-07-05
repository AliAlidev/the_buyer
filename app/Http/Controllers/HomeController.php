<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function adminIndex()
    {
        return view('admin.home');
    }

    public function buyerIndex()
    {
        return view('buyer.home');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        $data = Data::firstOrCreate(['code' => $request->code], [
            'quantity' => $request->quantity,
            'price' => $request->price,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description
        ]);
        if ($data->wasRecentlyCreated) {
            return back()->with('success', 'Data addedd successfully');
        } else {
            return back()->withErrors('This code already found!')->withInput();
        }
    }
}
