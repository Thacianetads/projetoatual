<?php 

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 1. Recebe e valida o ID
$txtConteudo = filter_input_array(INPUT_GET, FILTER_DEFAULT);

if (isset($txtConteudo["id"])) {
    $varId = filter_var($txtConteudo["id"], FILTER_VALIDATE_INT);

    if ($varId) {
        include "conecta.php";

        // 2. Busca o nome da imagem antes de deletar o produto
        $sql = "SELECT imagem FROM TBPRODUTO WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $varId);
        $stmt->execute();
        $stmt->bind_result($imagem_url);
        $stmt->fetch();
        $stmt->close();

        // 3. Se houver imagem, exclui do Supabase
        if ($imagem_url) {
            $path_parts = parse_url($imagem_url, PHP_URL_PATH);
            $arquivo = basename($path_parts);

            $supabase_url = $_ENV['SUPABASE_URL'];
            $supabase_key = $_ENV['SUPABASE_KEY'];
            $bucket = 'imagens';

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "$supabase_url/storage/v1/object/$bucket/$arquivo",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $supabase_key",
                ],
            ]);

            $response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);
            curl_close($curl);

            // Opcional: Verificar erro, mas não impedir a exclusão do registro
            if ($http_code != 200 && $http_code != 204) {
                // Log de erro se necessário, mas segue o fluxo
                error_log("Erro ao excluir imagem do Supabase: $error");
            }
        }

        // 4. AGORA SIM: Exclui o registro do banco de dados (DELETE em vez de UPDATE)
        $sqlDelete = "DELETE FROM TBPRODUTO WHERE id = ?";
        $stmt = $conexao->prepare($sqlDelete);
        $stmt->bind_param("i", $varId);
        
        if ($stmt->execute()) {
            echo "✅ Item e imagem excluídos com sucesso!";
            echo "<meta http-equiv='Refresh' CONTENT='0;URL=consultaProduto.php'>";
        } else {
            echo "❌ Erro ao excluir o produto do banco de dados.";
        }
        
        $stmt->close();
        mysqli_close($conexao);
        
    } else {
        echo "ID inválido!";
    }
} else {
    echo "Nenhum ID fornecido!";
}
?>