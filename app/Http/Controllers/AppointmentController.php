<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Staff;
use App\Services\StrapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    // Listahan ng appointments ng naka-login na user
    public function index(Request $request)
    {
        $appointments = $request->user()
            ->appointments()
            ->with('staff')
            ->latest('appointment_at')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    // Booking form
    public function create(StrapiService $strapi)
    {
        $services = $strapi->services();

        return view('appointments.create', [
            'services'  => $services ?? [],
            'cmsOnline' => $services !== null,
            'staff'     => Staff::orderBy('name')->get(),
        ]);
    }

    // I-save ang booking
    public function store(Request $request, StrapiService $strapi)
    {
        $data = $request->validate([
            'service_document_id' => 'required|string',
            'staff_id'            => 'nullable|exists:staff,id',
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

        $request->user()->appointments()->create([
            'staff_id'                 => $data['staff_id'] ?? null,
            'service_document_id'      => $service['documentId'],
            'service_name'             => $service['name'],
            'service_price'            => $service['price'],
            'service_duration_minutes' => $service['duration_minutes'],
            'appointment_at'           => $when,
            'notes'                    => $data['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Your appointment has been booked!');
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