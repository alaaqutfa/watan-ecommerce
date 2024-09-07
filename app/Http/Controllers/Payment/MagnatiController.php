<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class MagnatiController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        $user = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_status', '=', 'unpaid']], ['payment_type', '=', 'magnati'])->orderBy('created_at', 'desc')->latest()->first();
        $currency = currency_symbol();
        // ! Prod
        //  $send = "https://www.ipg-online.com/connect/gateway/processing";
        // ! Test
        $send = "https://test.ipg-online.com/connect/gateway/processing";
        // Timezeone needs to be set
        return view('frontend.payment.magnati', compact('order', 'currency', 'send'));
    }

    public function success(Request $request)
    {
        try {
            $order = Order::where('code', $request->oid)->orderBy('created_at', 'desc')->latest()->first();
            $payment = ["status" => "Success"];
            return (new CheckoutController)->checkout_done($order->combined_order_id, json_encode($payment));
        }
        catch (\Exception $e) {
            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }

    public function cancel()
    {
        flash(translate('Payment is cancelled'))->error();
        return redirect()->route('home');
    }
}
