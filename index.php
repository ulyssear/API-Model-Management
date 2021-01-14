<?php
declare(strict_types=1);

use App\{App, Model};

require_once __DIR__ . '/bootstrap.php';

$app = new App();


$models = [];

$models['user'] =
    new Model('user', [
        'email' => 'email|min:2|max:128|required',
        'password' => 'password|min:2|max:128|required',
        'role' => 'enum:user,admin|required'
    ]);

$models['casier'] =
    new Model('casier', [
        'container' => 'string|min:2|max:128|required',
        'numero' => 'big number|required',
        'fonctionnel' => 'boolean|default:0|required',
        'reserve' => 'boolean|default:0|required',
        'verouille' => 'boolean|default:0|required'
    ]);

$models['reservation'] =
    new Model('reservation', [
        'user' => 'foreign to:users.id',
        'casier' => 'foreign to:casiers.id'
    ]);

foreach ($models as $name => $model) {
    $app->addModel($model);
}

$app->router()
    ->get('/', function () use ($app) {
        $app->response()->php('home.php');
    });

$app->runAtAddress();

restore_error_handler();