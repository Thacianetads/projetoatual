<?php
include "conecta.php";

$sql = "SELECT * FROM TBPRODUTO";
$rs = mysqli_query($conexao,$sql);
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
</style>

<title> Consulta Produto </title>
<script language ="Javascript">
    function confirmacao(id,name){
        var resposta = confirm("Deseja remover "+name+"?");
        if (resposta == true){
            window.location.href ="excluirimagem.php?+id="+id;
        }
    }
    </script>
</head>
<body>
<h1> Lista de produtos </h1>
<hr>
<br>
<table cellspacing = "0" border = "1">
<thead>
    <tr>
        <th>ncm</th>
        <th>ecoflow_sku</th>
        <th>name</th>
        <th>cost_cents</th>
        <th>price_cents</th>
        <th>price_on_time_cents</th>
        <th>created_at</th>
        <th>updated_at</th>
        <th>imagem</th>
        <th>Anexar imagem</th>
        <th>Atualizar item</th>
        <th>Excluir imagem</th>
    </tr>
</thead>
<?php
    while ($reg = mysqli_fetch_array($rs)){
        $id = $reg["id"];
        $ncm = $reg["ncm"];
        $ecoflow_sku = $reg["ecoflow_sku"];
        $name = $reg["name"];
        $cost_cents = $reg["cost_cents"];
        $price_cents = $reg["price_cents"];
        $price_on_time_cents = $reg["price_on_time_cents"];
        $created_at = $reg["created_at"];
        $updated_at = $reg["updated_at"];
        $imagem = $reg["imagem"];

?>
    <tr>
        <td><?php print $ncm; ?></td>
        <td><?php print $ecoflow_sku; ?></td>
        <td><?php print $name; ?></td>
        <td><?php print $cost_cents; ?></td>
        <td><?php print $price_cents; ?></td>
        <td><?php print $price_on_time_cents; ?></td>
        <td><?php print $created_at; ?></td>
        <td><?php print $updated_at; ?></td>
        <td>
        <?php if(!empty($imagem)){ ?>
        <a href="<?php echo htmlspecialchars(trim($imagem)); ?>" 
       target="_blank" 
       rel="noopener noreferrer" 
       style="color:#2980b9; font-weight:600; text-decoration:none;">
       Ver imagem
    </a>
<?php } else { ?>
    <span style="color:#888;">Sem imagem</span>
<?php } ?>
        </td>
        <td>
            <button><a href="alteraimagem.php?id=<?php print $id;?>">Anexar imagem</a></button>
        </td>
         <td>
            <button><a href="alteraProduto.php?id=<?php print $id;?>">Atualizar item</a></button><br>
        </td>
        <td>
            <a href="javascript:func()" onclick="confirmacao('<?php print $id; ?>','<?php print $imagem;?>')"><img src="excluir.png" alt="Exclui Pessoa" border ="0" widht="20px" height="20px"></a>
        </td>

    </tr>
    
    <?php } ?>
</table>

<br>
<button><a href="produto.php">Cadastrar</a></button>
</body>
</html>