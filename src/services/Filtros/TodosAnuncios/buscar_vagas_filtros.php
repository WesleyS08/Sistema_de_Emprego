<?php
// Conexão com o banco de dados
include '../../conexão_com_banco.php';
// Receber os filtros enviados pela solicitação AJAX
$area = isset($_POST['area']) ? $_POST['area'] : 'Todas';
$tipos = isset($_POST['tipos']) ? $_POST['tipos'] : [];
$vagasAbertas = isset($_POST['vagasAbertas']) ? $_POST['vagasAbertas'] === 'true' : false;
$termoPesquisa = isset($_POST['termo']) ? $_POST['termo'] : '';

// Iniciar a consulta SQL para obter vagas com ou sem filtros
$sql = "SELECT * 
        FROM Tb_Anuncios 
        JOIN Tb_Vagas ON Tb_Anuncios.Id_Anuncios = Tb_Vagas.Tb_Anuncios_Id
        JOIN Tb_Empresa ON Tb_Vagas.Tb_Empresa_CNPJ = Tb_Empresa.CNPJ";

// Adicionar condições para cada filtro, conforme necessário
$parametros = [];
$filtros = [];

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

// Filtrar por termo de pesquisa
if (!empty($termoPesquisa)) {
    $filtros[] = "Tb_Anuncios.Titulo LIKE ?";
    $parametros[] = '%' . $termoPesquisa . '%';
}

// Adicionar filtros à consulta
if (!empty($filtros)) {
    $sql .= " WHERE " . implode(" AND ", $filtros);
}

// Preparar a consulta com parâmetros vinculados
$stmt = $_con->prepare($sql);


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

            // Obter o nome da empresa
            $nome_empresa = $row['Nome_da_Empresa'] ?? 'Empresa não identificada';

            // Data de criação
            $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";

            // Gerar HTML para cada vaga
            echo '<a class="postLink" href="../../../views/User/InformaçõesdoAnuncio/vaga.php?id=' . $row["Id_Anuncios"] . '">';
            echo '<article class="post">';
            echo '<div class="divAcessos">';
            echo '<img src="../../../../imagens/people.svg"></img>';
            echo '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
            echo '</div>';

            echo '<header>';
            echo '<img src="../../../../imagens/' . determinarImagemCategoria($row["Categoria"]) . '.svg">';
            echo '<label class="tipoVaga">' . $row["Categoria"] . '</label>';
            echo '</header>';

            echo '<section>';
            echo '<h3 class="nomeVaga">' . ($row["Titulo"] ?? "Título não definido") . '</h3>';
                // Se não houver empresa, definir um valor padrão
                if (empty($nome_empresa)) {
                    $nome_empresa = 'Confidencial';
                }

                // Agora pode imprimir
                echo '<p class="empresaVaga"> Empresa:' . $nome_empresa . '</p>';


                if ($row['Status'] == 'Aberto') {
                    echo '<h4 class="statusVaga" style="color:green">Aberto</h4>';
                    echo '<p class="dataVaga">' . $dataCriacao . '</p>';
                } else {
                    echo '<h4 class="statusVaga" style="color:red">' . $row['Status'] . '</h4>';
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

$_con->close();
?>
