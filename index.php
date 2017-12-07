<?php
 
/* show all errors */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require __DIR__ . '/vendor/autoload.php';
use \Slim\App;

/* Database configuration */
$dbhost = '127.0.0.1';        
$dbuser = 'andrei';
$dbpass  = 'f8d90aWaRri~Njtu';
$dbname = 'admin_andrei';
$dbmethod = 'mysql:dbname=';

$pdo = new PDO($dbmethod.$dbname, $dbuser, $dbpass);
$db  = new NotORM($pdo);
$app = new App();

$app->get('/', function() {
	echo "API IMSOTEC";
});


// Listeaza toate cuvintele
$app->get('/words', function($request, $response, $args) use($app, $db) {
	$rezultat["error"] = false;
	$rezultat["message"] = "Listare integrala a cuvintelor";
	foreach ($db->words() as $data) {
		$rezultat["words"][] = array(
			'id' => $data['id'],
			'ro' => $data['ro'],
			'en' => $data['en'],
			'de' => $data['de']            
			);
		}
    return $response->withJSON($rezultat, 200, JSON_UNESCAPED_UNICODE);
});

// Listeaza un cuvant dupa ID
$app->get('/word/{id}', function($request, $response, $args) use($app, $db) {
	$word = $db->words()->where('id', $args['id']);
	$word_detail = $word->fetch();
	if ($word->count() == 0) {
		$rezultat["error"] = true;
		$rezultat["message"] = "Nu exista acest ID";
	} else {
		$rezultat["error"] = false;
		$rezultat["message"] = "ID-ul solicitat a fost gasit";
		$rezultat["ro"] = $word_detail['ro'];
		$rezultat["en"] = $word_detail['en'];
		$rezultat["de"] = $word_detail['de'];
	}
    return $response->withJSON($rezultat, 200, JSON_UNESCAPED_UNICODE);
});

// Adauga un cuvant nou
$app->post('/word', function($request, $response, $args) use($app, $db) {
	$word = $request->getParams();
	$word_detail = $db->words->insert($word);
	if ($word_detail) {
		$rezultat["error"] = false;
		$rezultat["message"] = "Datele au fost adaugate cu succes";
		return $response->withJSON($rezultat, 200, JSON_UNESCAPED_UNICODE);
	} else {
		$rezultat["error"] = true;
		$rezultat["message"] = "Nu s-a putut salva informatia in baza de date";
		return $response->withJSON($rezultat, 404, JSON_UNESCAPED_UNICODE);
	}
});

// Modifica un cuvant existent
$app->put('/word/{id}', function($request, $response, $args) use($app, $db) {
    $nword = $request->getParams();
	$word = $db->words()->where('id', $args);
	if ($word->fetch()) {
		$result = $word->update($nword);
			$rezultat["error"] = false;
			$rezultat["message"] = "Modificarea a fost facuta cu succes";
			return $response->withJSON($rezultat, 200, JSON_UNESCAPED_UNICODE);
	} else {
			$rezultat["error"] = true;
			$$rezultat["message"] = "ID-ul solicitat a fost gasit";
			return $response->withJSON($rezultat, 404, JSON_UNESCAPED_UNICODE);
	}
});

// Sterge un cuvant dupa ID
$app->delete('/word/{id}', function($request, $response, $args) use($app, $db) {
	$word = $db->words()->where('id', $args);
	if ($word->fetch()) {
		$result = $word->delete();
			$rezultat["error"] = false;
			$rezultat["message"] = "Cuvantul a fost sters cu succes";
			return $response->withJSON($rezultat, 200, JSON_UNESCAPED_UNICODE);
	} else {
			$rezultat["error"] = true;
			$$rezultat["message"] = "ID-ul solicitat a fost gasit";
			return $response->withJSON($rezultat, 404, JSON_UNESCAPED_UNICODE);
	}
});

$app->run();
?>
