<?php

/**
 * Sample script to sign and send a request to the Walmart Affiliate Marketing API.
 *
 * https://walmart.io/docs/affiliate/introduction
 *
 * Usage:
 *   1. Fill out the required variables at the top of this script.
 *   2. Install dependencies via composer install.
 *   3. Run via php index.php or by opening this script in a browser.
 *
 * Acknowledgements:
 *   Abiral Neupane at https://stackoverflow.com/a/62847241/1120652
 *   @gorenstein at https://gitter.im/IO-support/community?at=5f2e5d2051bb7d3380d9b58b
 */

include './vendor/autoload.php';

use \GuzzleHttp\Client;

/**
 * Create an account at Walmart.io. Then create an application. Then follow the
 * steps at https://walmart.io/key-tutorial to create a set of keys. Upload
 * the public key (its contents start with BEGIN PUBLIC KEY) into the
 * production environment of the application that you created.
 */
$consumer_id = 'Paste here the consumer id that you will see in your application details after pasting the public key';
$key = 'Paste here the private key. Full, including BEGIN and END PRIVATE KEY lines.';

$version = '1';
$timestamp = round(microtime(true) * 1000);
$message = $consumer_id . "\n" . $timestamp . "\n" . $version . "\n";

$pkeyid = openssl_pkey_get_private($key);
openssl_sign($message, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
$signature = base64_encode($signature);
openssl_free_key($pkeyid);

$api = 'https://developer.api.walmart.com';
$product_resource = 'api-proxy/service/affil/product/v2/items/316226539';
$client = new Client(['base_uri' => $api]);
$response = $client->get($product_resource, [
  'headers' => [
    'WM_SEC.KEY_VERSION' => $version,
    'WM_CONSUMER.ID' => $consumer_id,
    'WM_CONSUMER.INTIMESTAMP' => $timestamp,
    'WM_SEC.AUTH_SIGNATURE' => $signature,
  ]
]);

print_r(json_decode($response->getBody()->__toString()));
