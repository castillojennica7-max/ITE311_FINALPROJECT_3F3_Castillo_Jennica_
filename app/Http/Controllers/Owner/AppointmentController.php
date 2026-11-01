<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Notifications\AppointmentStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    public function index(Request $request)
    {
        $salon  = $request->user()->salon;
        $status = $request->query('status');

        $appointments = $salon->appointments()
            ->with(['user', 'staff'])
            ->when(in_array($status, self::STATUSES), fn ($q) => $q->where('status', $status))
            ->latest('appointment_at')
            ->paginate(15)
            ->withQueryString();

        return view('owner.appointments.index', [
            'appointments' => $appointments,
            'status'       => $status,
            'statuses'     => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Sariling salon lang ang puwedeng galawin
        abort_unless($appointment->salon_id === $request->user()->salon->id, 403);

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUSES),
        ]);

        $appointment->update($data);

        if ($appointment->wasChanged('status')) {
            try {
                $appointment->user->notify(new AppointmentStatusChanged($appointment));
            } catch (\Throwable $e) {
                Log::warning('Failed to send status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Appointment status updated and the customer was notified.');
    }
}