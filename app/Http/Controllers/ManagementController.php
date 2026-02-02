<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;

class ManagementController extends Controller
{
    public function dashboard()
    {
        $bookings = Booking::latest()->get();
        $payments = Payment::latest()->get();

        $stats = [
            'pending'  => Booking::where('status', 'pending')->count(),
            'approved' => Booking::where('status', 'approved')->count(),
            'rejected' => Booking::where('status', 'rejected')->count(),
            'payments' => (float) Payment::sum('amount'),
        ];

        return view('Management.ManagementDashboard', compact('bookings', 'payments', 'stats'));
    }

    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'approved']);

        return back()->with('success', 'Booking approved!');
    }

    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'denied']);

        return back()->with('success', 'Booking rejected!');
    }
}
