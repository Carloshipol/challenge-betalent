<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function index()
    {
        return Gateway::orderBy('priority')->get();
    }

    public function activate($id)
    {
        $gateway = Gateway::findOrFail($id);

        $gateway->update([
            'is_active' => true
        ]);

        return response()->json($gateway);
    }

    public function deactivate($id)
    {
        $gateway = Gateway::findOrFail($id);

        $gateway->update([
            'is_active' => false
        ]);

        return response()->json($gateway);
    }

    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => ['required','integer']
        ]);

        $gateway = Gateway::findOrFail($id);

        $gateway->update([
            'priority' => $request->priority
        ]);

        return response()->json($gateway);
    }
}