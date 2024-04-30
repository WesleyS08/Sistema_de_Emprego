<?php
include "../../services/conexão_com_banco.php";

// Receber os filtros enviados pela solicitação AJAX
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : null; // Certifique-se de que este valor é seguro para uso no SQL
$area = isset($_POST['area']) ? $_POST['area'] : 'Todas';
$tipos = isset($_POST['tipos']) ? $_POST['tipos'] : array();
$vagasAbertas = isset($_POST['vagasAbertas']) ? $_POST['vagasAbertas'] === 'true' : false;
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] : '';

// Verifique se temos um ID de pessoa antes de continuar
if (is_null($idPessoa)) {
    echo "ID da pessoa não fornecido";
    exit;
}

$sql = "SELECT e.CNPJ
        FROM Tb_Pessoas p
        INNER JOIN Tb_Empresa e ON p.Id_Pessoas = e.Tb_Pessoas_Id
        WHERE p.Id_Pessoas = '$idPessoa'";

$result = $_con->query($sql);

if ($result->num_rows > 0) {
    // Armazenar o CNPJ da empresa na variável $cnpj_empresa
    $row = $result->fetch_assoc();
    $cnpj_empresa = $row["CNPJ"];
}
// Preparar a consulta para obter o nome da empresa
$sql_nome_empresa = "SELECT Nome_da_Empresa FROM Tb_Empresa WHERE CNPJ = ?";
$stmt = $_con->prepare($sql_nome_empresa);

// Bind the parameter
$stmt->bind_param("s", $cnpj_empresa);

// Execute
$stmt->execute();

// Fetch result
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nome_empresa = $row['Nome_da_Empresa'];
} else {
    // Handle no results case
    $nome_empresa = 'Empresa não encontrada';
}


// Construir a consulta SQL com base nos filtros
$sql = "SELECT * 
        FROM Tb_Anuncios 
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
        WHERE Tb_Pessoas.Id_Pessoas = ?"; // Adicionamos o filtro pelo ID da pessoa

// Adicionar filtro de área, se selecionado
if ($area != 'Todas') {
    $sql .= " AND Tb_Anuncios.Area = ?";
}

// Adicionar filtro de tipos, se selecionado
if (!empty($tipos)) {
    $sql .= " AND Tb_Anuncios.Categoria IN ('" . implode("','", $tipos) . "')";
}

// Adicionar filtro para vagas abertas
if ($vagasAbertas) {
    $sql .= " AND Tb_Vagas.Status = 'Aberto'";
}

// Adicionar filtro de termo de pesquisa por título
if (!empty($termoPesquisa)) {
    $sql .= " AND Tb_Anuncios.Titulo LIKE ?";
}

// Preparar a consulta com parâmetros vinculados
$stmt = $_con->prepare($sql);

// Vincular parâmetros
if ($stmt) {
    $parametros = [$idPessoa]; // Sempre vincule o ID da pessoa

    if ($area != 'Todas') {
        $parametros[] = $area;
    }

    if (!empty($termoPesquisa)) {
        $parametros[] = '%' . $termoPesquisa . '%'; // Usar wildcards para pesquisa de texto
    }

    $stmt->bind_param(str_repeat('s', count($parametros)), ...$parametros);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Gerar HTML para cada vaga encontrada
        while ($row = $result->fetch_assoc()) {

            // Consulta para contar o número de inscritos para esta vaga
            $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
            $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
            $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]); // "i" indica que o parâmetro é um inteiro
            $stmt_inscricoes->execute();
            $result_inscricoes = $stmt_inscricoes->get_result();

            // Verificar se a consulta teve sucesso
            if ($result_inscricoes === false) {
                // Tratar o erro, se necessário
                echo "Erro na consulta de contagem de inscrições: " . $_con->error;
                exit;
            }

            // Obter o resultado da contagem de inscrições
            $row_inscricoes = $result_inscricoes->fetch_assoc();
            $total_inscricoes = $row_inscricoes['total_inscricoes'];

            // HTML para cada vaga
            echo '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . $row["Id_Anuncios"] . '">';
            echo '<article class="post">';
            echo '<div class="divAcessos">';
            echo '<img src="../../../imagens/people.svg"></img>';
            echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
            echo '</div>';

            echo '<header>';
            switch ($row["Categoria"]) {
                case "CLT":
                    echo '<img src="../../../imagens/clt.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                case "Estágio":
                case "Jovem Aprendiz": // Caso tenham a mesma aparência visual
                    echo '<img src="../../../imagens/estagio.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                case "PJ":
                    echo '<img src="../../../imagens/pj.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                default:
                    echo '<label class="tipoVaga">Categoria não definida</label>';
                    break;
            }
            echo '</header>';

            echo '<section>';
            echo '<h3 class="nomeVaga">' . (isset($row["Titulo"]) ? $row["Titulo"] : "Título não definido") . '</h3>';
            echo '<p class="empresaVaga">' . (isset($row["Descricao"]) ? $row["Descricao"] : "Descrição não definida") . '</p>';
            if (empty($nome_empresa)) {
                $nome_empresa = 'Confidencial';
            }

            // Agora pode imprimir
            echo '<p class="empresaVaga">' . $nome_empresa . '</p>';

            $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
            if ($row['Status'] == 'Aberto') {
                echo '<p style="color: green;">' . $row['Status'] . '</p>';
                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
            } else {
                echo '<p style="color: red;">' . $row['Status'] . '</p>';
                echo '<p class="dataVaga">' . $datadeTermino . '</p>';
            }
            echo '</section>';

            echo '</article>';
            echo '</a>';
        }
    } else {
        echo "Nenhuma vaga encontrada com os filtros selecionados.";
    }

    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $_con->error;
}
?>