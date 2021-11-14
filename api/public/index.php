<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addRoutingMiddleware();

$dbClient = new MongoDB\Client($_ENV['MONGO_URI']);

$adapterClient = new GuzzleHttp\Client(['base_uri' => $_ENV['ADAPTER_URI']]);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});


$app->post('/setup/{id}', function (Request $request, Response $response, $args) use ($adapterClient, $dbClient) {
    $id = $args['id'];

    $dbName = 'test-'.$id;

    $res = $adapterClient->get('/pipeline');
    $data = json_decode($res->getBody()->getContents(), true);

    $db = $dbClient->{$dbName};

    $db->assets->drop();

    $db->command([
        'create' => 'assets',
        'viewOn' => 'raw_assets',
        'pipeline' => $data,
    ]);

    $response->getBody()->write(json_encode(['status' => 1]));
    return $response->withHeader('Content-Type', 'application/json');
}); 

$app->post('/data/{id}', function (Request $request, Response $response, $args) use ($dbClient) {
    $id = $args['id'];

    $dbName = 'test-'.$id;

    $collection = $dbClient->{$dbName}->raw_assets;

    $data = json_decode($request->getBody()->getContents(), true);

    foreach ($data as $item) {
        $updateResult = $collection->updateOne(
            ['_id' => $item['_id']],
            ['$set' => $item],
            ['upsert' => true],
        );
    }

    $response->getBody()->write(json_encode(['status' => 1]));
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/data/{id}', function (Request $request, Response $response, $args) use ($dbClient) {
    $id = $args['id'];

    $dbName = 'test-'.$id;

    $collection = $dbClient->{$dbName}->assets;

    $result = $collection->find([]);

    $response->getBody()->write(json_encode($result->toArray()));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
