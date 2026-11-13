<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Salon;
use App\Models\SalonService;
use App\Notifications\AppointmentBooked;
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
            ->with(['staff', 'salon', 'transaction'])
            ->latest('appointment_at')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    // Hakbang 1: pumili ng salon (mga salon na may aktibong subscription lang)
    public function create(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $salons = Salon::active()
            ->when($q !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('address', 'like', "%{$q}%")
            ))
            ->orderBy('name')
            ->get();

        return view('appointments.create', compact('salons', 'q'));
    }

    // Hakbang 2: booking form para sa napiling salon
    public function book(Request $request, Salon $salon)
    {
        abort_unless($salon->isActive(), 404);

        return view('appointments.book', [
            'salon'    => $salon,
            'services' => $salon->services()->where('is_active', true)->orderBy('category')->orderBy('name')->get(),
            'staff'    => $salon->staff()->orderBy('name')->get(),
            'selected' => (int) $request->query('service'),
        ]);
    }

    // I-save ang booking
    public function store(Request $request, Salon $salon)
    {
        abort_unless($salon->isActive(), 404);

        $data = $request->validate([
            // Serbisyo ng napiling salon lang ang puwede
            'salon_service_id' => ['required', Rule::exists('salon_services', 'id')
                ->where('salon_id', $salon->id)
                ->where('is_active', true)],
            // Staff ng napiling salon lang ang puwede
            'staff_id'         => ['nullable', Rule::exists('staff', 'id')->where('salon_id', $salon->id)],
            'appointment_at'   => 'required|date|after:now',
            'notes'            => 'nullable|string|max:500',
        ]);

        // Ang presyo ay kinukuha sa database ng salon, hindi sa ipinadala ng browser
        $service = SalonService::findOrFail($data['salon_service_id']);
        $when    = Carbon::parse($data['appointment_at']);

        // Bukas ba ang salon sa oras na iyon?
        if (! $salon->isOpenAt($when, (int) $service->duration_minutes)) {
            return back()->withInput()->withErrors([
                'appointment_at' => $salon->dayHoursLabel($when) . ' Please choose a time within opening hours, with enough time to finish the service before closing.',
            ]);
        }

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

        // Iwasan ang magkapatong na booking ng parehong staff
        if (! empty($data['staff_id'])) {
            $end = $when->copy()->addMinutes((int) $service->duration_minutes);

            $taken = Appointment::where('staff_id', $data['staff_id'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereBetween('appointment_at', [$when->copy()->startOfDay(), $when->copy()->endOfDay()])
                ->get()
                ->contains(function ($a) use ($when, $end) {
                    $aEnd = $a->appointment_at->copy()->addMinutes((int) $a->service_duration_minutes);

                    return $a->appointment_at < $end && $aEnd > $when;
                });

            if ($taken) {
                return back()->withInput()->withErrors([
                    'appointment_at' => 'That stylist is already booked at that time. Please choose another time or stylist.',
                ]);
            }
        }

        $appointment = $salon->appointments()->create([
            'user_id'                  => $request->user()->id,
            'staff_id'                 => $data['staff_id'] ?? null,
            'service_document_id'      => 'svc-' . $service->id,
            'service_name'             => $service->name,
            'service_price'            => $service->price,
            'service_duration_minutes' => $service->duration_minutes,
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

        if (in_array($appointment->status, ['pending', 'confirmed']) && ! $appointment->transaction()->exists()) {
            $appointment->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Appointment cancelled.');
    }
}
