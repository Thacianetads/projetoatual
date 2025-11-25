<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$ncm = $_POST['cNcm'];
$ecoflow_sku = $_POST['cEcoflow_sku'];
$name = $_POST['cName'];
$preco_digitado_reais = $_POST['cPreco'];

// 2. Limpeza e Conversão para Centavos (int)
// O processo é: remover o separador de milhar (.), trocar o separador decimal (,) por ponto (.), e multiplicar por 100.
$preco_limpo = str_replace('.', '', $preco_digitado_reais); // Remove separador de milhar
$preco_limpo = str_replace(',', '.', $preco_limpo); // Troca vírgula decimal por ponto

// Converte para float, multiplica por 100 e arredonda/converte para inteiro (centavos)
$preco_em_centavos = intval(round(floatval($preco_limpo) * 100));

// Variáveis para salvar e exibir
$preco_para_bd = $preco_em_centavos; // Este é o valor que vai para o banco (ex: 1050)
$preco_em_reais_numero = (float)$preco_para_bd / 100; // Valor float em Reais (ex: 10.50)

// 3. Formata o valor em Reais para exibição (ex: 10.50 -> R$ 10,50)
$preco_em_reais_formatado = "R$ " . number_format($preco_em_reais_numero, 2, ',', '.');
if (!$name || !$preco_para_bd) die("❌ Nome ou preço do produto não informado.");
$fabricantes_id_para_nome = [
    "391270" => "PGYTECH",
    "395837" => "DJI",
    "486138" => "ECOFLOW",
    "540260" => "AUTEL",
    "580571" => "MICASENSE",
    "581259" => "SPACEX"
];

$fabricantes_nome_para_id = array_flip($fabricantes_id_para_nome);
$fabricante_nome = $_POST['cFabricante'];
$fabricante_id = $fabricantes_nome_para_id[$fabricante_nome] ?? $fabricante_nome;
$fornecedor = $_POST['cFornecedor'];
$tags = $_POST['cTags'];
date_default_timezone_set('America/Sao_Paulo');
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');
$imagem = $_FILES["cImagem"] ?? null;
$acao = 'cadastrar'; 

if (!$name || !$preco_para_bd) die("❌ Nome ou preço do produto não informado.");

function redirecionaComErro($mensagemErro) {
    // ATENÇÃO: Substitua 'SEU_NOME_DO_FORMULARIO.php' pelo nome real do seu arquivo HTML/PHP
    // Exemplo: header("Location: cadastro_produto.php?error=" . urlencode($mensagemErro));
    header("Location: produto.php?error=" . urlencode($mensagemErro));
    exit();
}

include "conecta.php";


// A. Verificar se o SKU já existe (deve ser único)
if (!empty($ecoflow_sku)) { // Só verifica se o SKU foi preenchido
    $stmt_sku = $conexao->prepare("SELECT id FROM TBPRODUTO WHERE ecoflow_sku = ? LIMIT 1");
    $stmt_sku->bind_param("s", $ecoflow_sku);
    $stmt_sku->execute();
    $stmt_sku->store_result();
    
    if ($stmt_sku->num_rows > 0) {
        redirecionaComErro("O SKU '{$ecoflow_sku}' já está cadastrado. Por favor, use um SKU diferente.");
    }
    $stmt_sku->close();
}

// B. Verificar se o Nome já existe (deve ser único)
$stmt_nome = $conexao->prepare("SELECT id FROM TBPRODUTO WHERE name = ? LIMIT 1");
$stmt_nome->bind_param("s", $name);
$stmt_nome->execute();
$stmt_nome->store_result();

if ($stmt_nome->num_rows > 0) {
    redirecionaComErro("O Nome de produto '{$name}' já está cadastrado. Por favor, use um nome diferente.");
}
$stmt_nome->close();

$imagem_url = null;


if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {

    $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
    $tipo_mime = mime_content_type($imagem['tmp_name']);
    $extensoes_permitidas = ['jpg','jpeg','png','gif'];
    $max_size = 2 * 1024 * 1024;

    if (!in_array($extensao, $extensoes_permitidas)) die("❌ Extensão de arquivo não permitida.");
    if ($imagem['size'] > $max_size) die("❌ Arquivo muito grande (máx 2MB).");

    // Gera nome único da imagem
    $nome_arquivo = "produto_" . uniqid() . "." . $extensao;
    
    $supabase_url = $_ENV['SUPABASE_URL'];
    $supabase_key = $_ENV['SUPABASE_KEY'];
    $bucket = 'imagens';

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "$supabase_url/storage/v1/object/$bucket/$nome_arquivo",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => file_get_contents($imagem['tmp_name']),
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $supabase_key",
            "Content-Type: $tipo_mime",
            "x-upsert: true"
        ],
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);

    if ($http_code != 200 && $http_code != 201) die("❌ Erro ao enviar imagem (HTTP $http_code): $error");

    $imagem_url = "$supabase_url/storage/v1/object/public/$bucket/$nome_arquivo";
    echo "✅ Imagem enviada com sucesso! <br>";
}


// --- SALVA NO MYSQL ---
$stmt = $conexao->prepare("INSERT INTO TBPRODUTO 
    (ncm, ecoflow_sku, name, preco, fabricante, fornecedor, tags, created_at, updated_at, imagem) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssissssss",
    $ncm,
    $ecoflow_sku,
    $name,
    $preco_para_bd,
    $fabricante_nome,
    $fornecedor,
    $tags,
    $created_at,
    $updated_at,
    $imagem_url
);

$webhook_url = $_ENV['N8N_WEBHOOK_URL'];

$payload = [
    'acao' => $acao,
    'ncm' => $ncm,
    'sku' => $ecoflow_sku,
    'name' => $name,
    'preco_centavos' => $preco_para_bd,
    'preco_reais' => $preco_em_reais_formatado,
    'fabricante_id' => $fabricante_id,
    'fabricante' => $fabricante_nome,
    'fornecedor' => $fornecedor,
    'tags' => $tags,
    'created_at' => $created_at,
    'updated_at' => $updated_at,
    'imagem_url' => $imagem_url ?? null 
];

$curl_webhook = curl_init();
curl_setopt_array($curl_webhook, [
    CURLOPT_URL => $webhook_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
]);
curl_exec($curl_webhook);
curl_close($curl_webhook);


if ($stmt->execute()) {
    echo "✅ GRAVAÇÃO EXECUTADA COM SUCESSO!<br>";
    echo '<meta http-equiv="refresh" content="0;URL=consultaProduto.php">';
} else {
    echo "❌ Erro ao salvar no banco: " . $stmt->error;
}


$stmt->close();
$conexao->close();
?>
