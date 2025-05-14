<html>

<head>
    <title>Paytiko Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
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
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="name" placeholder="Username"
                            value="{{ $user->name }}" />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control" name="email" placeholder="Email"
                            value="{{ $user->email ?? '' }}" />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="text" class="form-control" name="phone" placeholder="Phone"
                            value="{{ $user->phone ?? '' }}" required />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fas fa-flag"></i>
                        </span>
                        <input type="hidden" name="country" />
                        <select name="country_id" class="form-select" required>
                            <option selected>{{ translate('Select your country') }}</option>
                            @foreach (get_active_countries() as $key => $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="div_state_id" class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                        <select name="state_id" class="form-select" required>
                            <option selected>{{ translate('State') }}</option>
                        </select>
                    </div>

                    <div id="div_city_id" class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-home"></i></span>
                        <select name="city_id" class="form-select">
                            <option selected>{{ translate('City') }}</option>
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fas fa-address-card"></i>
                        </span>
                        <input type="text" class="form-control" name="address"
                            placeholder="{{ translate('Address') }}" />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                        <input type="text" class="form-control" name="postal_code"
                            placeholder="{{ translate('Postal code') }}" />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        <input type="text" class="form-control" name="amount" placeholder="Amount" required />
                    </div>

                    <!-- Agree Boxs -->
                    <div class="pt-2rem py-2 fs-14">
                        <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox">
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('I agree to the') }}</span>
                            <span>{{ translate('The merchant/customer will seek all legal repercussions before the courts in the event of the use of any illegal payment method, including stolen or counterfeit cards or any other payment methods that violate special laws. Acceptance of the privacy terms is also binding on all Liverpool Airlines and the policies they include, including this clause.') }}</span>
                        </label>
                    </div>
                    <div class="pt-2rem py-2 fs-14">
                        <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox1">
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
        $('#div_state_id').hide();
        $('#div_city_id').hide();
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('#div_state_id').show();
            $('[name="state_id"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                url: "{{ route('get-state') }}",
                type: 'POST',
                data: {
                    country_id: country_id
                },
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj != '') {
                        $('[name="state_id"]').html(obj);
                    }
                }
            });
        }

        function get_city(state_id) {
            $('#div_city_id').show();
            $('[name="city_id"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                url: "{{ route('get-city') }}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function(response) {
                    var obj = JSON.parse(response);
                    if (obj != '') {
                        $('[name="city_id"]').html(obj);

                    }
                }
            });
        }

        function generateThreeDigitId() {
            const number = Math.floor(Math.random() * 1000); // من 0 إلى 999
            return number.toString().padStart(3, '0'); // يضيف أصفار في البداية إذا لزم
        }

        function quickPay() {
            var combined_order_id = generateThreeDigitId();
            var info = {
                name: $('[name="name"]').val(),
                email: $('[name="email"]').val(),
                phone: $('[name="phone"]').val(),
                country: $('[name="country_id"] option:selected').text(),
                state: $('[name="state_id"] option:selected').text(),
                city: $('[name="city_id"] option:selected').text(),
                address: $('[name="address"]').val(),
                postal_code: $('[name="postal_code"]').val(),
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
            if ($('#agree_checkbox').val() && $('#agree_checkbox1').val()) {
                quickPay();
            }
        });
    </script>
</body>

</html>
