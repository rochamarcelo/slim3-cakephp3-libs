<?php
$app->get('/', function ($request, $response) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    // Render index view
    return $this->renderer->render($response, 'index.phtml');
});
// Routes
$app->get('/api/animes', function($request, $response) {
    $body = $response->getBody();

    $query = $this->database->newQuery();
    $fields = [
        'id',
        'name',
        'episodes'
    ];
    $query->select($fields)->from('animes')->order([
        'name' => 'ASC'
    ]);

    $animes = $query->execute()->fetchAll('assoc');
    $body->write(json_encode($animes));
    return $response;
});
$app->get('/api/animes/{id}', function($request, $response, $args) {
    $query = $this->database->newQuery();
    $fields = [
        'id',
        'name',
        'episodes'
    ];
    $query->select($fields)->from('animes')->where([
        'id' => (int)$args['id']
    ]);

    $anime = $query->execute()->fetch('assoc');
    if ( !$anime ) {
        $newResponse = $response->withStatus(404);
        $body = $newResponse->getBody();
        $result = [
            'code' => 404,
            'message' => 'Anime not found'
        ];
        $body->write(json_encode($result));
        return $newResponse;
    }
    $body = $response->getBody();
    $body->write(json_encode($anime));
    return $response;
});

$app->post('/api/animes', function($request, $response) {
    $data = $request->getParsedBody();

    $errors = getAnimeValidationErrors($data);
    if (!empty($errors)) {
        $newResponse = $response->withStatus(400);
        $body = $newResponse->getBody();
        $result = [
            'code' => 400,
            'message' => 'Validation error',
            'errors' => $errors
        ];
        $body->write(json_encode($result));
        return $newResponse;
    }

    $anime = [
        'name' => $data['name'],
        'episodes' => $data['episodes']
    ];

    $result = $this->database->insert('animes', $anime);

    $anime['id'] = $result->lastInsertId();
    $body = $response->getBody();
    $body->write(json_encode($anime));
    return $response;
});
$app->put('/api/animes/{id}', function($request, $response, $args) {
    $data = $request->getParsedBody();

    $errors = getAnimeValidationErrors($data);
    if (!empty($errors)) {
        $newResponse = $response->withStatus(400);
        $body = $newResponse->getBody();
        $result = [
            'code' => 400,
            'message' => 'Validation error',
            'errors' => $errors
        ];
        $body->write(json_encode($result));
        return $newResponse;
    }
    $anime = [
        'name' => $data['name'],
        'episodes' => $data['episodes']
    ];

    $conditions = [
        'id' => (int)$args['id']
    ];
    $result = $this->database->update('animes', $anime, $conditions);
    $body = $response->getBody();
    $body->write(json_encode($anime));
    return $response;
});
$app->delete('/api/animes/{id}', function($request, $response, $args) {
    $body = $response->getBody();

    $conditions = [
        'id' => (int)$args['id']
    ];
    $result = $this->database->delete('animes', $conditions);

    $body->write(json_encode([]));
    return $response;
});