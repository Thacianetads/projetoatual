
<?php
include "conecta.php";

// Verifica se há busca
$busca = "";
if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $busca = mysqli_real_escape_string($conexao, $_GET['busca']);
    $sql = "SELECT * FROM TBPRODUTO 
            WHERE name LIKE '%$busca%' 
               OR ncm LIKE '%$busca%'
               OR preco LIKE '%$busca%'  
               OR ecoflow_sku LIKE '%$busca%' 
               OR fabricante LIKE '%$busca%' 
               OR fornecedor LIKE '%$busca%' 
               OR tags LIKE '%$busca%'";
} else {
    $sql = "SELECT * FROM TBPRODUTO";
}

$rs = mysqli_query($conexao, $sql);
$total_registros = mysqli_num_rows($rs);
?>

<html>
<head>
<meta charset="UTF-8">
<style>
    /* Reset básico */
    * {
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: #f5f7fa;
        margin: 20px;
        color: #333;
    }

    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: 700;
        letter-spacing: 1.2px;
    }

    hr {
        border: none;
        height: 2px;
        background: #2980b9;
        width: 400px;
        margin: 0 auto 30px auto;
        border-radius: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    thead {
        background: #2980b9;
        color: white;
        font-weight: 600;
        text-align: left;
    }

    th, td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
    }

    tbody tr:hover {
        background-color: #ecf0f1;
        cursor: default;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* Links nas células */
    td a {
        color: #2980b9;
        text-decoration: none;
        font-weight: 600;
    }

    td a:hover {
        text-decoration: underline;
    }

    /* Botões estilizados */
    button {
        background-color: #2980b9;
        color: white;
        border: none;
        padding: 7px 15px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 0.9rem;
    }

    button:hover {
        background-color: #1f618d;
    }

    button a {
        color: white;
        text-decoration: none;
        display: block;
    }

    /* Ícone excluir */
    td img {
        width: 20px;
        height: 20px;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    td img:hover {
        transform: scale(1.2);
    }

    /* Responsividade */
    @media screen and (max-width: 900px) {
        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead tr {
            display: none;
        }
        tbody tr {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        tbody td {
            padding-left: 50%;
            position: relative;
            border-bottom: 1px solid #eee;
        }
        tbody td:last-child {
            border-bottom: 0;
        }
        tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            top: 12px;
            font-weight: 600;
            color: #2980b9;
            white-space: nowrap;
        }
    }

    /* Botão cadastrar centralizado */
    .btn-cadastrar {
        display: block;
        margin: 30px auto;
        background-color: #27ae60;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: background-color 0.3s ease;
        border: none;
        cursor: pointer;
        color: white;
        width: fit-content;
    }

    .btn-cadastrar:hover {
        background-color: #1e8449;
    }

    .btn-cadastrar a {
        color: white;
        text-decoration: none;
    }

    .search-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .search-container input[type="text"] {
        width: 300px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }

    .search-container button {
        background-color: #2980b9;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 0.9rem;
        margin-left: 5px;
    }

    .search-container button:hover {
        background-color: #1f618d;
    }
    
    .container-preview {
        position: relative;
        display: inline-block;
    }

    .img-hover-zoom {
        display: none;
        position: absolute;
        
        /* --- POSICIONAMENTO (Para esquerda da tabela) --- */
        top: -100px;     /* Ajuste vertical */
        right: 100%;     /* Joga para a esquerda da miniatura */
        margin-right: 15px; /* Afasta um pouco da tabela */
        
        z-index: 99999;
        
        /* --- APARÊNCIA --- */
        border: 4px solid #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        background-color: #fff;
        
        /* --- FORÇAR TAMANHO GRANDE --- */
        width: 170px;       /* Força a largura fixa de 500px */
        height: auto;       /* Mantém a proporção (não achata a imagem) */
        min-width: 170px;   /* Garante que nunca fique menor que isso */
        
        /* Opcional: Se a imagem for muito pequena e ficar pixelada, 
           você pode remover o object-fit ou usar 'contain' */
        object-fit: contain; 
        border-radius: 6px;
    }

    .container-preview:hover .img-hover-zoom {
        display: block;
    }
    

</style>

<title> Consulta Produto </title>
<script language ="Javascript">
    function confirmacao(id,name){
        var resposta = confirm("Deseja remover "+name+"?");
        if (resposta == true){
            window.location.href ="excluirProduto.php?+id="+id;
        }
    }
    </script>
</head>
<body>
<h1> Lista de produtos </h1>
<hr>
<div class="search-container">
    <form method="GET" action="">
        <input type="text" name="busca" placeholder="Buscar produto, SKU, fabricante..." value="<?php echo htmlspecialchars($busca); ?>">
        <button type="submit">Pesquisar</button>
        <?php if (!empty($busca)) { ?>
            <button type="button" onclick="window.location.href='?';" style="background-color:#95a5a6;">Limpar</button>
        <?php } ?>
    </form>
</div>
<button><a href="produto.php">Cadastrar item</a></button>
<br>
<br>
<table cellspacing = "0" border = "1">
<thead>
    <tr>
        <th>ncm</th>
        <th>sku</th>
        <th>nome do produto</th>
        <th>preço</th>
        <th>Fabricante</th>
        <th>Fornecedor</th>
        <th>Tags</th>
        <th>imagem</th>
        <th>Ação</th>
    </tr>
</thead>
<?php
    while ($reg = mysqli_fetch_array($rs)){
        $id = $reg["id"];
        $ncm = $reg["ncm"];
        $ecoflow_sku = $reg["ecoflow_sku"];
        $name = $reg["name"];
        $preco_em_centavos = $reg["preco"];
        $preco_em_reais_numero = (float)$preco_em_centavos / 100;
        $preco_em_reais_formatado = "R$ " . number_format($preco_em_reais_numero, 2, ',', '.');
        $fabricante = $reg["fabricante"];
        $fornecedor = $reg["fornecedor"];
        $tags = $reg["tags"];
        $imagem = $reg["imagem"];

?>
    <tr>
        <td><?php print $ncm; ?></td>
        <td><?php print $ecoflow_sku; ?></td>
        <td><?php print $name; ?></td>
        <td><?php print $preco_em_reais_formatado; ?></td>
        <td><?php print $fabricante; ?></td>
        <td><?php print $fornecedor; ?></td>
        <td><?php print $tags; ?></td>
        <td>
    <?php if(!empty($imagem)){ ?>
        
        <div class="container-preview">
            
            <a href="<?php echo htmlspecialchars(trim($imagem)); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               title="Clique para abrir original">
               
               <img src="<?php echo htmlspecialchars(trim($imagem)); ?>" 
                    alt="Miniatura" 
                    style="width: 65px; height: 65px; border-radius: 4px; object-fit: cover; cursor: zoom-in; margin-left: 20px;">
               
               <img src="<?php echo htmlspecialchars(trim($imagem)); ?>" 
                    class="img-hover-zoom"
                    alt="Preview ampliado">
            </a>

        </div>

    <?php } else { ?>
        <span style="color:#888;">Sem imagem</span>
    <?php } ?>
</td>
         <td>
            <a href="alteraProduto.php?id=<?php print $id;?>"><img src="editar.png" alt="Altera Pessoa" border="0" widht="20px" height="20px" style="width:27px;height:27px"></a>
            <a href="javascript:func()" onclick="confirmacao('<?php print $id; ?>','<?php print $name;?>')"><img src="excluir.png" alt="Exclui Pessoa" border ="0" widht="20px" height="20px" style="width:27px;height:27px"></a>
        </td>

    </tr>
    
    <?php } ?>
</table>

<br>
</body>
</html>