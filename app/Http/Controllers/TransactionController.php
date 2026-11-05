<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show(Request $request, Transaction $transaction)
    {
        $user = $request->user();

        // Ang customer na nagbayad, ang may-ari ng salon, o ang platform admin lang
        $allowed = $user->isAdmin()
            || $transaction->user_id === $user->id
            || ($user->isSalonOwner() && $user->salon?->id === $transaction->salon_id);

        abort_unless($allowed, 403);

        $transaction->load(['salon', 'appointment.staff', 'customer']);

        return view('transactions.receipt', compact('transaction'));
    }
}