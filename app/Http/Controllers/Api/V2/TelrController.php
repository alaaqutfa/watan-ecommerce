<?php

namespace App\Http\Controllers\Api\V2;

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
        $order = Order::where([['user_id', '=', $request->user_id], ['payment_status', '=', 'unpaid'],['combined_order_id','=',$request->combined_order_id]])->orderBy('created_at', 'desc')->latest()->first();
        $data['payment_type'] = $request->payment_type;
        $data['combined_order_id'] = $request->combined_order_id;
        $data['amount'] = $request->amount;
        $data['user_id'] = $request->user_id;
        $data['package_id'] = 0;
        return view('frontend.payment.telr_app', compact('order','data'));
    }

    public function create_checkout_session(Request $request)
    {
        $reqInfo = $request->info;
        $JDONinfo = json_decode($reqInfo,true);
        $info = array();
        if(isset($JDONinfo)){
            foreach ($JDONinfo as $key => $value) {
                $info[$key] = $value;
            }
            $desc = "Name: ".$info['name'].". \n Email: ".$info['email']." \n Phone: ".$info['phone'];
        } else {
            $desc = "";
        }
        require_once 'vendor/autoload.php';
        $client = new \GuzzleHttp\Client();
        $storeId = env('TELR_STORE_ID');
        $authKey = env('TELR_AUTHENTICATION_KEY');
        $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
            'body' => '{"method":"create","store":"' . $storeId . '","authkey":"' . $authKey . '","framed":"0","order":{"cartid":"' . $request->code . '","test":"'.env('TELR_MODE').'","amount":"' . $request->amount . '","currency":"AED","description":"' . $desc . '"},"return":{"authorised":"'.route("api.telr.success").'","declined":"'.route("api.telr.cancel").'","cancelled":"'.route("api.telr.cancel").'"},"extra":{"combined_order_id":"'.$request->combined_order_id.'"}}',
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ],
        ]);
        $data = array();
        $data['payment_type'] = $request->payment_type;
        $data['combined_order_id'] = $request->combined_order_id;
        $data['order_id'] = $request->order_id;
        $data['amount'] = $request->amount;
        $data['user_id'] = $request->user_id;
        $data['package_id'] = $request->package_id;
        $body = json_decode($response->getBody());
        if(isset($body->order)){
            $session = [
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'AED',
                            'product_data' => [
                                'name' => "Payment"
                            ],
                            'unit_amount' => $request->amount,
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'client_reference_id' => json_encode($data),
                'ref' => $body->order->ref,
                'success_url' => route('api.telr.success'),
                'cancel_url' => route('api.telr.cancel'),
            ];
            session(['client_reference_id' => $session]);
        }
        return response()->json(['data' => $body, 'status' => 200]);
    }

    public function payment_success(Request $request)
    {
        try {
            $session = Session::get('client_reference_id');
            $payment = ["status" => "Success"];
            $decoded_reference_data = json_decode($session['client_reference_id']);
            $payment_type = $decoded_reference_data->payment_type;
            $ref = $session['ref'];
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
                    checkout_done($decoded_reference_data->combined_order_id, json_encode($payment));
                } elseif ($payment_type == 'order_re_payment') {
                    order_re_payment_done($decoded_reference_data->order_id, 'Telr', json_encode($payment));
                } elseif ($payment_type == 'wallet_payment') {
                    wallet_payment_done($decoded_reference_data->user_id, $decoded_reference_data->amount, 'Telr', json_encode($payment));
                } elseif ($payment_type == 'seller_package_payment') {
                    seller_purchase_payment_done($decoded_reference_data->user_id, $decoded_reference_data->package_id, 'Telr', json_encode($payment));
                } elseif ($payment_type == 'customer_package_payment') {
                    customer_purchase_payment_done($decoded_reference_data->user_id, $decoded_reference_data->package_id, 'Telr', json_encode($payment));
                }
                return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
            } else {
                return response()->json(['result' => false, 'message' => translate("Payment is failed")]);
            }
        }
        catch (\Exception $e) {
            dd($e);
            return response()->json(['result' => false, 'message' => translate("Payment is failed with error")]);
        }
    }

    public function cancel(Request $request)
    {
        return response()->json(['result' => false, 'message' => translate("Payment is cancelled")]);
    }
}
