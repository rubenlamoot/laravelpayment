<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>

        </style>
    </head>
    <body>
    @if(session('success_message'))
        <div class="alert alert-success">
            {{session('success_message')}}
        </div>
        @endif
    @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
            </ul>
        </div>
        @endif

        <div class="content">
            <form method="post" action="{{url('/checkout')}}" id="payment-form">
                @csrf
                <section>
                    <label for="amount">
                        <span class="input-label">Amount</span>
                        <span class="input-wrapper amount-wrapper">
                            <input type="tel" id="amount" name="amount" min="1" placeholder="amount"
                            value="10">
                        </span>
                    </label>
                    <div class="bt-drop-in-wrapper">
                        <div id="bt-dropin">

                        </div>

                    </div>
                </section>
                <input id="nonce" name="payment_method_nonce" type="hidden">
                <button class="button" type="submit"><span>Test Transaction</span></button>
            </form>
        </div>

        <script src="https://js.braintreegateway.com/web/dropin/1.16.0/js/dropin.min.js"></script>
        <script>
            var form = document.querySelector('#payment-form');
            var client_token = "{{$token}}";

            braintree.dropin.create({
                authorization: client_token,
                selector: '#bt-dropin',
                paypal: {
                    flow: 'vault'
                }
            }, function (createErr, instance) {
                if (createErr) {
                    console.log('Create Error', createErr);
                    return;
                }
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    instance.requestPaymentMethod(function (err, payload) {
                        if (err) {
                            console.log('Request Payment Method Error', err);
                            return;
                        }

                        // Add the nonce to the form and submit
                        document.querySelector('#nonce').value = payload.nonce;
                        form.submit();
                    });
                });
            });
        </script>

    </body>
</html>
