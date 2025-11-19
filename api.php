<?php
// api.php - simples endpoints para gerar código, adicionar atualizações e consultar
$config = include __DIR__.'/config.php';
$tz = $config['timezone'] ?? 'UTC';
date_default_timezone_set($tz);

$datapath = __DIR__.'/data.json';

function load_data(){ global $datapath; $s = file_get_contents($datapath); return json_decode($s, true); }
function save_data($d){ global $datapath; file_put_contents($datapath, json_encode($d, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); }

$action = $_GET['action'] ?? $_POST['action'] ?? $_REQUEST['action'] ?? ($_GET['action'] ?? null);
header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'track'){
    $code = $_GET['code'] ?? '';
    $data = load_data();
    if(!isset($data['routes'][$code])){
        echo json_encode(['success'=>false, 'message'=>'Código não encontrado']);
        exit;
    }
    $route = $data['routes'][$code];
    echo json_encode(['success'=>true, 'code'=>$code, 'client_name'=>$route['client_name'], 'updates'=>$route['updates']]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// login
if(($action ?? '') === 'login'){
    $pw = $input['password'] ?? '';
    if($pw === $config['admin_password']){
        // simple token (not secure) - valid for this example
        $token = bin2hex(random_bytes(12));
        // temporary token store in session file (not persistent across server restarts)
        session_start();
        $_SESSION['admin_token'] = $token;
        echo json_encode(['success'=>true,'token'=>$token]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Senha incorreta']);
    }
    exit;
}

if(($action ?? '') === 'generate'){
    // require token
    $token = $input['token'] ?? '';
    session_start();
    if(!isset($_SESSION['admin_token']) || $_SESSION['admin_token'] !== $token){
        echo json_encode(['success'=>false,'message'=>'Não autorizado. Faça login.']);
        exit;
    }
    $client = $input['client'] ?? 'Cliente';
    $code = strtoupper(substr(bin2hex(random_bytes(4)),0,8));
    $data = load_data();
    $data['routes'][$code] = [
        'client_name' => $client,
        'created_at' => date('c'),
        'updates' => [],
        'active' => true
    ];
    save_data($data);
    echo json_encode(['success'=>true,'code'=>$code]);
    exit;
}

if(($action ?? '') === 'add_update'){
    $token = $input['token'] ?? '';
    session_start();
    if(!isset($_SESSION['admin_token']) || $_SESSION['admin_token'] !== $token){
        echo json_encode(['success'=>false,'message'=>'Não autorizado. Faça login.']);
        exit;
    }
    $code = $input['code'] ?? '';
    $location = $input['location'] ?? 'Local não informado';
    $note = $input['note'] ?? '';
    $data = load_data();
    if(!isset($data['routes'][$code])){
        echo json_encode(['success'=>false,'message'=>'Rota não encontrada']);
        exit;
    }
    $data['routes'][$code]['updates'][] = [
        'ts' => date('c'),
        'location' => $location,
        'note' => $note
    ];
    save_data($data);
    echo json_encode(['success'=>true,'message'=>'Atualização registrada']);
    exit;
}

if(($action ?? '') === 'list'){
    $token = $_GET['token'] ?? '';
    session_start();
    if(!isset($_SESSION['admin_token']) || $_SESSION['admin_token'] !== $token){
        echo json_encode(['success'=>false,'message'=>'Não autorizado']);
        exit;
    }
    $data = load_data();
    echo json_encode(['success'=>true,'routes'=>$data['routes']]);
    exit;
}

echo json_encode(['success'=>false,'message'=>'Ação inválida']);
exit;
?>