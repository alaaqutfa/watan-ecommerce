<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class TelrController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay(Request $request)
    {
        $user = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_status', '=', 'unpaid']])->orderBy('created_at', 'desc')->latest()->first();
        $currency = currency_symbol();
        return view('frontend.payment.telr', compact('order', 'currency'));
    }

    public function create_checkout_session(Request $request)
    {
        require_once 'vendor/autoload.php';
        $client = new \GuzzleHttp\Client();
        $storeId = env('TELR_STORE_ID');
        $authKey = env('TELR_AUTHENTICATION_KEY');
        $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
            'body' => '{"method":"create","store":' . $storeId . ',"authkey":"' . $authKey . '","framed":1,"order":{"cartid":"' . $request->code . '","test":"1","amount":"' . $request->ammount . '","currency":"' . $request->currency . '","description":"' . $request->desc . '"},"return":{"authorised":"https://watan.website/telr/checkout-payment-detail","declined":"https://watan.website/telr/cancel","cancelled":"https://watan.website/telr/cancel"}}',
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ],
        ]);
        $body = json_decode($response->getBody());
        if(isset($body->order)){
            session(['ref' => $body->order->ref]);
        }
        echo $response->getBody();
    }

    public function checkout_payment_detail(Request $request)
    {
        require_once('vendor/autoload.php');
        $client = new \GuzzleHttp\Client();
        $storeId = env('TELR_STORE_ID');
        $authKey = env('TELR_AUTHENTICATION_KEY');
        $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
        'body' => '{"method":"check","store":' . $storeId . ',"authkey":"' . $authKey . '","order":{"ref":"' . $request->ref . '"}}',
        'headers' => [
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
        ],
        ]);
        echo $response->getBody();
    }

    public function success(Request $request)
    {
        $user = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_status', '=', 'unpaid']])->orderBy('created_at', 'desc')->latest()->first();
        $currency = currency_symbol();
        $ref = $request->session()->get('ref');
        return view('frontend.payment.telrsuccess', compact('order', 'currency','ref'));
    }

    public function cancel(Request $request)
    {
        flash(translate('Payment is cancelled'))->error();
        return redirect()->route('home');
    }
}
