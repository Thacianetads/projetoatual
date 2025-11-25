<?php

$ncm = $_POST['cNcm'];
$ecoflow_sku = $_POST['cEcoflow_sku'];
$name = $_POST['cName'];
$cost_cents = $_POST['cCost_cents'];
$price_cents = $_POST['cPrice_cents'];
$price_on_time_cents = $_POST['cPrice_on_time_cents'];
date_default_timezone_set('America/Sao_Paulo');
$created_at = date('Y-m-d H:i:s');
$updated_at = date('Y-m-d H:i:s');

$imagem = $_FILES['cImagem'] ?? null;

include "conecta.php";

// --- SALVA NO MYSQL ---
$stmt = $conexao->prepare("INSERT INTO TBPRODUTO 
    (ncm, ecoflow_sku, name, cost_cents, price_cents, price_on_time_cents, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssiisss",
    $ncm,
    $ecoflow_sku,
    $name,
    $cost_cents,
    $price_cents,
    $price_on_time_cents,
    $created_at,
    $updated_at,
);

if ($stmt->execute()) {
    echo "✅ GRAVAÇÃO EXECUTADA COM SUCESSO!<br>";
    echo '<meta http-equiv="refresh" content="0;URL=consultaProduto.php">';
} else {
    echo "❌ Erro ao salvar no banco: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>
