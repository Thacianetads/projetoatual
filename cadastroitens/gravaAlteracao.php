<?php
// Configurações iniciais

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

include "conecta.php";

// 1. Captura dos dados
$txtConteudo = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($txtConteudo) || empty($txtConteudo["cId"])) {
    die("❌ ID do produto não informado ou formulário inválido.");
}

$acao = 'Atualizar'; 
$id          = $txtConteudo["cId"];
$ncm         = $txtConteudo["cNcm"] ?? '';
$ecoflow_sku = $txtConteudo["cEcoflow_sku"] ?? '';
$name        = $txtConteudo["cName"] ?? '';
$fornecedor  = $txtConteudo['cFornecedor'] ?? '';
$tags        = $txtConteudo['cTags'] ?? '';
$preco_digitado_reais = $txtConteudo['cPreco'] ?? '';

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
$updated_at  = date('Y-m-d H:i:s');

// 🔹 2. Busca URL atual no Banco
$query = $conexao->prepare("SELECT imagem FROM TBPRODUTO WHERE ID = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$query->close();

// URL atual do banco
$url_imagem_final = $row['imagem'] ?? '';

// Verifica se houve upload de NOVA imagem
$imagem = $_FILES["cImagem"] ?? null;
$tem_nova_imagem = ($imagem && !empty($imagem['name']) && $imagem['error'] === UPLOAD_ERR_OK);

// 🔹 3. Lógica Inteligente de Upload
if ($tem_nova_imagem) {
    $supabase_url = $_ENV['SUPABASE_URL'];
    $supabase_key = $_ENV['SUPABASE_KEY'];
    $bucket       = 'imagens';

    $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
    $tipo_mime = mime_content_type($imagem['tmp_name']);

    // === AQUI ESTÁ A MÁGICA QUE VOCÊ PEDIU ===
    if (!empty($url_imagem_final)) {
        // CENÁRIO A: Já existe imagem.
        // Extraímos apenas o nome do arquivo da URL antiga para manter a URL igual.
        // Ex: de ".../prod_15.jpg" pegamos "prod_15.jpg"
        $nome_arquivo = basename(parse_url($url_imagem_final, PHP_URL_PATH));
    } else {
        // CENÁRIO B: Primeira vez.
        // Geramos um nome padrão baseado no ID.
        $nome_arquivo = "produto_" . uniqid() . "." . $extensao;
    }

    if (function_exists('curl_init')) {
        $curl = curl_init();
        
        // Usamos PUT e x-upsert: true para GARANTIR a substituição do arquivo
        curl_setopt_array($curl, [
            CURLOPT_URL => "$supabase_url/storage/v1/object/$bucket/$nome_arquivo",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST", // Supabase aceita POST para update se tiver upsert
            CURLOPT_POSTFIELDS => file_get_contents($imagem['tmp_name']),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $supabase_key",
                "Content-Type: $tipo_mime",
                "x-upsert: true" // <--- Isso sobrescreve o arquivo mantendo o nome
            ],
        ]);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($http_code == 200 || $http_code == 201) {
            $cache_buster = strtotime($updated_at); // Converte para um inteiro (timestamp Unix)
            $url_imagem_final = "$supabase_url/storage/v1/object/public/$bucket/$nome_arquivo?v=$cache_buster";
        } else {
            die("❌ Erro Supabase ($http_code): $response");
        }
    } else {
        die("❌ Erro: cURL não habilitado.");
    }
}

$webhook_url = $_ENV['N8N_WEBHOOK_URL'];

$curl_webhook = curl_init();
curl_setopt_array($curl_webhook, [
    CURLOPT_URL => $webhook_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
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
        'imagem_url' => $url_imagem_final 
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
]);

$response_webhook = curl_exec($curl_webhook);
$http_code_webhook = curl_getinfo($curl_webhook, CURLINFO_HTTP_CODE);
$error_webhook = curl_error($curl_webhook);
curl_close($curl_webhook);

if ($http_code_webhook != 200 && $http_code_webhook != 201) {
    echo "<br>⚠️ Erro ao enviar para o webhook (HTTP $http_code_webhook): $error_webhook <br>Resposta: $response_webhook";
} else {
    echo "<br>✅ URL da imagem enviada para o webhook com sucesso!";
}

// 🔹 4. Atualiza o Banco de Dados
$sql = "UPDATE TBPRODUTO SET 
            NCM = ?, 
            ECOFLOW_SKU = ?, 
            NAME = ?, 
            PRECO = ?, 
            FABRICANTE = ?, 
            FORNECEDOR = ?, 
            TAGS = ?, 
            IMAGEM = ?,  
            updated_at = ? 
        WHERE ID = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("sssssssssi", 
    $ncm, 
    $ecoflow_sku, 
    $name, 
    $preco_para_bd, 
    $fabricante_nome, 
    $fornecedor, 
    $tags, 
    $url_imagem_final, 
    $updated_at, 
    $id
);

if ($stmt->execute()) {
    echo "<script>
            window.location.href='consultaProduto.php';
          </script>";
} else {
    echo "❌ Erro ao atualizar banco: " . $stmt->error;
}

$stmt->close();
mysqli_close($conexao);
?>