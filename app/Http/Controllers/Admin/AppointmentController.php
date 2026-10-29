<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Notifications\AppointmentStatusChanged;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    public function index(Request $request)
    {
        $status = $request->query('status');

        $appointments = Appointment::with(['user', 'staff'])
            ->when(in_array($status, self::STATUSES), fn ($q) => $q->where('status', $status))
            ->latest('appointment_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'status'       => $status,
            'statuses'     => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUSES),
        ]);

                $appointment->update($data);

        // Magpapadala lang ng email kung talagang nagbago ang status
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