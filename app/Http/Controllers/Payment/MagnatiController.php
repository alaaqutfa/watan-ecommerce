<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use Session;

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
        // ! Prod
         $send = "https://www.ipg-online.com/connect/gateway/processing";
        // ! Test
        // $send = "https://test.ipg-online.com/connect/gateway/processing";
        // Timezeone needs to be set
        return view('frontend.payment.magnati', compact('order', 'send'));
    }

    public function success(Request $request)
    {
        try {
            $order = Order::where('code', $request->oid)->orderBy('created_at', 'desc')->latest()->first();
            $payment = ["status" => "Success"];
            $payment_type = Session::get('payment_type');
            $paymentData = session()->get('payment_data');
            if(true) {
                if ($payment_type == 'cart_payment') {
                    return (new CheckoutController)->checkout_done(session()->get('combined_order_id'), json_encode($payment));
                }
                else if ($payment_type == 'order_re_payment') {
                    return (new CheckoutController)->orderRePaymentDone($paymentData, json_encode($payment));
                }
                else if ($payment_type == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done($paymentData, json_encode($payment));
                }
                else if ($payment_type == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
                }
                else if ($payment_type == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
                }
            } else {
                flash(translate('Payment failed'))->error();
                return redirect()->route('home');
            }
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
