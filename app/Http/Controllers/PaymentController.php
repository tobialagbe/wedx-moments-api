<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class PaymentController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        // Assume a method to handle payment logic
        // Update event's payment status on success

        return response()->json(['message' => 'Payment successful', 'paid' => true]);
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        // Assuming Event model has a payment relationship or method to fetch payment history
        $paymentHistory = $event->paymentHistory();

        return response()->json($paymentHistory);
    }
}
