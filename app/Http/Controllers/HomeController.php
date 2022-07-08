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
    }

    public function createitem(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        $data = Data::firstOrCreate(['code' => $request->code], [
            'name' => $request->name,
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

    public function createitemindex()
    {
        return view('buyer.home');
    }

    public function findBySerialCode(Request $request)
    {
        $data = Data::where('code', $request->code)->first();
        if ($data) {
            return response()->json(['success' => true, 'data' => $data], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 400);
        }
    }

    public function listitems()
    {
        $items = Data::all();
        return view('buyer.list_inventory_items', ['items' => $items]);
    }
}
