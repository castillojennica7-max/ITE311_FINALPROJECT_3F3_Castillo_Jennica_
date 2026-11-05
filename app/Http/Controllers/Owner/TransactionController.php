<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Transaction;
use App\Notifications\PaymentReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public const MANUAL_METHODS = ['cash', 'gcash', 'card'];
    public const ALL_METHODS    = ['cash', 'gcash', 'card', 'paypal'];

    public function index(Request $request)
    {
        $salon  = $request->user()->salon;
        $method = $request->query('method');

        $transactions = $salon->transactions()
            ->with(['customer', 'appointment'])
            ->when(in_array($method, self::ALL_METHODS), fn ($q) => $q->where('method', $method))
            ->latest('paid_at')
            ->paginate(15)
            ->withQueryString();

        $totals = [
            'today' => (float) $salon->transactions()->whereDate('paid_at', today())->sum('amount'),
            'month' => (float) $salon->transactions()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'all'   => (float) $salon->transactions()->sum('amount'),
            'count' => $salon->transactions()->count(),
        ];

        return view('owner.transactions.index', [
            'transactions' => $transactions,
            'totals'       => $totals,
            'method'       => $method,
            'methods'      => self::ALL_METHODS,
        ]);
    }

    // Form para mag-record ng bayad sa salon (cash, GCash, card)
    public function create(Request $request, Appointment $appointment)
    {
        if ($response = $this->guard($request, $appointment)) {
            return $response;
        }

        return view('owner.transactions.create', [
            'appointment' => $appointment->load(['user', 'staff']),
            'methods'     => self::MANUAL_METHODS,
        ]);
    }

    public function store(Request $request, Appointment $appointment)
    {
        if ($response = $this->guard($request, $appointment)) {
            return $response;
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:1|max:999999',
            'method' => ['required', Rule::in(self::MANUAL_METHODS)],
            'notes'  => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'salon_id'       => $appointment->salon_id,
            'appointment_id' => $appointment->id,
            'user_id'        => $appointment->user_id,
            'recorded_by'    => $request->user()->id,
            'amount'         => $data['amount'],
            'method'         => $data['method'],
            'reference_no'   => 'TX-' . strtoupper(Str::random(8)),
            'notes'          => $data['notes'] ?? null,
            'paid_at'        => now(),
        ]);

        // Kapag nabayaran na, tapos na rin ang serbisyo
        if ($appointment->status !== 'completed') {
            $appointment->update(['status' => 'completed']);
        }

        try {
            $appointment->user->notify(new PaymentReceived($transaction));
        } catch (\Throwable $e) {
            Log::warning('Failed to send payment email: ' . $e->getMessage());
        }

        return redirect()->route('transactions.receipt', $transaction)
            ->with('success', 'Payment recorded.');
    }

    // Sariling salon lang; hindi puwede ang kanselado o bayad na
    private function guard(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->salon_id === $request->user()->salon->id, 403);

        if ($appointment->status === 'cancelled') {
            return redirect()->route('owner.appointments.index')
                ->with('error', 'Cancelled appointments cannot be paid.');
        }

        if ($appointment->transaction()->exists()) {
            return redirect()->route('owner.appointments.index')
                ->with('error', 'This appointment is already paid.');
        }

        return null;
    }
}