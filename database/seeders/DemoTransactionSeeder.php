<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2027);

        $methods = ['cash', 'cash', 'cash', 'gcash', 'gcash', 'card', 'paypal'];
        $count   = 0;

        // Walang events: hindi magpapadala ng email habang nagse-seed
        Model::withoutEvents(function () use ($methods, &$count) {
            Appointment::where('status', 'completed')
                ->doesntHave('transaction')
                ->get()
                ->each(function (Appointment $appointment) use ($methods, &$count) {
                    // 85% ng mga natapos na appointment ay bayad na
                    if (mt_rand(1, 100) > 85) {
                        return;
                    }

                    $method = $methods[mt_rand(0, count($methods) - 1)];

                    Transaction::create([
                        'salon_id'           => $appointment->salon_id,
                        'appointment_id'     => $appointment->id,
                        'user_id'            => $appointment->user_id,
                        'amount'             => $appointment->service_price,
                        'method'             => $method,
                        'reference_no'       => 'TX-' . strtoupper(Str::random(8)),
                        'provider_reference' => $method === 'paypal' ? 'demo_' . Str::random(14) : null,
                        'notes'              => $method === 'gcash' ? 'GCash ref ' . mt_rand(100000, 999999) : null,
                        'paid_at'            => $appointment->appointment_at->copy()->addMinutes($appointment->service_duration_minutes),
                    ]);

                    $count++;
                });
        });

        $this->command->info("Done: {$count} demo transactions created.");
    }
}