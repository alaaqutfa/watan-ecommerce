<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\SellerPackageController;
use Session;

class TelrController extends Controller
{
    public function telr(Request $request)
    {
        $user = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_status', '=', 'unpaid']])->orderBy('created_at', 'desc')->latest()->first();
        return view('frontend.payment.telr', compact('order'));
    }

    public function create_checkout_session(Request $request)
    {
        $reqInfo = $request->info;
        $JDONinfo = json_decode($reqInfo,true);
        $info = array();
        foreach ($JDONinfo as $key => $value) {
            $info[$key] = $value;
        }
        $desc = "Name: ".$info['name'].". \n Email: ".$info['email']." \n Phone: ".$info['phone'];
        require_once 'vendor/autoload.php';
        $client = new \GuzzleHttp\Client();
        $storeId = env('TELR_STORE_ID');
        $authKey = env('TELR_AUTHENTICATION_KEY');
        // ! Prod
        $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
            'body' => '{"method":"create","store":"' . $storeId . '","authkey":"' . $authKey . '","framed":"0","order":{"cartid":"' . $request->code . '","test":"0","amount":"' . $request->ammount . '","currency":"AED","description":"' . $desc . '"},"return":{"authorised":"'.route("telr.success").'","declined":"'.route("telr.cancel").'","cancelled":"'.route("telr.cancel").'"},"extra":{"combined_order_id":"'.$request->combined_order_id.'"}}',
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ],
        ]);
        // ! Test
        // $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
        //     'body' => '{"method":"create","store":' . $storeId . ',"authkey":"' . $authKey . '","framed":1,"order":{"cartid":"' . $request->code . '","test":"1","amount":"' . $request->ammount . '","currency":"AED","description":"' . $desc . '"},"return":{"authorised":"'.route("telr.success").'","declined":"'.route("telr.cancel").'","cancelled":"'.route("telr.cancel").'"},"customer":{"email":"'.$info["email"].'","phone":"'.$info["phone"].'","name":{"title":"'.$info["name"].'","forenames":"'.$info["name"].'","surname":"'.$info["name"].'"},"address":{"line1":"'.$info["address"].'","line2":"'.$info["address"].'","line3":"'.$info["address"].'","city":"'.$info["city"].'","state":"'.$info["city"].'","country":"'.$info["country"].'","areacode":"'.$info["postal_code"].'"},"ref":"'.$request->combined_order_id.'"},"extra":{"combined_order_id":"'.$request->combined_order_id.'"}}',
        //     'headers' => [
        //         'Content-Type' => 'application/json',
        //         'accept' => 'application/json',
        //     ],
        // ]);
        $body = json_decode($response->getBody());
        if(isset($body->order)){
            session(['ref' => $body->order->ref]);
        }
        echo $response->getBody();
    }

    public function payment_success(Request $request)
    {
        try {
            $payment = ["status" => "Success"];
            $ref = Session::get('ref');
            $payment_type = Session::get('payment_type');
            $paymentData = session()->get('payment_data');
            require_once('vendor/autoload.php');
            $client = new \GuzzleHttp\Client();
            $storeId = env('TELR_STORE_ID');
            $authKey = env('TELR_AUTHENTICATION_KEY');
            $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
            'body' => '{"method":"check","store":"' . $storeId . '","authkey":"' . $authKey . '","order":{"ref":"' . $ref . '"}}',
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ],
            ]);
            $data = json_decode($response->getBody());
            $statusText = $data->order->status->text;
            $statusCode = $data->order->status->code;
            if($statusText == 'Paid' && $statusCode == 3) {
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
        }
        catch (\Exception $e) {
            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }

    public function cancel(Request $request)
    {
        flash(translate('Payment is cancelled'))->error();
        return redirect()->route('home');
    }
}
