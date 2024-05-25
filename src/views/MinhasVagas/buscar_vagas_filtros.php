<?php
include "../../services/conexão_com_banco.php";
// Informações Enviadas do AJAX 
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : null;
$area = isset($_POST['area']) ? $_POST['area'] : 'Todas';
$tipos = isset($_POST['tipos']) ? $_POST['tipos'] : array();
$vagasAbertas = isset($_POST['vagasAbertas']) ? $_POST['vagasAbertas'] === 'true' : false;
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] : '';

$tema = isset($_POST['tema']) ? $_POST['tema'] === 'true' : false; // Verifica se o modo noturno deve ser aplicado

// Segunda Consulta para selecionar o tema que salvo no banco de dados
$query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
$stmt = $_con->prepare($query);

// Verifique se a preparação foi bem-sucedida
if ($stmt) {
    $stmt->bind_param('i', $idPessoa);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $row = $result->fetch_assoc();
        $tema = $row['Tema'] ?? null;
    } else {
        $tema = null;
    }
    $stmt->close();
} else {
    die("Erro ao preparar a query: " . $_con->error);
}


// Verificação do IdPessoa está presente 
if (is_null($idPessoa)) {
    // ! Arrumar questão de tratamento de Erros !! 
    echo "ID da pessoa não fornecido";
    exit;
}

// Primeira Consulta ao banco de dados 
$sql = "SELECT e.CNPJ
        FROM Tb_Pessoas p
        INNER JOIN Tb_Empresa e ON p.Id_Pessoas = e.Tb_Pessoas_Id
        WHERE p.Id_Pessoas = '$idPessoa'";

// Armazena o CNPJ da empresa 
$result = $_con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $cnpj_empresa = $row["CNPJ"];
}
// Segunda Consulta para Obter o Nome da Empresa 
$sql_nome_empresa = "SELECT Nome_da_Empresa FROM Tb_Empresa WHERE CNPJ = ?";
$stmt = $_con->prepare($sql_nome_empresa);
$stmt->bind_param("s", $cnpj_empresa);
$stmt->execute();

// Armazena o nome da empresa 
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nome_empresa = $row['Nome_da_Empresa'];
} else {
    // ! Arrumar questão de tratamento de Erros !! 
    $nome_empresa = 'Empresa não encontrada';
}

// Terceira Consulta ao banco de dados 
$sql = "SELECT * 
        FROM Tb_Anuncios 
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ
        JOIN Tb_Pessoas ON Tb_Empresa.Tb_Pessoas_Id = Tb_Pessoas.Id_Pessoas
        WHERE Tb_Pessoas.Id_Pessoas = ?";

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
        $parametros[] = '%' . $termoPesquisa . '%';
    }
    $stmt->bind_param(str_repeat('s', count($parametros)), ...$parametros);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Gerar HTML para cada vaga encontrada
        while ($row = $result->fetch_assoc()) {
            // Consulta para contar o número de inscritos para cada vaga
            $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
            $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
            $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]);
            $stmt_inscricoes->execute();
            $result_inscricoes = $stmt_inscricoes->get_result();
            $total_inscricoes = $result_inscricoes->fetch_assoc()['total_inscricoes'] ?? 0;
            $stmt_inscricoes->close();

            // Obter o nome da empresa
            $nome_empresa = $row['Nome_da_Empresa'] ?? 'Empresa não identificada';

            // Data de criação
            $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";

            // Gerar HTML para cada vaga
            echo '<a class="postLink" href="../Vaga/vaga.php?id=' . $row["Id_Anuncios"] . '">';
            echo '<article class="post">';
            echo '<div class="divAcessos">';
            if ($tema == 'noturno') {
                echo '<img src="../../assets/images/icones_diversos/peopleWhite.svg"></img>';
            } else {
                echo '<img src="../../assets/images/icones_diversos/people.svg"></img>';
            }
            echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
            echo '</div>';

            echo '<header>';
            switch ($row["Categoria"]) {
                case "CLT":
                    echo '<img src="../../../imagens/clt.svg">';
                    echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
                    break;
                case "Estágio":
                case "Jovem Aprendiz":
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
            echo '<h3 class="nomeVaga">' . ($row["Titulo"] ?? "Título não definido") . '</h3>';
            if (empty($nome_empresa)) {
                $nome_empresa = 'Confidencial';
            }
            echo '<p class="empresaVaga">' . $nome_empresa . '</p>';

            if ($row['Status'] == 'Aberto') {
                echo '<h4 class="statusVaga" style="color:green">Aberto</h4>';
                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
            } else {
                echo '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
                echo '<p class="dataVaga">' . $dataCriacao . '</p>';
            }
            echo '</section>';
            echo '</article>';
            echo '</a>';
        }
    } else {
        echo "<p class='infos' style='text-align:center; margin:0 auto; position: absolute'>Nenhuma vaga encontrada com os filtros selecionados.</p>";
    }

    $stmt->close();
} else {
    echo "Erro na preparação da consulta: " . $_con->error;
}

$_con->close();
?>