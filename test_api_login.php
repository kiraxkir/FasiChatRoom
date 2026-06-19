<?php
$url = 'http://localhost:8000/api/login';
$data = http_build_query([
    'matricule' => 'SI2024001',
    'motDePasse' => 'EtuPass123!'
]);
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => $data,
        'ignore_errors' => true,
    ],
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
echo "HTTP_RESPONSE:\n";
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        echo $h . "\n";
    }
}
echo "BODY:\n" . $response . "\n";
