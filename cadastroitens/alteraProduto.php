<?php


include 'conecta.php';

// Conecta ao MySQL usando as variáveis do .env

$txtConteudo = filter_input_array(INPUT_GET, FILTER_DEFAULT);

if (isset($txtConteudo["id"])){
    $varId = $txtConteudo["id"];
    //comando para buscar os dados do id respectivo
    $sql = "SELECT * FROM TBPRODUTO WHERE id = '".$varId."'";

    $rs = mysqli_query($conexao, $sql);
    $reg = mysqli_fetch_array($rs);

    if (!$reg) {
        echo "REGISTRO NÃO LOCALIZADO!";
        echo "<meta HTTP-EQUIV='Refresh' CONTENT='5;URL=consultaProduto.php'>";
        exit;
    }

    $id = $reg["id"];
    $ncm = $reg["ncm"];
    $ecoflow_sku = $reg["ecoflow_sku"];
    $name = $reg["name"];
    $preco_em_centavos = $reg["preco"];
    $preco_em_reais_numero = (float)$preco_em_centavos / 100;
    $preco_em_reais_formatado = number_format($preco_em_reais_numero, 2, ',', '.');
    $created_at = $reg["created_at"];
    $updated_at = $reg["updated_at"];
    $fabricante = $reg['fabricante'];
    $fornecedor = $reg['fornecedor'];
    $tags = $reg['tags'];
    $imagem = $reg["imagem"];
    $acao = 'Atualizar item';
    
} else {
    echo "ID NÃO INFORMADO!";
    echo "<meta HTTP-EQUIV='Refresh' CONTENT='2;URL=consultaProduto.php'>";
    exit;
}
?>

<html>
<head>
    <title> Alterar dados do produto </title>
    <script language ="Javascript">
    function confirmacao(id,name){
        var resposta = confirm("Deseja remover "+name+"?");
        if (resposta == true){
            window.location.href ="excluirimagem.php?+id="+id;
        }
    }
