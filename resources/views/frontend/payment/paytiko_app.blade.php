<html>

<head>
    <title>Paytiko Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        <div class="loadContainer">
            <center>
                <div class="loader"></div>
                <br>
                <br>
                <p style="width: 250px; margin: auto;">Don't close the tab. The payment is being processed . . .</p>
            </center>
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
        var combined_order_id = {{ $order->combined_order_id }};
        var DBdesc = "{{ $order->shipping_address }}";
        var info = DBdesc.replaceAll("&quot;", '"');
        var data = {
            code: "{{ $order->code }}",
            amount: {{ $data['amount'] }},
            info,
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
            url: "{{ route('api.paytiko.get_token') }}",
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function(data) {
                const SESSION_TOKEN = data['cashierSessionToken'];
                const IFRAME_CONTAINER_SELECTOR = 'div#cashier-iframe-container'
                const DISPLAY_MODE = PAYTIKO_CASHIER_DISPLAY_MODE.REDIRECT;
                const LOCALE = 'en-US';
                const ORDER_ID = combined_order_id;
                const MODE = {{ env('PAYTIKO_MODE') }};
                var ENVIRONMENT = "UAT";
                if(MODE) {
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
                $('.loadContainer').fadeOut();
                $('#payment-card').fadeIn();
            },
            error: function(errMsg) {
                console.log(errMsg);
            }
        });
    </script>
</body>

</html>
