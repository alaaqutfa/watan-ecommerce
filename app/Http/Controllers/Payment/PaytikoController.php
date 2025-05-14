<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPackageController;
use App\Models\Order;
use Illuminate\Http\Request;
use Session;

class PaytikoController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function pay()
    {
        $user  = auth()->user();
        $order = Order::where([['user_id', '=', $user->id], ['payment_type', '=', 'paytiko'], ['payment_status', '=', 'unpaid']])->orderBy('created_at', 'desc')->latest()->first();
        return view('frontend.payment.paytiko', compact('order'));
    }

    public function quickPay()
    {
        $user  = auth()->user();
        return view('frontend.payment.paytiko_quickPay', compact('user'));
    }

    public function create_checkout_session(Request $request)
    {
        $reqInfo  = $request->info;
        $JDONinfo = json_decode($reqInfo, true);
        $info     = [];
        if (isset($JDONinfo)) {
            foreach ($JDONinfo as $key => $value) {
                $info[$key] = $value;
            }
            $desc = "Name: " . $info['name'] . ". \n Email: " . $info['email'] . " \n Phone: " . $info['phone'];
        } else {
            $desc = "";
        }
        require_once 'vendor/autoload.php';
        $client = new \GuzzleHttp\Client();
        $mode   = env('PAYTIKO_MODE');
        if ($mode) {
            $url       = "https://core.paytiko.com/api/sdk/checkout";
            $secretKey = env('PAYTIKO_MERCHANT_SECRET_KEY');
        } else {
            $url       = "https://uat-core.paytiko.com/api/sdk/checkout";
            $secretKey = "Ikab-i1ElE4i";
        }
        $countryCodes = [
            "Afghanistan"                      => "AF",
            "Albania"                          => "AL",
            "Algeria"                          => "DZ",
            "Andorra"                          => "AD",
            "Angola"                           => "AO",
            "Antigua and Barbuda"              => "AG",
            "Argentina"                        => "AR",
            "Armenia"                          => "AM",
            "Australia"                        => "AU",
            "Austria"                          => "AT",
            "Azerbaijan"                       => "AZ",
            "Bahamas"                          => "BS",
            "Bahrain"                          => "BH",
            "Bangladesh"                       => "BD",
            "Barbados"                         => "BB",
            "Belarus"                          => "BY",
            "Belgium"                          => "BE",
            "Belize"                           => "BZ",
            "Benin"                            => "BJ",
            "Bhutan"                           => "BT",
            "Bolivia"                          => "BO",
            "Bosnia and Herzegovina"           => "BA",
            "Botswana"                         => "BW",
            "Brazil"                           => "BR",
            "Brunei"                           => "BN",
            "Bulgaria"                         => "BG",
            "Burkina Faso"                     => "BF",
            "Burundi"                          => "BI",
            "Cabo Verde"                       => "CV",
            "Cambodia"                         => "KH",
            "Cameroon"                         => "CM",
            "Canada"                           => "CA",
            "Central African Republic"         => "CF",
            "Chad"                             => "TD",
            "Chile"                            => "CL",
            "China"                            => "CN",
            "Colombia"                         => "CO",
            "Comoros"                          => "KM",
            "Congo (Congo-Brazzaville)"        => "CG",
            "Costa Rica"                       => "CR",
            "Croatia"                          => "HR",
            "Cuba"                             => "CU",
            "Cyprus"                           => "CY",
            "Czech Republic"                   => "CZ",
            "Democratic Republic of the Congo" => "CD",
            "Denmark"                          => "DK",
            "Djibouti"                         => "DJ",
            "Dominica"                         => "DM",
            "Dominican Republic"               => "DO",
            "Ecuador"                          => "EC",
            "Egypt"                            => "EG",
            "El Salvador"                      => "SV",
            "Equatorial Guinea"                => "GQ",
            "Eritrea"                          => "ER",
            "Estonia"                          => "EE",
            "Eswatini"                         => "SZ",
            "Ethiopia"                         => "ET",
            "Fiji"                             => "FJ",
            "Finland"                          => "FI",
            "France"                           => "FR",
            "Gabon"                            => "GA",
            "Gambia"                           => "GM",
            "Georgia"                          => "GE",
            "Germany"                          => "DE",
            "Ghana"                            => "GH",
            "Greece"                           => "GR",
            "Grenada"                          => "GD",
            "Guatemala"                        => "GT",
            "Guinea"                           => "GN",
            "Guinea-Bissau"                    => "GW",
            "Guyana"                           => "GY",
            "Haiti"                            => "HT",
            "Honduras"                         => "HN",
            "Hungary"                          => "HU",
            "Iceland"                          => "IS",
            "India"                            => "IN",
            "Indonesia"                        => "ID",
            "Iran"                             => "IR",
            "Iraq"                             => "IQ",
            "Ireland"                          => "IE",
            "Israel"                           => "IL",
            "Italy"                            => "IT",
            "Jamaica"                          => "JM",
            "Japan"                            => "JP",
            "Jordan"                           => "JO",
            "Kazakhstan"                       => "KZ",
            "Kenya"                            => "KE",
            "Kiribati"                         => "KI",
            "Kuwait"                           => "KW",
            "Kyrgyzstan"                       => "KG",
            "Laos"                             => "LA",
            "Latvia"                           => "LV",
            "Lebanon"                          => "LB",
            "Lesotho"                          => "LS",
            "Liberia"                          => "LR",
            "Libya"                            => "LY",
            "Liechtenstein"                    => "LI",
            "Lithuania"                        => "LT",
            "Luxembourg"                       => "LU",
            "Madagascar"                       => "MG",
            "Malawi"                           => "MW",
            "Malaysia"                         => "MY",
            "Maldives"                         => "MV",
            "Mali"                             => "ML",
            "Malta"                            => "MT",
            "Marshall Islands"                 => "MH",
            "Mauritania"                       => "MR",
            "Mauritius"                        => "MU",
            "Mexico"                           => "MX",
            "Micronesia"                       => "FM",
            "Moldova"                          => "MD",
            "Monaco"                           => "MC",
            "Mongolia"                         => "MN",
            "Montenegro"                       => "ME",
            "Morocco"                          => "MA",
            "Mozambique"                       => "MZ",
            "Myanmar (Burma)"                  => "MM",
            "Namibia"                          => "NA",
            "Nauru"                            => "NR",
            "Nepal"                            => "NP",
            "Netherlands"                      => "NL",
            "New Zealand"                      => "NZ",
            "Nicaragua"                        => "NI",
            "Niger"                            => "NE",
            "Nigeria"                          => "NG",
            "North Korea"                      => "KP",
            "North Macedonia"                  => "MK",
            "Norway"                           => "NO",
            "Oman"                             => "OM",
            "Pakistan"                         => "PK",
            "Palau"                            => "PW",
            "Palestine State"                  => "PS",
            "Panama"                           => "PA",
            "Papua New Guinea"                 => "PG",
            "Paraguay"                         => "PY",
            "Peru"                             => "PE",
            "Philippines"                      => "PH",
            "Poland"                           => "PL",
            "Portugal"                         => "PT",
            "Qatar"                            => "QA",
            "Romania"                          => "RO",
            "Russia"                           => "RU",
            "Rwanda"                           => "RW",
            "Saint Kitts and Nevis"            => "KN",
            "Saint Lucia"                      => "LC",
            "Saint Vincent and the Grenadines" => "VC",
            "Samoa"                            => "WS",
            "San Marino"                       => "SM",
            "Sao Tome and Principe"            => "ST",
            "Saudi Arabia"                     => "SA",
            "Senegal"                          => "SN",
            "Serbia"                           => "RS",
            "Seychelles"                       => "SC",
            "Sierra Leone"                     => "SL",
            "Singapore"                        => "SG",
            "Slovakia"                         => "SK",
            "Slovenia"                         => "SI",
            "Solomon Islands"                  => "SB",
            "Somalia"                          => "SO",
            "South Africa"                     => "ZA",
            "South Korea"                      => "KR",
            "South Sudan"                      => "SS",
            "Spain"                            => "ES",
            "Sri Lanka"                        => "LK",
            "Sudan"                            => "SD",
            "Suriname"                         => "SR",
            "Sweden"                           => "SE",
            "Switzerland"                      => "CH",
            "Syria"                            => "SY",
            "Taiwan"                           => "TW",
            "Tajikistan"                       => "TJ",
            "Tanzania"                         => "TZ",
            "Thailand"                         => "TH",
            "Timor-Leste"                      => "TL",
            "Togo"                             => "TG",
            "Tonga"                            => "TO",
            "Trinidad and Tobago"              => "TT",
            "Tunisia"                          => "TN",
            "Turkey"                           => "TR",
            "Turkmenistan"                     => "TM",
            "Tuvalu"                           => "TV",
            "Uganda"                           => "UG",
            "Ukraine"                          => "UA",
            "United Arab Emirates"             => "AE",
            "United Kingdom"                   => "GB",
            "United States"                    => "US",
            "Uruguay"                          => "UY",
            "Uzbekistan"                       => "UZ",
            "Vanuatu"                          => "VU",
            "Vatican City"                     => "VA",
            "Venezuela"                        => "VE",
            "Vietnam"                          => "VN",
            "Yemen"                            => "YE",
            "Zambia"                           => "ZM",
            "Zimbabwe"                         => "ZW",
        ];
        $timestamp    = time();
        $rawSignature = "{$info['email']};{$timestamp};{$secretKey}";
        $signature    = hash('sha256', $rawSignature);

        $response = $client->request('POST', $url,
            [
                'body'    => '
            {
                "firstName":"' . $info['name'] . '",
                "email":"' . $info['email'] . '",
                "countryCode":"' . $countryCodes[$info['country']] . '",
                "currency":"USD",
                "lockedAmount":' . $request->amount . ',
                "orderId":"' . $request->combined_order_id . '",
                "phone":"' . $info['phone'] . '",
                "city":"' . $info['state'] . '",
                "street":"' . $info['address'] . '",
                "region":"' . $info['city'] . '",
                "zipCode":"' . $info['postal_code'] . '",
                "signature":"' . $signature . '",
                "isPayout":false,
                "timestamp":' . $timestamp . ',
            }',
                'headers' => [
                    'X-Merchant-Secret' => $secretKey,
                    'accept'            => '*/*',
                    'Accept-Encoding'   => 'gzip, deflate, br',
                    'Content-Type'      => 'application/json; charset=utf-8',
                    'User-Agent'        => 'SDK API',
                ],
            ]);
        $body = json_decode($response->getBody(), true);
        return response()->json($body);
    }

    public function success(Request $request)
    {
        try {
            $payment      = ["status" => "Success"];
            $ref          = Session::get('ref');
            $payment_type = Session::get('payment_type');
            $paymentData  = session()->get('payment_data');
            require_once 'vendor/autoload.php';
            $client   = new \GuzzleHttp\Client();
            $storeId  = env('TELR_STORE_ID');
            $authKey  = env('TELR_AUTHENTICATION_KEY');
            $response = $client->request('POST', 'https://secure.telr.com/gateway/order.json', [
                'body'    => '{"method":"check","store":"' . $storeId . '","authkey":"' . $authKey . '","order":{"ref":"' . $ref . '"}}',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'accept'       => 'application/json',
                ],
            ]);
            $data       = json_decode($response->getBody());
            $statusText = $data->order->status->text;
            $statusCode = $data->order->status->code;
            if ($statusText == 'Paid' && $statusCode == 3) {
                if ($payment_type == 'cart_payment') {
                    return (new CheckoutController)->checkout_done(session()->get('combined_order_id'), json_encode($payment));
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
            } else {
                flash(translate('Payment failed'))->error();
                return redirect()->route('home');
            }
        } catch (\Exception $e) {
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