</script>
<style>
    /* Fundo geral e estilo base */
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #dfe9f3 0%, #ffffff 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Garante altura mínima */
        margin: 0;
        padding: 20px 0; /* Espaço para scroll se necessário */
    }

    /* Caixa principal */
    .container {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 30px 40px;
        width: 350px;
    }

    /* Rótulos e campos */
    label {
        display: block;
        margin-top: 10px;
        color: #444;
        font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    input[type="datetime-local"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-top: 5px;
        font-size: 14px;
        box-sizing: border-box; /* Importante para padding não estourar width */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Efeito ao focar */
    input:focus {
        border-color: #0078d7;
        box-shadow: 0 0 5px rgba(0, 120, 215, 0.4);
        outline: none;
    }

    /* Botão bonito */
    .btn {
        margin-top: 20px;
        width: 100%;
        background-color: #0078d7;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn:hover {
        background-color: #005fa3;
    }

    .styled-select {
        appearance: none;
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px 40px 10px 15px; 
        font-size: 16px;
        color: #333;
        cursor: pointer;
        width: 100%; 
        box-sizing: border-box; 
        background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        transition: all 0.3s ease;
        margin-top: 5px;
    }

    .styled-select:hover {
        border-color: #888;
    }

    .styled-select:focus {
        border-color: #0078ff;
        box-shadow: 0 0 4px rgba(0, 120, 255, 0.4);
        outline: none;
    }

    .container-preview {
        position: relative;
        display: inline-block;
    }

    .img-hover-zoom {
        display: none;
        position: absolute;
        
        /* --- POSICIONAMENTO (Para esquerda da tabela) --- */
        top: -400px;     /* Ajuste vertical */
        right: 170%;     /* Joga para a esquerda da miniatura */
        margin-right: 15px; /* Afasta um pouco da tabela */
        
        z-index: 99999;
        
        /* --- APARÊNCIA --- */
        border: 4px solid #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        background-color: #fff;
        
        /* --- FORÇAR TAMANHO GRANDE --- */
        width: 350px;       /* Força a largura fixa de 500px */
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
</head>
<body>

<div class="container"> 
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <h1>TELA PARA ALTERAR DADOS DO PRODUTO </H1> 
    <HR><BR>
    
    <form action="gravaAlteracao.php" id="produtoForm" method="post" enctype="multipart/form-data"> 
        
        <input type="hidden" id="id" name="cId" value="<?php print $id;?>"/>
        <input type="hidden" id="acao_form" value="Atualizar item"/>

        <label> NCM: </label>
        <input type="text" id="ncm" name="cNcm" value="<?php print $ncm;?>" required><br>

        <label> SKU: </label>
        <input type="text" id="ecoflow_sku" name="cEcoflow_sku" value="<?php print $ecoflow_sku;?>" required><br>

        <label> Nome: </label>
        <input type="text" id="name" name="cName" value="<?php print $name;?>" required><br>

        <label> Preço: </label>
        <input type="text" id="preco" name="cPreco" placeholder="R$ 0,00" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg transition duration-150 ease-in-out"
        nputmode="numeric" value="<?php print $preco_em_reais_formatado;?>" required><br>

        <label>Fabricante:</label>
        <select name="cFabricante" id="fabricante" class="styled-select" required>
            <option value="">Selecione a opção</option>
            <option value="PGYTECH" <?php if ($fabricante == "PGYTECH") echo "selected"; ?>>PGYTECH</option>
            <option value="DJI" <?php if ($fabricante == "DJI") echo "selected"; ?>>DJI</option>
            <option value="ECOFLOW" <?php if ($fabricante == "ECOFLOW") echo "selected"; ?>>ECOFLOW</option>
            <option value="AUTEL" <?php if ($fabricante == "AUTEL") echo "selected"; ?>>AUTEL</option>
            <option value="MICASENSE" <?php if ($fabricante == "MICASENSE") echo "selected"; ?>>MICASENSE</option>
            <option value="SPACEX" <?php if ($fabricante == "SPACEX") echo "selected"; ?>>SPACEX</option>
        </select>

    
</select>

        <label>Fornecedor:</label>
        <select name="cFornecedor" id="fornecedor" class="styled-select" required>
            <option value="">Selecione a opção</option>
            <option value="MULTILASER" <?php if ($fornecedor == "MULTILASER") echo "selected"; ?>>MULTILASER</option>
            <option value="INTELBRAS" <?php if ($fornecedor == "INTELBRAS") echo "selected"; ?>>INTELBRAS</option>
            <option value="TIMBER" <?php if ($fornecedor == "TIMBER") echo "selected"; ?>>TIMBER</option>
            <option value="GOLDEN DISTRIBUIDORA LTDA" <?php if ($fornecedor == "GOLDEN DISTRIBUIDORA LTDA") echo "selected"; ?>>GOLDEN DISTRIBUIDORA LTDA</option>
            <option value="GOHOBBYT FUTURE TECHNOLOGY LTDA" <?php if ($fornecedor == "GOHOBBYT FUTURE TECHNOLOGY LTDA") echo "selected"; ?>>GOHOBBYT FUTURE TECHNOLOGY LTDA</option>
            <option value="ALLCOMP" <?php if ($fornecedor == "ALLCOMP") echo "selected"; ?>>ALLCOMP</option>
            <option value="DRONENERDS" <?php if ($fornecedor == "DRONENERDS") echo "selected"; ?>>DRONENERDS</option>
            <option value="SANTIAGO & SINTRA" <?php if ($fornecedor == "SANTIAGO & SINTRA") echo "selected"; ?>>SANTIAGO & SINTRA</option>
            <option value="POWERSAFE" <?php if ($fornecedor == "POWERSAFE") echo "selected"; ?>>POWERSAFE</option>
            <option value="AGEAGLE AERIAL SYSTEMS INC" <?php if ($fornecedor == "AGEAGLE AERIAL SYSTEMS INC") echo "selected"; ?>>AGEAGLE AERIAL SYSTEMS INC</option>
            <option value="STARLINK" <?php if ($fornecedor == "STARLINK") echo "selected"; ?>>STARLINK</option>
        </select>

        <label>Tags:</label>
        <select name="cTags" id="tags" class="styled-select" required>
            <option value="">Selecione a opção</option>
            <option value="Agras" <?php if ($tags == "Agras") echo "selected"; ?>>Agras</option>
            <option value="Consumer" <?php if ($tags == "Consumer") echo "selected"; ?>>Consumer</option>
            <option value="DEMO" <?php if ($tags == "DEMO") echo "selected"; ?>>DEMO</option>
            <option value="DN" <?php if ($tags == "DN") echo "selected"; ?>>DN</option>
            <option value="Ecoflow" <?php if ($tags == "Ecoflow") echo "selected"; ?>>Ecoflow</option>
            <option value="Enterprise" <?php if ($tags == "Enterprise") echo "selected"; ?>>Enterprise</option>
            <option value="Pecas" <?php if ($tags == "Pecas") echo "selected"; ?>>Pecas</option>
            <option value="Starlink" <?php if ($tags == "Starlink") echo "selected"; ?>>Starlink</option>
            <option value="Treinamento" <?php if ($tags == "Treinamento") echo "selected"; ?>>Treinamento</option>
        </select>
        <br><br>
        <?php if(!empty($imagem)){ ?>
        <label>Imagem atual:</label><br>
        <input type="text" value="<?php print $imagem;?>" style="width:92%;">
       <a href="javascript:func()" onclick="confirmacao('<?php print $id; ?>','<?php print $imagem;?>')" style="margin-left: 5px;">
        <img src="excluir.png" alt="Exclui Pessoa" border ="0" widht="20px" height="17px"></a>
        <br>
        <br>
        <div class="container-preview" style="display: flex; align-items: center;">
            <a href="<?php echo htmlspecialchars(trim($imagem)); ?>" 
               target="_blank" 
               rel="noopener noreferrer"
               title="Clique para abrir original">
               
               <img src="<?php echo htmlspecialchars(trim($imagem)); ?>" 
                    alt="Miniatura" 
                    style="width: 200px; height: 200px; border-radius: 4px; object-fit: cover; cursor: zoom-in; margin-left: 70px;">
               
               <img src="<?php echo htmlspecialchars(trim($imagem)); ?>" 
                    class="img-hover-zoom"
                    alt="Preview ampliado">
            </a>
        </div>
        <label>Modificar imagem:</label><br>
        <input type="file" name="cImagem" id="imagem" accept="image/*"><br>
            <?php } else { ?>
        <label>Adicionar nova imagem:</label><br>
        <input type="file" name="cImagem" id="imagem" accept="image/*"><br>
    <?php } ?>
    
        
        <input type="submit" value="Enviar" name="b1" class="btn"><br>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const campo = document.getElementById("preco");
    const form = document.getElementById("produtoForm");

    function formatarMoeda(valor) {
        // Remove tudo que não for número
        valor = valor.replace(/\D/g, "");

        // Se não houver valor ou for 0, retorna vazio
        if (!valor || valor === "0" || valor === "00") return "";

        // Garante pelo menos 3 dígitos
        while (valor.length < 3) {
            valor = "0" + valor;
        }

        let parteInteira = valor.slice(0, -2);
        let parteDecimal = valor.slice(-2);

        // Remove zeros à esquerda da parte inteira
        parteInteira = parteInteira.replace(/^0+/, "") || "0";

        // Formata milhares
        parteInteira = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Se for 0,00, retorna vazio
        if (parteInteira === "0" && parteDecimal === "00") return "";

        return `R$ ${parteInteira},${parteDecimal}`;
    }

    // Inicializa o campo já com o valor do PHP
    let valorAtual = campo.value.replace(/\D/g, "");
    campo.value = formatarMoeda(valorAtual);

    campo.addEventListener("input", function () {
        let somenteNumeros = campo.value.replace(/\D/g, "");
        campo.value = formatarMoeda(somenteNumeros);
    });

    campo.addEventListener("keydown", function (e) {
        if (campo.selectionStart <= 3 && (e.key === "Backspace" || e.key === "Delete")) {
            e.preventDefault();
        }
    });

    form.addEventListener("submit", function () {
        let somenteNumeros = campo.value.replace(/\D/g, "");

        // Se não houver valor ou for 0,00, limpa o campo
        if (!somenteNumeros || somenteNumeros === "0" || somenteNumeros === "00") {
            campo.value = "";
            return;
        }

        while (somenteNumeros.length < 3) {
            somenteNumeros = "0" + somenteNumeros;
        }

        let parteInteira = somenteNumeros.slice(0, -2).replace(/^0+/, "") || "0";
        let parteDecimal = somenteNumeros.slice(-2);

        // Se for 0,00, limpa o campo
        if (parteInteira === "0" && parteDecimal === "00") {
            campo.value = "";
        } else {
            campo.value = `${parteInteira},${parteDecimal}`;
        }
    });
});
</script>



</body>
</html>