<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSalonProfileSeeder extends Seeder
{
    public function run(): void
    {
        // Oras: Lunes hanggang Biyernes, Sabado, at Linggo
        $week = function (string $open, string $close, ?array $saturday = null, ?array $sunday = null): array {
            $h = [];

            foreach (['mon', 'tue', 'wed', 'thu', 'fri'] as $d) {
                $h[$d] = ['open' => $open, 'close' => $close, 'closed' => false];
            }

            $h['sat'] = $saturday
                ? ['open' => $saturday[0], 'close' => $saturday[1], 'closed' => false]
                : ['open' => $open, 'close' => $close, 'closed' => false];

            $h['sun'] = $sunday
                ? ['open' => $sunday[0], 'close' => $sunday[1], 'closed' => false]
                : ['open' => '09:00', 'close' => '17:00', 'closed' => true];

            return $h;
        };

        $s = fn (string $name, string $cat, int $price, int $min, string $desc) =>
            ['name' => $name, 'category' => $cat, 'price' => $price, 'duration_minutes' => $min, 'description' => $desc];

        $promo = fn (string $title, string $desc, int $percent, int $days) =>
            ['type' => 'promo', 'title' => $title, 'description' => $desc, 'discount_percent' => $percent, 'days' => $days];

        $bundle = fn (string $title, string $desc, int $price, string $includes, int $days) =>
            ['type' => 'bundle', 'title' => $title, 'description' => $desc, 'bundle_price' => $price, 'included_services' => $includes, 'days' => $days];

        $rows = [
            [
                'email'   => 'maria.santos@salonsync.test',
                'cover'   => 'glam-haven',
                'tagline' => 'Premium hair, nails, and bridal beauty',
                'about'   => "Glam Haven Salon is a full-service salon in the heart of Poblacion. Our stylists, colorists, and nail artists have more than ten years of experience, and we use only trusted professional products.\n\nWalk in for a quick trim or book ahead for our bridal and event packages.",
                'hours'   => $week('09:00', '19:00', ['09:00', '20:00'], ['10:00', '17:00']),
                'services' => [
                    $s('Haircut and Styling', 'hair', 350, 45, 'Consultation, wash, cut, and blow-dry styling.'),
                    $s('Full Hair Color', 'hair', 1800, 120, 'Single-color application with premium dye and treatment.'),
                    $s('Brazilian Rebond', 'hair', 3500, 180, 'Long-lasting straightening for smooth, shiny hair.'),
                    $s('Hair Spa Treatment', 'hair', 900, 60, 'Deep-conditioning spa with scalp massage.'),
                    $s('Gel Manicure', 'nails', 450, 60, 'Nail shaping, cuticle care, and long-wear gel polish.'),
                    $s('Foot Spa and Pedicure', 'nails', 500, 60, 'Relaxing foot soak, scrub, and polish.'),
                    $s('Bridal Makeup', 'makeup', 3500, 120, 'Trial-tested bridal look with lashes and touch-up kit.'),
                ],
                'promos' => [
                    $promo('Grand Opening Promo', 'Enjoy a discount on all hair services this month.', 20, 30),
                    $bundle('Bridal Glow Package', 'Look your best on your big day.', 5900, "Bridal makeup\nHaircut and styling\nGel manicure", 60),
                ],
            ],
            [
                'email'   => 'jose.ramirez@salonsync.test',
                'cover'   => 'kulay-beauty',
                'tagline' => 'Color specialists: balayage, highlights, and more',
                'about'   => 'Kulay Beauty Studio is all about color. Our colorists craft custom shades that fit your skin tone and lifestyle, from subtle highlights to bold transformations.',
                'hours'   => $week('10:00', '19:00', ['09:00', '19:00']),
                'services' => [
                    $s('Haircut', 'hair', 250, 40, 'Wash, cut, and blow-dry.'),
                    $s('Balayage', 'hair', 3200, 180, 'Hand-painted, natural-looking highlights.'),
                    $s('Highlights', 'hair', 2200, 150, 'Classic foil highlights with toner.'),
                    $s('Keratin Treatment', 'hair', 1500, 90, 'Smoothing treatment that reduces frizz.'),
                    $s('Nail Art', 'nails', 350, 60, 'Creative nail designs, price per set.'),
                    $s('Manicure', 'nails', 200, 40, 'Classic manicure with polish.'),
                ],
                'promos' => [
                    $promo('Color Week Discount', 'Save on any color service from Monday to Wednesday.', 15, 21),
                    $bundle('Color and Treat', 'Color plus a treatment to keep it glossy.', 2600, "Full hair color\nKeratin treatment (mini)\nBlow-dry", 45),
                ],
            ],
            [
                'email'   => 'angelica.reyes@salonsync.test',
                'cover'   => 'bella-trese',
                'tagline' => 'Affordable everyday beauty',
                'about'   => 'Bella Trese Salon offers friendly, budget-friendly service for the whole family. Quick, clean, and always on time.',
                'hours'   => $week('09:00', '18:00', ['09:00', '18:00'], ['10:00', '16:00']),
                'services' => [
                    $s('Haircut', 'hair', 150, 30, 'Simple, clean cut.'),
                    $s('Blow-dry', 'hair', 200, 30, 'Wash and blow-dry.'),
                    $s('Hair Color', 'hair', 800, 90, 'Single-color application.'),
                    $s('Hair Spa', 'hair', 500, 60, 'Nourishing hair spa.'),
                    $s('Manicure', 'nails', 150, 30, 'Basic manicure and polish.'),
                    $s('Pedicure', 'nails', 180, 40, 'Basic pedicure and polish.'),
                ],
                'promos' => [
                    $promo('Student Discount', 'Show your school ID and save on any haircut.', 10, 60),
                    $bundle('Mani-Pedi Duo', 'Pamper your hands and feet.', 280, "Manicure\nPedicure", 45),
                ],
            ],
            [
                'email'   => 'ricardo.delacruz@salonsync.test',
                'cover'   => 'cutting-edge',
                'tagline' => 'Sharp cuts for sharp people',
                'about'   => 'The Cutting Edge Barbershop is a classic barbershop with a modern touch. Walk-ins are welcome, but booking guarantees your chair.',
                'hours'   => $week('09:00', '20:00', null, ['10:00', '18:00']),
                'services' => [
                    $s('Classic Haircut', 'barber', 120, 30, 'Scissor or clipper cut with neck shave.'),
                    $s('Fade Cut', 'barber', 180, 40, 'Low, mid, or high fade with sharp lines.'),
                    $s('Hot Towel Shave', 'barber', 150, 30, 'Traditional straight-razor shave.'),
                    $s('Beard Trim', 'barber', 100, 20, 'Shape and line-up.'),
                    $s('Kids Haircut', 'barber', 100, 25, 'For kids 12 and below.'),
                    $s('Hair Color (Black)', 'hair', 400, 45, 'Cover gray with natural black.'),
                ],
                'promos' => [
                    $bundle('Gentleman Package', 'The full barber experience.', 280, "Classic haircut\nBeard trim\nHot towel", 45),
                    $promo('Father and Son Day', 'Book two haircuts together and save.', 15, 30),
                ],
            ],
            [
                'email'   => 'cristina.villanueva@salonsync.test',
                'cover'   => 'hiraya',
                'tagline' => 'Hair and nails under one roof',
                'about'   => 'Hiraya Hair and Nails brings hair and nail services together so you can finish everything in one visit.',
                'hours'   => $week('10:00', '19:00', ['09:00', '20:00'], ['11:00', '17:00']),
                'services' => [
                    $s('Haircut', 'hair', 200, 40, 'Wash, cut, and style.'),
                    $s('Rebond', 'hair', 2200, 180, 'Straightening treatment.'),
                    $s('Hair Color', 'hair', 950, 90, 'Single-color application.'),
                    $s('Gel Polish', 'nails', 400, 50, 'Long-lasting gel polish.'),
                    $s('Nail Art', 'nails', 250, 45, 'Design add-on, per set.'),
                    $s('Pedicure', 'nails', 250, 50, 'Foot soak, scrub, and polish.'),
                    $s('Manicure', 'nails', 200, 40, 'Classic manicure.'),
                ],
                'promos' => [
                    $promo('Weekday Special', 'Save on all nail services from Monday to Thursday.', 15, 28),
                    $bundle('Hair and Nails Combo', 'One visit, full makeover.', 1100, "Haircut\nHair color (short hair)\nManicure", 40),
                ],
            ],
            [
                'email'   => 'benjamin.aquino@salonsync.test',
                'cover'   => 'mestiza',
                'tagline' => 'Relax. Refresh. Renew.',
                'about'   => "Mestiza Salon and Spa is your escape from the busy day. Enjoy signature haircuts, restorative spa treatments, and professional makeup in a calm, comfortable space.\n\nAppointments are recommended for all spa services.",
                'hours'   => $week('10:00', '20:00', ['09:00', '21:00'], ['10:00', '19:00']),
                'services' => [
                    $s('Signature Haircut', 'hair', 500, 50, 'Consultation, wash, cut, and styling with a senior stylist.'),
                    $s('Hair Color', 'hair', 2200, 120, 'Premium color with gloss finish.'),
                    $s('Hair Spa', 'hair', 1000, 60, 'Deep conditioning with scalp massage.'),
                    $s('Body Massage', 'spa', 1200, 60, 'Full-body relaxation massage.'),
                    $s('Facial Treatment', 'spa', 1500, 75, 'Deep-cleansing facial with hydrating mask.'),
                    $s('Foot Spa', 'spa', 700, 45, 'Soak, scrub, and foot massage.'),
                    $s('Makeup Session', 'makeup', 2500, 90, 'Event-ready makeup application.'),
                ],
                'promos' => [
                    $bundle('Relaxation Day', 'The ultimate pamper package.', 2900, "Body massage\nFacial treatment\nFoot spa", 60),
                    $promo('Weekday Spa Deal', 'Save on all spa services from Monday to Thursday.', 20, 30),
                ],
            ],
            [
                'email'   => 'rowena.bautista@salonsync.test',
                'cover'   => 'lakambini',
                'tagline' => 'Your neighborhood beauty lounge',
                'about'   => 'Lakambini Beauty Lounge offers dependable hair, nail, and lash services in a relaxed, welcoming setting.',
                'hours'   => $week('09:30', '18:30', ['09:00', '19:00']),
                'services' => [
                    $s('Haircut', 'hair', 220, 40, 'Wash, cut, and blow-dry.'),
                    $s('Hair Color', 'hair', 1100, 100, 'Single-color application.'),
                    $s('Rebond', 'hair', 2600, 180, 'Straightening treatment.'),
                    $s('Gel Manicure', 'nails', 380, 50, 'Long-wear gel polish.'),
                    $s('Pedicure', 'nails', 260, 50, 'Foot soak, scrub, and polish.'),
                    $s('Eyelash Extension', 'makeup', 900, 75, 'Classic lash set for a natural look.'),
                ],
                'promos' => [
                    $promo('Birthday Treat', 'Celebrating a birthday this month? Enjoy a discount on any service.', 10, 30),
                    $bundle('Lash and Nails', 'Look polished from head to fingertip.', 1150, "Eyelash extension\nGel manicure", 45),
                ],
            ],
            [
                'email'   => 'eduardo.navarro@salonsync.test',
                'cover'   => 'ginto',
                'tagline' => 'Trusted hair studio since day one',
                'about'   => 'Ginto Hair Studio specializes in hair color, perms, and treatments for men and women.',
                'hours'   => $week('09:00', '18:00', ['09:00', '18:00']),
                'services' => [
                    $s('Haircut', 'hair', 180, 35, 'Wash, cut, and style.'),
                    $s('Hair Color', 'hair', 900, 90, 'Single-color application.'),
                    $s('Highlights', 'hair', 1800, 140, 'Foil highlights with toner.'),
                    $s('Perm', 'hair', 1600, 150, 'Soft curls or waves.'),
                    $s('Hair Treatment', 'hair', 700, 60, 'Repair treatment for dry or damaged hair.'),
                    $s('Mens Cut', 'barber', 140, 30, 'Clean and simple cut.'),
                ],
                'promos' => [
                    $promo('Rainy Season Special', 'Hair treatment discount for damaged hair.', 15, 30),
                    $bundle('Cut and Color', 'A fresh look in one visit.', 950, "Haircut\nHair color (short hair)", 40),
                ],
            ],
            [
                'email'   => 'liza.mercado@salonsync.test',
                'cover'   => 'sampaguita',
                'tagline' => 'Nails done right, every time',
                'about'   => 'Sampaguita Nail Bar focuses on clean, creative, long-lasting nail care. All tools are sanitized after every customer.',
                'hours'   => $week('10:00', '19:00', ['10:00', '20:00'], ['11:00', '18:00']),
                'services' => [
                    $s('Classic Manicure', 'nails', 180, 35, 'Nail shaping, cuticle care, and polish.'),
                    $s('Classic Pedicure', 'nails', 220, 45, 'Foot soak, scrub, and polish.'),
                    $s('Gel Manicure', 'nails', 400, 50, 'Long-wear gel polish for hands.'),
                    $s('Gel Pedicure', 'nails', 450, 60, 'Long-wear gel polish for feet.'),
                    $s('Nail Extension', 'nails', 800, 90, 'Acrylic or gel extensions.'),
                    $s('Nail Art Design', 'nails', 300, 45, 'Custom designs, price per set.'),
                    $s('Paraffin Hand Treatment', 'spa', 350, 30, 'Softening paraffin wax for hands.'),
                ],
                'promos' => [
                    $bundle('Mani-Pedi Gel Combo', 'Gel manicure and gel pedicure at a lower price.', 750, "Gel manicure\nGel pedicure", 45),
                    $promo('Friends Discount', 'Come with a friend and each of you saves.', 10, 30),
                ],
            ],
            [
                'email'   => 'marco.domingo@salonsync.test',
                'cover'   => 'urban-trim',
                'tagline' => 'Modern cuts, classic shaves',
                'about'   => 'Urban Trim Barbershop is a neighborhood barbershop with fast service, fair prices, and skilled barbers.',
                'hours'   => $week('09:00', '21:00', null, ['09:00', '19:00']),
                'services' => [
                    $s('Classic Haircut', 'barber', 130, 30, 'Clean cut with neck shave.'),
                    $s('Skin Fade', 'barber', 200, 40, 'Skin fade with sharp lines.'),
                    $s('Hot Towel Shave', 'barber', 160, 30, 'Relaxing hot towel shave.'),
                    $s('Beard Sculpt', 'barber', 120, 25, 'Detailed beard shaping.'),
                    $s('Hair Wash and Style', 'barber', 80, 15, 'Quick wash and styling.'),
                    $s('Kids Cut', 'barber', 110, 25, 'For kids 12 and below.'),
                ],
                'promos' => [
                    $promo('Payday Promo', 'Treat yourself every 15th and 30th.', 15, 60),
                    $bundle('Cut and Shave', 'Fresh cut and a smooth shave.', 250, "Classic haircut\nHot towel shave", 45),
                ],
            ],
        ];

        $updated = 0;

        foreach ($rows as $row) {
            $salon = User::where('email', $row['email'])->first()?->salon;

            if (! $salon) {
                $this->command->warn("No salon found for {$row['email']}. Run DemoSalonSeeder first.");
                continue;
            }

            // Papalitan lang ang default na larawan; hindi gagalawin ang in-upload na larawan o totoong litrato
            $current   = $salon->cover_path;
            $isDefault = ! $current || preg_match('#^images/covers/[^/]+\.svg$#', $current);

            $salon->update([
                'tagline'       => $row['tagline'],
                'about'         => $row['about'],
                'opening_hours' => $row['hours'],
                'cover_path'    => $isDefault ? 'images/covers/' . $row['cover'] . '.svg' : $current,
            ]);

            if ($salon->services()->doesntExist()) {
                foreach ($row['services'] as $service) {
                    $salon->services()->create($service + ['is_active' => true]);
                }
            }

            if ($salon->promotions()->doesntExist()) {
                foreach ($row['promos'] as $p) {
                    $days = $p['days'];
                    unset($p['days']);

                    $salon->promotions()->create($p + [
                        'valid_until' => now()->addDays($days)->toDateString(),
                        'is_active'   => true,
                    ]);
                }
            }

            $updated++;
        }

        $this->command->info("Done: profiles, hours, services, and promos added to {$updated} demo salons.");
    }
}
