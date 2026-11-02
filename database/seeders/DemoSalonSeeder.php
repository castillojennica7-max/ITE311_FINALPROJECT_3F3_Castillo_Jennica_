<?php

namespace Database\Seeders;

use App\Models\Salon;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use App\Services\StrapiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSalonSeeder extends Seeder
{
    public function run(): void
    {
        // Kunin ang mga plan mula sa Strapi; kung hindi maabot, gumamit ng built-in
        $plans = collect(app(StrapiService::class)->plans() ?? []);

        if ($plans->isEmpty()) {
            $this->command->warn('Strapi is not reachable or has no plans. Using built-in demo plans.');
            $plans = collect([
                ['documentId' => 'demo-basic',    'name' => 'Basic',    'price' => 499,  'max_staff' => 3,    'max_monthly_appointments' => 100],
                ['documentId' => 'demo-standard', 'name' => 'Standard', 'price' => 999,  'max_staff' => 10,   'max_monthly_appointments' => 500],
                ['documentId' => 'demo-premium',  'name' => 'Premium',  'price' => 1999, 'max_staff' => null, 'max_monthly_appointments' => null],
            ]);
        }

        $plansByName = $plans->keyBy('name');

        $rows = [
            [
                'owner' => ['Maria Santos', 'maria.santos@salonsync.test'],
                'salon' => 'Glam Haven Salon', 'phone' => '0917 000 0101', 'address' => '12 Mabini Street, Poblacion',
                'plan'  => 'Premium',
                'staff' => [
                    ['Angela Cruz', 'Hair Stylist', '0917 000 0111'],
                    ['Rina Lopez', 'Colorist', '0917 000 0112'],
                    ['Joy Mendoza', 'Manicurist', '0917 000 0113'],
                ],
            ],
            [
                'owner' => ['Jose Ramirez', 'jose.ramirez@salonsync.test'],
                'salon' => 'Kulay Beauty Studio', 'phone' => '0917 000 0102', 'address' => '45 Rizal Avenue, San Roque',
                'plan'  => 'Standard',
                'staff' => [
                    ['Dante Pascual', 'Hair Stylist', '0917 000 0121'],
                    ['Lyka Salvador', 'Colorist', '0917 000 0122'],
                    ['Mia Corpuz', 'Nail Technician', '0917 000 0123'],
                ],
            ],
            [
                'owner' => ['Angelica Reyes', 'angelica.reyes@salonsync.test'],
                'salon' => 'Bella Trese Salon', 'phone' => '0917 000 0103', 'address' => '8 Bonifacio Street, Centro',
                'plan'  => 'Basic',
                'staff' => [
                    ['Trisha Ocampo', 'Hair Stylist', '0917 000 0131'],
                    ['Karen Flores', 'Manicurist', '0917 000 0132'],
                ],
            ],
            [
                'owner' => ['Ricardo Dela Cruz', 'ricardo.delacruz@salonsync.test'],
                'salon' => 'The Cutting Edge Barbershop', 'phone' => '0917 000 0104', 'address' => '21 Luna Street, Poblacion',
                'plan'  => 'Basic',
                'staff' => [
                    ['Jun Dizon', 'Barber', '0917 000 0141'],
                    ['Paolo Castro', 'Barber', '0917 000 0142'],
                    ['Ian Soriano', 'Hair Stylist', '0917 000 0143'],
                ],
            ],
            [
                'owner' => ['Cristina Villanueva', 'cristina.villanueva@salonsync.test'],
                'salon' => 'Hiraya Hair & Nails', 'phone' => '0917 000 0105', 'address' => '77 Quezon Boulevard, Barangay Uno',
                'plan'  => 'Standard',
                'staff' => [
                    ['Bianca Tan', 'Hair Stylist', '0917 000 0151'],
                    ['Nina Aguilar', 'Manicurist', '0917 000 0152'],
                    ['Grace Padilla', 'Pedicurist', '0917 000 0153'],
                ],
            ],
            [
                'owner' => ['Benjamin Aquino', 'benjamin.aquino@salonsync.test'],
                'salon' => 'Mestiza Salon & Spa', 'phone' => '0917 000 0106', 'address' => '5 Del Pilar Street, San Jose',
                'plan'  => 'Premium',
                'staff' => [
                    ['Celine Rivera', 'Senior Stylist', '0917 000 0161'],
                    ['Hannah Gomez', 'Makeup Artist', '0917 000 0162'],
                    ['Patricia Lim', 'Manicurist', '0917 000 0163'],
                ],
            ],
            [
                'owner' => ['Rowena Bautista', 'rowena.bautista@salonsync.test'],
                'salon' => 'Lakambini Beauty Lounge', 'phone' => '0917 000 0107', 'address' => '103 Aguinaldo Road, Santa Cruz',
                'plan'  => 'Standard',
                'staff' => [
                    ['Faith Ignacio', 'Hair Stylist', '0917 000 0171'],
                    ['Joanna Ramos', 'Nail Technician', '0917 000 0172'],
                ],
            ],
            [
                'owner' => ['Eduardo Navarro', 'eduardo.navarro@salonsync.test'],
                'salon' => 'Ginto Hair Studio', 'phone' => '0917 000 0108', 'address' => '30 Burgos Street, Poblacion',
                'plan'  => 'Basic',
                'staff' => [
                    ['Carlo Tolentino', 'Hair Stylist', '0917 000 0181'],
                    ['Ella Magno', 'Colorist', '0917 000 0182'],
                    ['Rey Manalo', 'Barber', '0917 000 0183'],
                ],
            ],
            [
                'owner' => ['Liza Mercado', 'liza.mercado@salonsync.test'],
                'salon' => 'Sampaguita Nail Bar', 'phone' => '0917 000 0109', 'address' => '16 Magsaysay Avenue, San Isidro',
                'plan'  => 'Basic',
                'staff' => [
                    ['Daisy Valdez', 'Manicurist', '0917 000 0191'],
                    ['Ruby Enriquez', 'Pedicurist', '0917 000 0192'],
                    ['Shiela Roque', 'Nail Technician', '0917 000 0193'],
                ],
            ],
            [
                'owner' => ['Marco Domingo', 'marco.domingo@salonsync.test'],
                'salon' => 'Urban Trim Barbershop', 'phone' => '0917 000 0110', 'address' => '9 Katipunan Street, Barangay Dos',
                'plan'  => 'Standard',
                'staff' => [
                    ['Kevin Alcantara', 'Barber', '0917 000 0201'],
                    ['Jerome Bernardo', 'Barber', '0917 000 0202'],
                ],
            ],
        ];

        // Walang events: hindi magpapadala ng email o mag-si-sync sa Strapi habang nagse-seed
        Model::withoutEvents(function () use ($rows, $plansByName, $plans) {
            foreach ($rows as $row) {
                [$ownerName, $ownerEmail] = $row['owner'];

                $owner = User::firstOrNew(['email' => $ownerEmail]);
                $owner->forceFill([
                    'name'              => $ownerName,
                    'password'          => Hash::make('password'),
                    'role'              => 'salon_owner',
                    'email_verified_at' => now(),
                ])->save();

                $salon = Salon::updateOrCreate(
                    ['owner_id' => $owner->id],
                    ['name' => $row['salon'], 'phone' => $row['phone'], 'address' => $row['address']]
                );

                foreach ($row['staff'] as [$name, $role, $phone]) {
                    Staff::firstOrCreate(
                        ['salon_id' => $salon->id, 'name' => $name],
                        ['role' => $role, 'phone' => $phone]
                    );
                }

                if (! $salon->subscriptions()->active()->exists()) {
                    $plan  = $plansByName->get($row['plan']) ?? $plans->first();
                    $start = now()->subDays(rand(1, 20));

                    Subscription::create([
                        'salon_id'                 => $salon->id,
                        'user_id'                  => $owner->id,
                        'plan_document_id'         => $plan['documentId'],
                        'plan_name'                => $plan['name'],
                        'price'                    => $plan['price'],
                        'max_staff'                => $plan['max_staff'] ?? null,
                        'max_monthly_appointments' => $plan['max_monthly_appointments'] ?? null,
                        'salon_name'               => $salon->name,
                        'status'                   => 'active',
                        'reference_no'             => 'SS-' . strtoupper(Str::random(8)),
                        'starts_at'                => $start,
                        'ends_at'                  => $start->copy()->addMonth(),
                    ]);
                }
            }
        });

        $this->command->info('Done: 10 demo salons with owners, plans, and staff.');
        $this->command->info('Owner login password for all accounts: password');
    }
}