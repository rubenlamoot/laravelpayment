<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);
    $token = $gateway->ClientToken()->generate();
    return view('welcome',[
        'token' => $token
    ]);
});

Route::get('/hosted', function () {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);
    $token = $gateway->ClientToken()->generate();
    return view('hosted', [
        'token' => $token
    ]);
});


Route::post('/checkout', function (){
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

    $amount = $_POST["amount"];
    $nonce = $_POST["payment_method_nonce"];

    $result = $gateway->transaction()->sale([
        'amount' => $amount,
        'paymentMethodNonce' => $nonce,
        'options' => [
            'submitForSettlement' => true
        ]
    ]);

    if ($result->success || !is_null($result->transaction)) {
        $transaction = $result->transaction;
//        header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
    } else {
        $errorString = "";

        foreach($result->errors->deepAll() as $error) {
            $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
        }

//        $_SESSION["errors"] = $errorString;
//        header("Location: " . $baseUrl . "index.php");

        return back()->withErrors('An error occured with the message; ' .$result->message);
    }
});
