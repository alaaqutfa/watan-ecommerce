<html>

<head>
    <title>Telr Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
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
    <div class="loadContainer">
        <center>
            <div class="loader"></div>
            <br>
            <br>
            <p style="width: 250px; margin: auto;">Don't close the tab. The payment is being processed . . .</p>
        </center>
    </div>
    <iframe src="" id="telrPay" style="display: none;width: 100%;height: 100vh;" frameborder="0"></iframe>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        var currency = "{{ $currency }}";
        if (currency == "$") {
            currency = "USD";
        } else if (currency == "aed") {
            currency = "AED";
        }
        var data = {
            code: "{{ $order->code }}",
            ammount: "{{ $order->grand_total }}",
            desc: "{{ $order->shipping_address }}",
            currency,
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'accept':'application/json',
            }
        });
        $.ajax({
            url: "{{ route('telr.get_token') }}",
            method: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function(data) {
                var res = JSON.parse(data);
                $('#telrPay').attr('src',res['order']['url']);
                $('.loadContainer').hide();
                $('#telrPay').show();
            },
            error: function(errMsg) {

            }
        });
    </script>
</body>

</html>
