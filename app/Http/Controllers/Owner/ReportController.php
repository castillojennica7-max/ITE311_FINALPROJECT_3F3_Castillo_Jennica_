<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Support\Csv;
use App\Support\ReportRange;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public const EXPORTS = ['transactions', 'appointments', 'customers'];

    public function index(Request $request)
    {
        $salon   = $request->user()->salon;
        $range   = ReportRange::fromRequest($request);
        $between = $range->between();

        // Bayad na natanggap
        $payments = $salon->transactions()->whereBetween('paid_at', $between);
        $revenue  = (float) (clone $payments)->sum('amount');
        $count    = (clone $payments)->count();
        $average  = $count > 0 ? $revenue / $count : 0.0;

        $byMethod = (clone $payments)
            ->selectRaw('method, count(*) as total, sum(amount) as revenue')
            ->groupBy('method')
            ->orderByDesc('revenue')
            ->get();

        // Appointments
        $apptByStatus = $salon->appointments()
            ->whereBetween('appointment_at', $between)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $newCustomers = $salon->appointments()
            ->whereBetween('appointment_at', $between)
            ->distinct()
            ->count('user_id');

        // Kita ayon sa serbisyo at sa stylist
        $byService = Transaction::where('transactions.salon_id', $salon->id)
            ->whereBetween('transactions.paid_at', $between)
            ->leftJoin('appointments', 'appointments.id', '=', 'transactions.appointment_id')
            ->selectRaw("coalesce(appointments.service_name, 'Other') as name, count(*) as total, sum(transactions.amount) as revenue")
            ->groupBy('name')
            ->orderByDesc('revenue')
            ->get();

        $byStylist = Transaction::where('transactions.salon_id', $salon->id)
            ->whereBetween('transactions.paid_at', $between)
            ->leftJoin('appointments', 'appointments.id', '=', 'transactions.appointment_id')
            ->leftJoin('staff', 'staff.id', '=', 'appointments.staff_id')
            ->selectRaw("coalesce(staff.name, 'No stylist assigned') as name, count(*) as total, sum(transactions.amount) as revenue")
            ->groupBy('name')
            ->orderByDesc('revenue')
            ->get();

        // Kita bawat araw
        $series = $range->days() <= 92 ? $range->emptySeries() : [];

        if ($series) {
            $salon->transactions()
                ->whereBetween('paid_at', $between)
                ->selectRaw('date(paid_at) as d, sum(amount) as total')
                ->groupBy('d')
                ->pluck('total', 'd')
                ->each(function ($total, $d) use (&$series) {
                    if (array_key_exists($d, $series)) {
                        $series[$d] = (float) $total;
                    }
                });
        }

        return view('owner.reports.index', compact(
            'range', 'revenue', 'count', 'average', 'byMethod', 'apptByStatus',
            'newCustomers', 'byService', 'byStylist', 'series'
        ));
    }

    public function export(Request $request, string $type)
    {
        abort_unless(in_array($type, self::EXPORTS, true), 404);

        $salon   = $request->user()->salon;
        $range   = ReportRange::fromRequest($request);
        $between = $range->between();
        $name    = "my-salon-{$type}-" . $range->from->toDateString() . '-to-' . $range->to->toDateString() . '.csv';

        switch ($type) {
            case 'transactions':
                $rows = $salon->transactions()->with(['customer', 'appointment'])
                    ->whereBetween('paid_at', $between)
                    ->orderBy('paid_at')
                    ->get()
                    ->map(fn ($t) => [
                        $t->reference_no, $t->customer?->name ?? 'Walk-in', $t->appointment?->service_name ?? '',
                        $t->methodLabel(), (float) $t->amount, $t->paid_at->format('Y-m-d H:i'), $t->notes ?? '',
                    ]);

                return Csv::download($name,
                    ['Reference', 'Customer', 'Service', 'Method', 'Amount', 'Paid at', 'Notes'], $rows);

            case 'appointments':
                $rows = $salon->appointments()->with(['user', 'staff'])
                    ->whereBetween('appointment_at', $between)
                    ->orderBy('appointment_at')
                    ->get()
                    ->map(fn ($a) => [
                        $a->id, $a->user->name, $a->user->email, $a->service_name,
                        $a->staff?->name ?? 'Any available', $a->appointment_at->format('Y-m-d H:i'),
                        (float) $a->service_price, $a->status,
                    ]);

                return Csv::download($name,
                    ['ID', 'Customer', 'Customer email', 'Service', 'Stylist', 'Date and time', 'Price', 'Status'], $rows);

            default: // customers
                $rows = $salon->appointments()->with('user')
                    ->whereBetween('appointment_at', $between)
                    ->get()
                    ->groupBy('user_id')
                    ->map(fn ($items) => [
                        $items->first()->user->name,
                        $items->first()->user->email,
                        $items->count(),
                        (float) $items->where('status', 'completed')->sum('service_price'),
                        $items->max('appointment_at')->format('Y-m-d H:i'),
                    ])
                    ->values();

                return Csv::download($name,
                    ['Customer', 'Email', 'Bookings', 'Completed value', 'Last appointment'], $rows);
        }
    }
}
