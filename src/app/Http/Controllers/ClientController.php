<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        return Client::latest()->paginate(10);
    }

    public function show($id)
    {
        return Client::with([
            'transactions.gateway',
            'transactions.products'
        ])->findOrFail($id);
    }
}