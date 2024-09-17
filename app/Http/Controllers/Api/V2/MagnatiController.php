<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MagnatiController extends Controller
{

    public function magnati(Request $request)
    {
        if(!env('MAGNATI_MODE')) {
            // ! Prod
            $send = "https://www.ipg-online.com/connect/gateway/processing";
            $storeId = env('MAGNATI_STORE_NAME_PROD');
            $sharedSecret = env('MAGNATI_SHARED_SECRET_PROD');
        } else {
            // ! Test
            $send = "https://test.ipg-online.com/connect/gateway/processing";
            $storeId = env('MAGNATI_STORE_NAME_TEST');
            $sharedSecret = env('MAGNATI_SHARED_SECRET_TEST');
        }
        $price = convert_price_aed($request->amount);
        date_default_timezone_set('Asia/Dubai');
        $dateTime = date("Y:m:d-H:i:s");
        $stringToHash = $storeId . $dateTime . $price . "784" . $sharedSecret;
        $ascii = bin2hex($stringToHash);
        $hash = hash("sha256", $ascii);
        $combined_order_id = $request->combined_order_id;
        $data = array();
        $data['payment_type'] = $request->payment_type;
        $data['order_id'] = $request->order_id;
        $data['user_id'] = $request->user_id;
        $data['package_id'] = $request->package_id;
        $session = json_encode($data);
        return view('frontend.payment.magnati_app', compact('storeId','combined_order_id', 'dateTime', 'hash', 'price', 'send', 'session'));
    }

    public function payment_success(Request $request)
    {
        try {
            if ($request->status == "APPROVED") {
                $session = json_decode($request->customParam_token);
                $payment = ["status" => "Success"];
                $payment_type = $session->payment_type;
                if ($payment_type == 'cart_payment') {
                    checkout_done($request->oid, json_encode($payment));
                } elseif ($payment_type == 'order_re_payment') {
                    order_re_payment_done($session->order_id, 'Magnati', json_encode($payment));
                } elseif ($payment_type == 'wallet_payment') {
                    wallet_payment_done($session->user_id, $request->chargetotal, 'Magnati', json_encode($payment));
                } elseif ($payment_type == 'seller_package_payment') {
                    seller_purchase_payment_done($session->user_id, $session->package_id, 'Magnati', json_encode($payment));
                } elseif ($payment_type == 'customer_package_payment') {
                    customer_purchase_payment_done($session->user_id, $session->package_id, 'Magnati', json_encode($payment));
                }
                return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
            } else {
                return response()->json(['result' => false, 'message' => translate("Payment is failed")]);
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => translate("Payment is failed")]);
        }
    }
    public function cancel()
    {
        return response()->json(['result' => false, 'message' => translate("Payment is cancelled")]);
    }
}
