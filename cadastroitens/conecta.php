<?php
//realiza a conexão com o mySql
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

$conexao = mysqli_connect($host, $user, $pass);

$bancodedados = $_ENV['DB_NAME'];
//conecta mySql e base de dados
$bd = mysqli_select_db($conexao,$bancodedados);
if (mysqli_connect_errno()){
    printf("Falha na conexão; %s \n", mysqli_connect_error());
    die();
}
?>