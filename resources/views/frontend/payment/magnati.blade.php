<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
    <title>Magnati Payment</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" />
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js" />
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" />
    </script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data-10-year-range.min.js">
    </script>
    <script>
        /*
            5204740000002745 05/31 CVV:100
            4149011500000527 05/31 CVV:100
        */
        $(function() {
            setTimeout(() => {
                document.getElementById("submit").click();
            }, 2000);
        });
    </script>
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
                <form method="post" action="{{ $send }}">
                    <input type="hidden" name="txntype" value="sale">
                    <input type="hidden" name="timezone" value="Asia/Dubai"/>
                    <input type="hidden" name="txndatetime" value="{{ $dateTime }}"/>
                    <input type="hidden" name="hash_algorithm" value="SHA256"/>
                    <input type="hidden" name="hash" value="{{ $hash }}"/>
                    <input type="hidden" name="storename" value="{{ $storeId }}"/>
                    <input type="hidden" name="checkoutoption" value="combinedpage"/>
                    <input type="hidden" name="authenticateTransaction" value="true" />
                    <input type="hidden" name="chargetotal" value="{{ $price }}"/>
                    <input type="hidden" name="currency" value="784"/>
                    <input type="hidden" name="mobileMode" value="true"/>
                    <input type="text" name="oid" value="{{ $order->combined_order_id }}" />
                    <input type="hidden" name="responseSuccessURL" value="{{ route('magnati.success') }}"/>
                    <input type="hidden" name="responseFailURL" value="{{ route('magnati.cancel') }}"/>
                    <input type="hidden" name="transactionNotificationURL" value="https://dev-services.hubdev.wine/api-json/magnati?token=2643ihdfuig" />
                    <input type="submit" id="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>
</body>

</html>

