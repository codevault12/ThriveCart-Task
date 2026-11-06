<?php

declare(strict_types=1);

// GET  /api/products
// POST /api/basket  {"items": ["R01", "G01"]}

use Acme\Basket\BasketFactory;
use Acme\Catalogue\UnknownProductException;

require __DIR__ . '/../vendor/autoload.php';

/** @var BasketFactory $factory */
$factory = require __DIR__ . '/../config/acme.php';

/**
 * @param array<mixed>|JsonSerializable $body
 */
function respond(int $status, array|JsonSerializable $body): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body, JSON_THROW_ON_ERROR);
    exit;
}

$method = is_string($_SERVER['REQUEST_METHOD'] ?? null) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$path = parse_url(is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);

match ([$method, $path]) {
    ['GET', '/api/products'] => respond(200, $factory->catalogue->all()),
    ['POST', '/api/basket'] => (static function () use ($factory): never {
        try {
            $payload = json_decode((string) file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            respond(400, ['error' => 'Request body must be valid JSON.']);
        }

        $items = is_array($payload) ? ($payload['items'] ?? null) : null;
        if (!is_array($items) || !array_is_list($items) || array_filter($items, 'is_string') !== $items) {
            respond(422, ['error' => '"items" must be a list of product codes.']);
        }

        /** @var list<string> $items */
        try {
            respond(200, $factory->create($items)->summary());
        } catch (UnknownProductException $e) {
            respond(422, ['error' => $e->getMessage()]);
        }
    })(),
    default => respond(404, ['error' => 'Not found.']),
};
