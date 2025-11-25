
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>FORMULÁRIO PRODUTO</title>
<style>
/* --- Estilo geral --- */
body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #dfe9f3 0%, #ffffff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}


.container {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 30px 40px;
    width: 350px;
}

label {
    display: block;
    margin-top: 10px;
    color: #444;
    font-weight: 600;
}

input[type="text"] {
    width: 93%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="file"] {
    width: 93%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-top: 5px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input:focus {
    border-color: #0078d7;
    box-shadow: 0 0 5px rgba(0,120,215,0.4);
    outline: none;
}

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
  padding: 10px 40px 10px 15px; /* reduzido para caber o texto */
  font-size: 16px;
  color: #333;
  cursor: pointer;
  width: 100%; /* garante que ocupe toda a largura do container */
  box-sizing: border-box; /* evita estouro do container */
  background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
  background-repeat: no-repeat;
  background-position: right 10px center;
  transition: all 0.3s ease;
}

.styled-select:hover {
  border-color: #888;
}

.styled-select:focus {
  border-color: #0078ff;
  box-shadow: 0 0 4px rgba(0, 120, 255, 0.4);
  outline: none;
}

.error-message {
    background-color: #ffebeb;
    color: #cc0000;
    border: 1px solid #cc0000;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}

</style>
</head>
<body>

<div class="container">
    <h1>Cadastrar itens</h1>
    <hr>
    <?php
    // ESTE É O NOVO BLOCO PHP QUE DEVE SER INCLUÍDO
    // Ele verifica se a variável 'error' existe na URL e exibe a mensagem.
    if (isset($_GET['error'])) {
        $erro = htmlspecialchars($_GET['error']);
        echo '<div class="error-message">' . $erro . '</div>';
    }
    ?>
    <form action="gravaProduto.php" id="produtoForm" enctype="multipart/form-data" method="post">
        <label>NCM: </label>
        <input type="text" id="ncm" name="cNcm" required>

        <label>SKU: </label>
        <input type="text" id="ecoflow_sku" name="cEcoflow_sku" required>

        <label>Nome do produto:</label>
        <input type="text" id="name" name="cName" required>
        <br>
        <label>Preço:</label>
        <input type="text" id="preco" name="cPreco" placeholder="R$ 0,00"
        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg transition duration-150 ease-in-out"
        nputmode="numeric" required>
        <br>
        <label>Fabricante:</label>
        <SELECT name = "cFabricante" id="fabricante" class="styled-select" required><br><br>
        <option value="">Selecione a opção</option>
        <OPTION SELECT VALUE ="PGYTECH">PGYTECH
        <OPTION SELECT VALUE ="DJI">DJI
        <OPTION SELECT VALUE ="ECOFLOW">ECOFLOW         
        <OPTION SELECT VALUE="AUTEL">AUTEL</option>
        <OPTION SELECT VALUE="MICASENSE">MICASENSE</option>
        <OPTION SELECT VALUE="SPACEX">SPACEX</option>
        </select>

            
        </SELECT><br>

        <label>Fornecedor:</label>
        <SELECT name = "cFornecedor" id="fornecedor" class="styled-select" required><br><br>
        <option value="">Selecione a opção</option>
        <OPTION SELECT VALUE ="MULTILASER">MULTILASER
        <OPTION SELECT VALUE ="INTELBRAS">INTELBRAS
        <OPTION SELECT VALUE ="TIMBER">TIMBER
        <OPTION SELECT VALUE ="GOLDEN DISTRIBUIDORA LTDA">GOLDEN DISTRIBUIDORA LTDA
        <OPTION SELECT VALUE ="GOHOBBYT FUTURE TECHNOLOGY LTDA">GOHOBBYT FUTURE TECHNOLOGY LTDA
        <OPTION SELECT VALUE ="ALLCOMP">ALLCOMP
        <OPTION SELECT VALUE ="DRONENERDS">DRONENERDS
        <OPTION SELECT VALUE ="SANTIAGO & SINTRA">SANTIAGO & SINTRA
        <OPTION SELECT VALUE ="POWERSAFE">POWERSAFE
        <OPTION SELECT VALUE ="AGEAGLE AERIAL SYSTEMS INC">AGEAGLE AERIAL SYSTEMS INC
        <OPTION SELECT VALUE ="STARLINK">STARLINK
        </SELECT><br>

        <label>Tags:</label>
        <SELECT name = "cTags" id="tags" class="styled-select" required><br><br>
        <OPTION VALUE="">Selecione a opção</option>
        <OPTION SELECT VALUE ="Agras">Agras
        <OPTION SELECT VALUE ="Consumer">Consumer
        <OPTION SELECT VALUE ="DEMO">DEMO
        <OPTION SELECT VALUE ="DN">DN
        <OPTION SELECT VALUE ="Ecoflow">Ecoflow
        <OPTION SELECT VALUE ="Enterprise">Enterprise
        <OPTION SELECT VALUE ="Pecas">Pecas
        <OPTION SELECT VALUE ="Starlink">Starlink
        <OPTION SELECT VALUE ="Treinamento">Treinamento
        </SELECT><br>
        
        <label>Adicionar imagem:</label>
        <input type="file" id="imagem" name="cImagem" accept="image/*"><br>  
        <input type="submit" value="Inserir" class="btn">
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const campo = document.getElementById("preco");
    const form = document.getElementById("produtoForm");

    function formatarMoeda(valor) {
        // Mantém só números
        valor = valor.replace(/\D/g, "");

        if (!valor) return ""; // Retorna vazio se não houver valor

        // Se tiver menos de 3 dígitos, completa à esquerda
        while (valor.length < 3) {
            valor = "0" + valor;
        }

        let parteInteira = valor.slice(0, -2);
        let parteDecimal = valor.slice(-2);

        // Remove zeros à esquerda da parte inteira
        parteInteira = parteInteira.replace(/^0+/, "");
        if (parteInteira === "") parteInteira = "0";

        // Formata milhares
        parteInteira = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        // Se o valor for 0,00, retorna vazio
        if (parteInteira === "0" && parteDecimal === "00") return "";

        return `R$ ${parteInteira},${parteDecimal}`;
    }

    // Inicializa o campo vazio
    campo.value = "";

    campo.addEventListener("input", function () {
        let somenteNumeros = campo.value.replace(/\D/g, "");

        campo.value = formatarMoeda(somenteNumeros);
    });

    campo.addEventListener("keydown", function (e) {
        if (campo.selectionStart <= 3 && (e.key === "Backspace" || e.key === "Delete")) {
            e.preventDefault();
        }
    });

    form.addEventListener("submit", function (e) {
        let somenteNumeros = campo.value.replace(/\D/g, "");
        if (!somenteNumeros) return; // Se estiver vazio, não faz nada

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
