<html>

<head>
    <title>Paytiko Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">
    @if (!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        @php
            $meta_image = uploaded_asset(get_setting('meta_image'));
        @endphp
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ $meta_image }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ $meta_image }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ $meta_image }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif
    <!-- Favicon -->
    @php
        $site_icon = uploaded_asset(get_setting('site_icon'));
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
            margin: auto;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #cashier-iframe-container {
            width: 100%;
            height: 100%;
            min-height: 700px;
        }
    </style>
    <script src="https://core.paytiko.com/cdn/js/sdk/paytiko-sdk.1.0.min.js"></script>
</head>

<body>
    <div class="container w-100 d-flex justify-content-center align-items-center flex-column" style="height: 100vh">
        <div class="loadContainer" style="display: none;">
            <center>
                <div class="loader"></div>
                <br>
                <br>
                <p style="width: 250px; margin: auto;">Don't close the tab. The payment is being processed . . .</p>
            </center>
        </div>
        <div id="payment-data" class="card w-100">
            <div class="card-body">
                <!-- Header Logo -->
                <div class="col-auto pl-0 pr-3 py-4 d-flex align-items-center">
                    <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                        @php
                            $header_logo = get_setting('header_logo');
                        @endphp
                        @if ($header_logo != null)
                            <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                                class="mw-100 h-30px h-md-40px" height="40">
                        @else
                            <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                class="mw-100 h-30px h-md-40px" height="40">
                        @endif
                    </a>
                </div>
                <form id="payForm" method="POST">
                @if (Auth::check() && count(Auth::user()->addresses))
                    @php($address = Auth::user()->addresses[0])
                    <div class="border mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                        <span class="d-flex p-3 aiz-megabox-elem border-0">
                                            <!-- Address -->
                                            <span class="flex-grow-1 pl-3 text-left">
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('Address') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ $address->address }}</span>
                                                </div>
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('Postal Code') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ $address->postal_code }}</span>
                                                </div>
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('City') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ optional($address->city)->name }}</span>
                                                </div>
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('State') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ optional($address->state)->name }}</span>
                                                </div>
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('Country') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ optional($address->country)->name }}</span>
                                                </div>
                                                <div class="row">
                                                    <span class="fs-14 text-secondary col-3">{{ translate('Phone') }}</span>
                                                    <span class="fs-14 text-dark fw-500 ml-2 col">{{ $address->phone }}</span>
                                                </div>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        <input type="number" class="form-control" name="amount" placeholder="{{ translate('Amount') }}" required />
                    </div>

                    <!-- Agree Boxs -->
                    <div class="pt-2rem py-2 fs-14">
                        <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox">
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('I agree to the') }}</span>
                        </label>
                        <a href="{{ route('terms') }}" class="fw-700">{{ translate('terms and conditions') }}</a>,
                        <a href="{{ route('returnpolicy') }}" class="fw-700">{{ translate('return policy') }}</a> &
                        <a href="{{ route('privacypolicy') }}" class="fw-700">{{ translate('privacy policy') }}</a>
                    </div>

                    <button id="payBtn" class="btn btn-success">{{ translate('Pay Now') }}</button>
                </form>
            </div>
        </div>
        <div id="payment-card" class="card" style="display: none;">
            <div class="card-body">
                <div id="cashier-iframe-container"></div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        function generateThreeDigitId() {
            const number = Math.floor(Math.random() * 1000); // من 0 إلى 999
            return number.toString().padStart(3, '0'); // يضيف أصفار في البداية إذا لزم
        }

        function quickPay() {
            var combined_order_id = generateThreeDigitId();
            var info = {
                name: '{{ $user->name }}',
                email: '{{ $user->email }}',
                phone: '{{ $address->phone }}',
                country: '{{ optional($address->country)->name }}',
                state: '{{ optional($address->state)->name }}',
                city: '{{ optional($address->city)->name }}',
                address: '{{ $address->address }}',
                postal_code: '{{ $address->postal_code }}',
            };
            var data = {
                amount: $('[name="amount"]').val(),
                info: JSON.stringify(info),
                combined_order_id
            };
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'accept': 'application/json',
                }
            });
            $.ajax({
                url: "{{ route('paytiko.get_token') }}",
                method: "POST",
                data: JSON.stringify(data),
                contentType: "application/json",
                success: function(data) {
                    $('#payment-data').fadeOut();
                    $('.loadContainer').fadeIn();
                    const SESSION_TOKEN = data['cashierSessionToken'];
                    const IFRAME_CONTAINER_SELECTOR = 'div#cashier-iframe-container'
                    const DISPLAY_MODE = PAYTIKO_CASHIER_DISPLAY_MODE.REDIRECT;
                    const LOCALE = 'en-US';
                    const ORDER_ID = combined_order_id;
                    const MODE = {{ env('PAYTIKO_MODE') }};
                    var ENVIRONMENT = "UAT";
                    if (MODE) {
                        ENVIRONMENT = 'PRODUCTION';
                    }
                    PaytikoSdk.cashier.invoke({
                        environment: ENVIRONMENT,
                        orderId: ORDER_ID,
                        sessionToken: SESSION_TOKEN,
                        iframeContainerSelector: IFRAME_CONTAINER_SELECTOR,
                        displayMode: DISPLAY_MODE,
                        locale: LOCALE
                    });
                },
                error: function(errMsg) {
                    console.log(errMsg);
                }
            });
        }

        $('#payForm').on('submit', function(e) {
            e.preventDefault();
            if ($('#agree_checkbox').val()) {
                quickPay();
            }
        });
    </script>
</body>

</html>
