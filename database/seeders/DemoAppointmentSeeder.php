<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Salon;
use App\Models\User;
use App\Services\StrapiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Parehong resulta tuwing patatakbuhin ang seeder
        mt_srand(2026);

        $salons = Salon::active()->with('staff')->get()->values();

        if ($salons->isEmpty()) {
            $this->command->error('No active salons found. Run DemoSalonSeeder first.');
            return;
        }

        // Kunin ang mga service mula sa Strapi; kung hindi maabot, gumamit ng built-in
        $services = collect(app(StrapiService::class)->services() ?? [])->values();

        if ($services->isEmpty()) {
            $this->command->warn('Strapi is not reachable or has no services. Using built-in demo services.');
            $services = collect([
                ['documentId' => 'demo-haircut',   'name' => 'Haircut',            'price' => 150, 'duration_minutes' => 30],
                ['documentId' => 'demo-color',     'name' => 'Hair Color',         'price' => 800, 'duration_minutes' => 90],
                ['documentId' => 'demo-spa',       'name' => 'Hair Spa Treatment', 'price' => 500, 'duration_minutes' => 60],
                ['documentId' => 'demo-manicure',  'name' => 'Manicure',           'price' => 200, 'duration_minutes' => 45],
                ['documentId' => 'demo-pedicure',  'name' => 'Pedicure',           'price' => 250, 'duration_minutes' => 45],
            ]);
        }

        $customerRows = [
            ['Andrea Villareal',   'andrea.villareal@salonsync.test'],
            ['Miguel Hernandez',   'miguel.hernandez@salonsync.test'],
            ['Sofia Garcia',       'sofia.garcia@salonsync.test'],
            ['Gabriel Torres',     'gabriel.torres@salonsync.test'],
            ['Isabel Fernandez',   'isabel.fernandez@salonsync.test'],
            ['Nathan Cabrera',     'nathan.cabrera@salonsync.test'],
            ['Camille Estrada',    'camille.estrada@salonsync.test'],
            ['Lorenzo Medina',     'lorenzo.medina@salonsync.test'],
            ['Patricia Soriano',   'patricia.soriano@salonsync.test'],
            ['Vincent Abad',       'vincent.abad@salonsync.test'],
            ['Daniela Francisco',  'daniela.francisco@salonsync.test'],
            ['Rafael Santiago',    'rafael.santiago@salonsync.test'],
        ];

        $notes = [
            null, null, null, null,
            'First time at this salon.',
            'Please use sulfate-free products.',
            'Running a few minutes late, thank you!',
            'Bringing a reference photo.',
        ];

        $created = 0;
        $skipped = false;

        // Walang events: hindi magpapadala ng email o mag-si-sync sa Strapi habang nagse-seed
        Model::withoutEvents(function () use ($salons, $services, $customerRows, $notes, &$created, &$skipped) {
            $customers = collect();

            foreach ($customerRows as [$name, $email]) {
                $user = User::firstOrNew(['email' => $email]);
                $user->forceFill([
                    'name'              => $name,
                    'password'          => Hash::make('password'),
                    'role'              => 'customer',
                    'email_verified_at' => now(),
                ])->save();
                $customers->push($user);
            }

            // Huwag magdoble kung naseed na dati
            if (Appointment::whereIn('user_id', $customers->pluck('id'))->exists()) {
                $skipped = true;
                return;
            }

            $taken = []; // iwas double booking ng parehong staff sa parehong oras

            $make = function (Salon $salon, User $customer, Carbon $when, string $status) use ($services, $notes, &$taken, &$created) {
                $staff = null;

                if ($salon->staff->isNotEmpty() && mt_rand(1, 100) <= 80) {
                    $staff = $salon->staff[mt_rand(0, $salon->staff->count() - 1)];

                    $key = $staff->id . '|' . $when->format('Y-m-d H:i');
                    if (isset($taken[$key])) {
                        return;
                    }
                    $taken[$key] = true;
                }

                $service = $services[mt_rand(0, $services->count() - 1)];

                Appointment::create([
                    'salon_id'                 => $salon->id,
                    'user_id'                  => $customer->id,
                    'staff_id'                 => $staff?->id,
                    'service_document_id'      => $service['documentId'],
                    'service_name'             => $service['name'],
                    'service_price'            => $service['price'],
                    'service_duration_minutes' => $service['duration_minutes'],
                    'appointment_at'           => $when,
                    'status'                   => $status,
                    'notes'                    => $notes[mt_rand(0, count($notes) - 1)],
                ]);

                $created++;
            };

            // 3 hanggang 6 na appointment bawat customer, halo ng nakaraan at paparating
            foreach ($customers as $customer) {
                $count = mt_rand(3, 6);

                for ($i = 0; $i < $count; $i++) {
                    $salon = $salons[mt_rand(0, $salons->count() - 1)];
                    $past  = mt_rand(1, 100) <= 60;
                    $days  = $past ? -mt_rand(1, 45) : mt_rand(1, 21);

                    $when = Carbon::now()->startOfDay()->addDays($days)
                        ->setTime(mt_rand(9, 17), mt_rand(0, 1) * 30);

                    $status = $past
                        ? (mt_rand(1, 100) <= 80 ? 'completed' : 'cancelled')
                        : (mt_rand(1, 100) <= 55 ? 'confirmed' : 'pending');

                    $make($salon, $customer, $when, $status);
                }
            }

            // May ilang appointment para ngayong araw, para may laman ang "Today's appointments"
            foreach ($salons->take(6) as $salon) {
                $customer = $customers[mt_rand(0, $customers->count() - 1)];
                $when     = Carbon::now()->startOfDay()->setTime(mt_rand(13, 18), 0);

                $make($salon, $customer, $when, mt_rand(1, 100) <= 60 ? 'confirmed' : 'pending');
            }
        });

        if ($skipped) {
            $this->command->warn('Demo appointments already exist. Nothing was added.');
            return;
        }

        $this->command->info("Done: 12 demo customers and {$created} demo appointments.");
        $this->command->info('Customer login password for all accounts: password');
    }
}