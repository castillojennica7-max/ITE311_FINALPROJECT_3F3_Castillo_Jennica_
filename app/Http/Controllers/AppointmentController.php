<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Salon;
use App\Notifications\AppointmentBooked;
use App\Services\StrapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    // Listahan ng appointments ng naka-login na customer
    public function index(Request $request)
    {
        $appointments = $request->user()
            ->appointments()
            ->with(['staff', 'salon'])
            ->latest('appointment_at')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    // Hakbang 1: pumili ng salon (mga salon na may aktibong subscription lang)
    public function create()
    {
        return view('appointments.create', [
            'salons' => Salon::active()->orderBy('name')->get(),
        ]);
    }

    // Hakbang 2: booking form para sa napiling salon
    public function book(StrapiService $strapi, Salon $salon)
    {
        abort_unless($salon->isActive(), 404);

        $services = $strapi->services();

        return view('appointments.book', [
            'salon'     => $salon,
            'services'  => $services ?? [],
            'cmsOnline' => $services !== null,
            'staff'     => $salon->staff()->orderBy('name')->get(),
        ]);
    }

    // I-save ang booking
    public function store(Request $request, StrapiService $strapi, Salon $salon)
    {
        abort_unless($salon->isActive(), 404);

        $data = $request->validate([
            'service_document_id' => 'required|string',
            // Staff ng napiling salon lang ang puwede
            'staff_id'            => ['nullable', Rule::exists('staff', 'id')->where('salon_id', $salon->id)],
            'appointment_at'      => 'required|date|after:now',
            'notes'               => 'nullable|string|max:500',
        ]);

        $services = $strapi->services();

        if ($services === null) {
            return back()->withInput()->withErrors([
                'service_document_id' => 'The service list is unavailable right now. Please try again later.',
            ]);
        }

        // Kukunin natin ang presyo mula sa CMS, hindi sa ipinadala ng browser
        $service = collect($services)->firstWhere('documentId', $data['service_document_id']);

        if (! $service) {
            return back()->withInput()->withErrors([
                'service_document_id' => 'Please choose a valid service.',
            ]);
        }

        $when = Carbon::parse($data['appointment_at']);

        // Limit ng plan ng salon: bilang ng appointments kada buwan
        $limit = $salon->subscriptions()->active()->latest('starts_at')->first()?->max_monthly_appointments;

        if ($limit !== null) {
            $count = $salon->appointments()
                ->where('status', '!=', 'cancelled')
                ->whereYear('appointment_at', $when->year)
                ->whereMonth('appointment_at', $when->month)
                ->count();

            if ($count >= $limit) {
                return back()->withInput()->withErrors([
                    'appointment_at' => 'This salon is fully booked for that month. Please choose another month or salon.',
                ]);
            }
        }

        // Iwasan ang double booking ng parehong staff sa parehong oras
        if (! empty($data['staff_id'])) {
            $taken = Appointment::where('staff_id', $data['staff_id'])
                ->where('appointment_at', $when)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($taken) {
                return back()->withInput()->withErrors([
                    'appointment_at' => 'That stylist is already booked at that time. Please choose another time.',
                ]);
            }
        }

        $appointment = $salon->appointments()->create([
            'user_id'                  => $request->user()->id,
            'staff_id'                 => $data['staff_id'] ?? null,
            'service_document_id'      => $service['documentId'],
            'service_name'             => $service['name'],
            'service_price'            => $service['price'],
            'service_duration_minutes' => $service['duration_minutes'],
            'appointment_at'           => $when,
            'notes'                    => $data['notes'] ?? null,
        ]);

        // Ang pagkabigo ng email ay hindi dapat makasira sa booking
        try {
            $request->user()->notify(new AppointmentBooked($appointment));
        } catch (\Throwable $e) {
            Log::warning('Failed to send booking email: ' . $e->getMessage());
        }

        return redirect()->route('appointments.index')
            ->with('success', "Your appointment at {$salon->name} has been booked! A confirmation email was sent.");
    }

    // I-cancel ang sariling appointment
    public function cancel(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        if (in_array($appointment->status, ['pending', 'confirmed'])) {
            $appointment->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Appointment cancelled.');
    }
}