<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPackageController;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;

class ZiinaController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        $user = auth()->user();

        // البحث عن آخر طلب غير مدفوع عن طريق Ziina
        $order = Order::where('user_id', $user->id)
                      ->where('payment_status', 'unpaid')
                      ->where('payment_type', 'ziina')
                      ->latest()
                      ->first();

        // التحقق مما إذا كان هناك طلب
        if (!$order) {
            return redirect()->back()->with('error', 'لم يتم العثور على طلب غير مدفوع.');
        }
        $state = "order_" . $order->combined_order_id;
        $callbackUrl = route('ziina.callback');

        return redirect()->away("https://auth.ziina.com/oidc/auth?client_id=test&response_type=code&redirect_uri={$callbackUrl}&scope=read_account+write_payment_intents&state={$state}");
    }
    public function callback(Request $request)
    {
        $user  = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_status', '=', 'unpaid']], ['payment_type', '=', 'ziina'])->orderBy('created_at', 'desc')->latest()->first();

        if (! env('ZIINA_MODE')) {
            // ! Prod
            $testMode = false;
        } else {
            // ! Test
            $testMode = true;
        }

        $aedPrice = floatval(convert_price_aed($order->grand_total));
        $price    = intval(round($aedPrice * 100));
        date_default_timezone_set('Asia/Dubai');
        $expiry = Carbon::now()->addMinutes(30)->timestamp;
        // Timezeone needs to be set
        return view('frontend.payment.ziina', compact('order', 'price', 'expiry', 'testMode'));
    }

    public function success(Request $request)
    {
        try {
            $payment      = ["status" => "Success"];
            $payment_type = Session::get('payment_type');
            $paymentData  = session()->get('payment_data');
            if ($payment_type == 'cart_payment') {
                return (new CheckoutController)->checkout_done($request->oid, json_encode($payment));
            } else if ($payment_type == 'order_re_payment') {
                return (new CheckoutController)->orderRePaymentDone($paymentData, json_encode($payment));
            } else if ($payment_type == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done($paymentData, json_encode($payment));
            } else if ($payment_type == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
            }
            // else if ($payment_type == 'seller_package_payment') {
            //     return (new SellerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
            // }
        } catch (\Exception $e) {
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
