<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Salon;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Support\Csv;
use App\Support\ReportRange;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public const EXPORTS = ['appointments', 'subscriptions', 'transactions', 'users', 'salons'];

    public function index(Request $request)
    {
        $range   = ReportRange::fromRequest($request);
        $between = $range->between();

        // Mga bagong user ayon sa role
        $usersByRole = User::whereBetween('created_at', $between)
            ->selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        // Subscriptions (kita ng SalonSync)
        $subscriptions = Subscription::whereBetween('starts_at', $between);
        $subCount      = (clone $subscriptions)->count();
        $subRevenue    = (float) (clone $subscriptions)->sum('price');
        $subsByPlan    = (clone $subscriptions)
            ->selectRaw('plan_name, count(*) as total, sum(price) as revenue')
            ->groupBy('plan_name')
            ->orderByDesc('revenue')
            ->get();

        // Appointments
        $apptByStatus = Appointment::whereBetween('appointment_at', $between)
            ->selectRaw('status, count(*) as total, sum(service_price) as value')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $apptTotal = (int) $apptByStatus->sum('total');

        // Bayad ng mga customer sa mga salon
        $payments     = Transaction::whereBetween('paid_at', $between);
        $paymentTotal = (float) (clone $payments)->sum('amount');
        $paymentCount = (clone $payments)->count();

        // Pinakamahusay na mga salon sa panahong ito
        $topSalons = Salon::query()
            ->withCount(['appointments as appointments_in_range' => fn ($q) => $q->whereBetween('appointment_at', $between)])
            ->withSum(['transactions as revenue_in_range' => fn ($q) => $q->whereBetween('paid_at', $between)], 'amount')
            ->orderByDesc('revenue_in_range')
            ->orderByDesc('appointments_in_range')
            ->limit(10)
            ->get();

        // Appointments bawat araw
        $series = $range->emptySeries();

        if ($range->days() <= 92) {
            Appointment::whereBetween('appointment_at', $between)
                ->selectRaw('date(appointment_at) as d, count(*) as total')
                ->groupBy('d')
                ->pluck('total', 'd')
                ->each(function ($total, $d) use (&$series) {
                    if (array_key_exists($d, $series)) {
                        $series[$d] = (int) $total;
                    }
                });
        } else {
            $series = [];
        }

        return view('admin.reports.index', compact(
            'range', 'usersByRole', 'subCount', 'subRevenue', 'subsByPlan',
            'apptByStatus', 'apptTotal', 'paymentTotal', 'paymentCount', 'topSalons', 'series'
        ));
    }

    public function export(Request $request, string $type)
    {
        abort_unless(in_array($type, self::EXPORTS, true), 404);

        $range   = ReportRange::fromRequest($request);
        $between = $range->between();
        $name    = "salonsync-{$type}-" . $range->from->toDateString() . '-to-' . $range->to->toDateString() . '.csv';

        switch ($type) {
            case 'appointments':
                $rows = Appointment::with(['salon', 'user', 'staff'])
                    ->whereBetween('appointment_at', $between)
                    ->orderBy('appointment_at')
                    ->get()
                    ->map(fn ($a) => [
                        $a->id, $a->salon->name, $a->user->name, $a->user->email, $a->service_name,
                        $a->staff?->name ?? 'Any available', $a->appointment_at->format('Y-m-d H:i'),
                        (float) $a->service_price, $a->status,
                    ]);

                return Csv::download($name,
                    ['ID', 'Salon', 'Customer', 'Customer email', 'Service', 'Stylist', 'Date and time', 'Price', 'Status'], $rows);

            case 'subscriptions':
                $rows = Subscription::with('user')
                    ->whereBetween('starts_at', $between)
                    ->orderBy('starts_at')
                    ->get()
                    ->map(fn ($s) => [
                        $s->reference_no, $s->salon_name, $s->user->name, $s->user->email, $s->plan_name,
                        (float) $s->price, $s->starts_at->format('Y-m-d'), $s->ends_at->format('Y-m-d'), $s->status,
                    ]);

                return Csv::download($name,
                    ['Reference', 'Salon', 'Owner', 'Owner email', 'Plan', 'Amount', 'Starts', 'Ends', 'Status'], $rows);

            case 'transactions':
                $rows = Transaction::with(['salon', 'customer', 'appointment'])
                    ->whereBetween('paid_at', $between)
                    ->orderBy('paid_at')
                    ->get()
                    ->map(fn ($t) => [
                        $t->reference_no, $t->salon->name, $t->customer?->name ?? 'Walk-in',
                        $t->appointment?->service_name ?? '', $t->methodLabel(),
                        (float) $t->amount, $t->paid_at->format('Y-m-d H:i'),
                    ]);

                return Csv::download($name,
                    ['Reference', 'Salon', 'Customer', 'Service', 'Method', 'Amount', 'Paid at'], $rows);

            case 'users':
                $rows = User::whereBetween('created_at', $between)
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn ($u) => [
                        $u->id, $u->name, $u->email, $u->role,
                        $u->created_at->format('Y-m-d'), $u->suspended_at ? 'Suspended' : 'Active',
                    ]);

                return Csv::download($name, ['ID', 'Name', 'Email', 'Role', 'Registered', 'Status'], $rows);

            default: // salons
                $rows = Salon::with('owner')
                    ->withCount(['staff', 'appointments'])
                    ->whereBetween('created_at', $between)
                    ->orderBy('created_at')
                    ->get()
                    ->map(fn ($s) => [
                        $s->id, $s->name, $s->owner->name, $s->owner->email, $s->address ?? '',
                        $s->staff_count, $s->appointments_count, $s->created_at->format('Y-m-d'),
                        $s->isSuspended() ? 'Suspended' : 'Active',
                    ]);

                return Csv::download($name,
                    ['ID', 'Salon', 'Owner', 'Owner email', 'Address', 'Staff', 'Appointments', 'Registered', 'Status'], $rows);
        }
    }
}
