<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with([
            'client',
            'gateway',
            'products'
        ])
        ->latest()
        ->paginate(10);
    }

    public function show($id)
    {
        return Transaction::with([
            'client',
            'gateway',
            'products'
        ])->findOrFail($id);
    }
}