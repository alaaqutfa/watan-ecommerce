@php($price = convert_price_aed($data['amount']))
<html>

<head>
    <title>Telr Payment</title>
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
    </style>
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
        <div class="card invisible">
            <div class="card-body">
                <a id="telrPay" href="" class="btn btn-success invisible">{{ translate('Pay Now') }}</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        var combined_order_id = "{{ $data['combined_order_id'] }}";
        @isset($order)
            var DBdesc = "{{ $order->shipping_address }}";
        @else
            var DBdesc = "";
        @endisset
        var info = DBdesc.replaceAll("&quot;", '"');
        var data = {
            code: "{{ $data['combined_order_id'] }}",
            amount: "{{ $price }}",
            info,
            combined_order_id,
            user_id: "{{ $data['user_id'] }}",
            payment_type: "{{ $data['payment_type'] }}",
            package_id: "{{ $data['package_id'] }}",
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'accept': 'application/json',
            }
        });
        $.ajax({
            url: "{{ route('api.telr.get_token') }}",
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function(data) {
                if(data['status'] == 200) {
                    $('#telrPay').attr('href', data['data']['order']['url']);
                    setTimeout(() => {
                        document.getElementById("telrPay").click();
                    }, 1000);
                }
            },
            error: function(errMsg) {
                console.log(errMsg);
            }
        });
    </script>
</body>

</html>
