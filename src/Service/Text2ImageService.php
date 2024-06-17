<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class Text2ImageService
{
private $client;
private $apiKey;
private $secretKey;
private $apiUrl;

public function __construct(HttpClientInterface $client, string $apiKey, string $secretKey, string $apiUrl)
{
$this->client = $client;
$this->apiKey = $apiKey;
$this->secretKey = $secretKey;
$this->apiUrl = $apiUrl;
}

public function generateImage(string $prompt): ?string
{
$modelId = $this->getModelId();

if (!$modelId) {
return null;
}

$params = [
"type" => "GENERATE",
"numImages" => 1,
"width" => 1024,
"height" => 1024,
"generateParams" => [
"query" => $prompt,
],
];

$response = $this->client->request('POST', $this->apiUrl . 'key/api/v1/text2image/run', [
'headers' => [
'X-Key' => 'Key ' . $this->apiKey,
'X-Secret' => 'Secret ' . $this->secretKey,
],
'json' => [
'model_id' => $modelId,
'params' => $params,
],
]);

$data = $response->toArray();

if (!isset($data['uuid'])) {
return null;
}

return $this->checkGeneration($data['uuid']);
}

private function getModelId(): ?int
{
$response = $this->client->request('GET', $this->apiUrl . 'key/api/v1/models', [
'headers' => [
'X-Key' => 'Key ' . $this->apiKey,
'X-Secret' => 'Secret ' . $this->secretKey,
],
]);

$data = $response->toArray();

return $data[0]['id'] ?? null;
}

private function checkGeneration(string $uuid, int $attempts = 10, int $delay = 10): ?string
{
while ($attempts > 0) {
$response = $this->client->request('GET', $this->apiUrl . 'key/api/v1/text2image/status/' . $uuid, [
'headers' => [
'X-Key' => 'Key ' . $this->apiKey,
'X-Secret' => 'Secret ' . $this->secretKey,
],
]);

$data = $response->toArray();

if ($data['status'] === 'DONE' && isset($data['images'][0])) {
return $data['images'][0];
}

$attempts--;
sleep($delay);
}

return null;
}
}