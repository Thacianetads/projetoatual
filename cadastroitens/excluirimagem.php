<?php
$txtConteudo = filter_input_array(INPUT_GET, FILTER_DEFAULT);

if (isset($txtConteudo["id"])) {
    $varId = filter_var($txtConteudo["id"], FILTER_VALIDATE_INT);

    if ($varId) {
        include "conecta.php";

        // Pega o nome da imagem do banco
        $sql = "SELECT imagem FROM TBPRODUTO WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $varId);
        $stmt->execute();
        $stmt->bind_result($imagem_url);
        $stmt->fetch();
        $stmt->close();

        if ($imagem_url) {
            // Extrai o nome do arquivo da URL
            $path_parts = parse_url($imagem_url, PHP_URL_PATH);
            $arquivo = basename($path_parts);

            // Configuração Supabase
            $supabase_url = 'https://zxrwexyelatcjkmfzqxl.supabase.co';
            $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inp4cndleHllbGF0Y2prbWZ6cXhsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc2MTU1MDAyOSwiZXhwIjoyMDc3MTI2MDI5fQ.IFlr3a2ASDDdjpz0RgXIMG0Xu_8OnXJ0celL3BLjtrs'; // Use variável de ambiente
            $bucket = 'imagens';

            // Exclusão via cURL
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

            if ($http_code != 200 && $http_code != 204) {
                die("❌ Erro ao excluir imagem (HTTP $http_code): $error <br>Resposta: $response");
            }

            // Atualiza banco
            $sqlUpdate = "UPDATE TBPRODUTO SET imagem = NULL WHERE id = ?";
            $stmt = $conexao->prepare($sqlUpdate);
            $stmt->bind_param("i", $varId);
            $stmt->execute();
            $stmt->close();

            echo "✅ Imagem excluída com sucesso!";
            echo "<meta http-equiv='Refresh' CONTENT='0;URL=consultaProduto.php'>";
        } else {
            echo "❌ Produto não possui imagem!";
        }

        mysqli_close($conexao);
    } else {
        echo "ID inválido!";
    }
} else {
    echo "Nenhum ID fornecido!";
}
?>
