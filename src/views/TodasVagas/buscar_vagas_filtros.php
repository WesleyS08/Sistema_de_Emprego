<?php
// Conexão com o banco de dados
include "../../services/conexão_com_banco.php";

// Função para remover palavras de ligação do termo de pesquisa
function removerPalavrasDeLigacao($string, $palavras_de_ligacao)
{
    $string = strtolower($string);
    // Dividir a string em palavras
    $palavras = explode(" ", $string);
    // Remover espaços em branco e palavras de ligação
    $palavras = array_filter($palavras, function ($palavra) use ($palavras_de_ligacao) {
        return !in_array($palavra, $palavras_de_ligacao) && !empty (trim($palavra));
    });
    // Reunir as palavras filtradas em uma string novamente
    return implode(" ", $palavras);
}

// Receber os filtros enviados pela solicitação AJAX
$area = isset($_POST['area']) ? $_POST['area'] : 'Todas';
$tipos = isset($_POST['tipos']) ? $_POST['tipos'] : [];
$vagasAbertas = isset($_POST['vagasAbertas']) ? $_POST['vagasAbertas'] === 'true' : false;
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] : '';
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : 0;
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

// Defina suas palavras de ligação
$palavras_de_ligacao = array('de', 'em', 'para', 'com', 'por', 'sem');

// Remover palavras de ligação do termo de pesquisa
$termoPesquisa = removerPalavrasDeLigacao($termoPesquisa, $palavras_de_ligacao);

// Dividir o termo de pesquisa em palavras
$palavrasChave = explode(" ", $termoPesquisa);

// Iniciar a consulta SQL para obter vagas com ou sem filtros
$sql = "SELECT * 
        FROM Tb_Anuncios 
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ";

// Adicionar condições para cada filtro, conforme necessário
$parametros = [];
$filtros = [];

foreach ($palavrasChave as $index => $palavra) {
    $parametro = "termo" . ($index + 1);
    $filtros[] = "Tb_Anuncios.Titulo LIKE ?";
    $parametros[] = '%' . $palavra . '%';
}

// Função para determinar a imagem com base na categoria do trabalho
function determinarImagemCategoria($categoria)
{
    switch ($categoria) {
        case 'Estágio':
            return 'estagio';
        case 'CLT':
            return 'clt';
        case 'PJ':
            return 'pj';
        case 'Jovem Aprendiz':
            return 'estagio';
        default:
            return 'default';
    }
}

// Filtrar por área
if ($area != 'Todas') {
    $filtros[] = "Tb_Anuncios.Area = ?";
    $parametros[] = $area;
}

// Filtrar por tipos
if (!empty($tipos)) {
    $filtros[] = "Tb_Anuncios.Categoria IN ('" . implode("','", $tipos) . "')";
}

// Filtrar por vagas abertas
if ($vagasAbertas) {
    $filtros[] = "Tb_Vagas.Status = 'Aberto'";
}

// Adicionar filtros à consulta
if (!empty($filtros)) {
    $sql .= " WHERE " . implode(" AND ", $filtros);
}

// Preparar a consulta com parâmetros vinculados
$stmt = $_con->prepare($sql);

if ($stmt) {
    // Vincular parâmetros, se necessário
    if (!empty($parametros)) {
        $stmt->bind_param(str_repeat('s', count($parametros)), ...$parametros);
    }

    // Executar a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar se há resultados
    if ($result->num_rows > 0) {

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
            echo '<img src="../../../imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">';
            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
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