<?php
include "conecta.php";

$txtConteudo = filter_input_array(INPUT_GET, FILTER_DEFAULT);
if (isset($txtConteudo["id"])) {
    $varId = $txtConteudo["id"];
    $sql = "SELECT * FROM TBPRODUTO WHERE id = '".$varId."'";
    $rs = mysqli_query($conexao, $sql);
    $reg = mysqli_fetch_array($rs);

    $id = $reg["id"];
    $imagem = $reg["imagem"];
} else {
    echo "REGISTRO NÃO LOCALIZADO!";
    echo "<meta HTTP-EQUIV='Refresh' CONTENT='5;URL=consultaProduto.php'>";
    exit;
}
?>

<html>
<head>
<title> Alterar dados do produto </title>
<head>
<body>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  #drop-area {
    width: 300px;
    height: 200px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    text-align: center;
    line-height: 200px;
    font-family: Arial, sans-serif;
    color: #aaa;
    margin: 50px auto;
    cursor: pointer;
  }
  #drop-area img {
    max-width: 100%;
    max-height: 100%;
    display: block;
    margin: 0 auto;
  }

  /* Fundo geral e estilo base */
body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #dfe9f3 0%, #ffffff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Caixa principal */
.container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 30px 40px;
    width: 400px;
    animation: fadeIn 0.4s ease;
}

 .drop-area {
    width: 300px;
    height: 200px;
    border: 2px dashed #ccc;
    border-radius: 10px;
    text-align: center;
    line-height: 200px;
    font-family: Arial, sans-serif;
    color: #aaa;
    margin: 50px auto;
  }
  .drop-area img {
    max-width: 100%;
    max-height: 100%;
    display: block;
    margin: 0 auto;
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
</style>
<div class="container">
<h1>CADASTRAR IMAGEM </H1> <HR><BR>
<form action="adicionaimagem.php" method="post" enctype="multipart/form-data"> 
<input type="hidden" name="cId" value="<?php echo $id;?>"/>
<div class="div1">
<div class="div2">
<br>
<label>Cadastrar imagem:</label><br>
<input type="file" name="cImagem" accept="image/*" enctype="multipart/form-data"><br>
<input type="submit" value="Enviar" name="b1" class="btn"><br>
</div>
<br>
<br>
</div>
</div>

</form>

</body>
</html>

