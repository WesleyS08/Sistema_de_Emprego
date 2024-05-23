<?php
include "../../services/conexão_com_banco.php";

// Receber o termo de pesquisa e o ID da pessoa usando POST
$termo = isset($_POST['termo']) ? $_POST['termo'] : '';
$idPessoa = isset($_POST['idPessoa']) ? intval($_POST['idPessoa']) : 0;

if ($idPessoa > 0) {
    // Consulta para selecionar o tema salvo no banco de dados
    $query = "SELECT Tema FROM Tb_Pessoas WHERE Id_Pessoas = ?";
    $stmt = $_con->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $idPessoa);
        $stmt->execute();
        $result = $stmt->get_result();
        $tema = $result ? ($result->fetch_assoc()['Tema'] ?? null) : null;
        $stmt->close();
    } else {
        die("Erro ao preparar a query: " . $_con->error);
    }
}


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

// Consulta SQL para buscar vagas que correspondem ao termo de pesquisa
$sql = "SELECT A.*, V.Status, E.Nome_da_Empresa
        FROM Tb_Anuncios A 
        INNER JOIN Tb_Vagas V ON A.Id_Anuncios = V.Tb_Anuncios_Id
        INNER JOIN Tb_Empresa E ON V.Tb_Empresa_CNPJ = E.CNPJ
        WHERE A.Titulo LIKE ?
        LIMIT 10";

$stmt = $_con->prepare($sql);
$likeTerm = "%" . $termo . "%";
$stmt->bind_param("s", $likeTerm);
$stmt->execute();
$result = $stmt->get_result();

$htmlVagas = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Consulta para contar o número de inscrições para cada vaga
        $sql_contar_inscricoes = "SELECT COUNT(*) AS total_inscricoes FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Anuncios_Id = ?";
        $stmt_inscricoes = $_con->prepare($sql_contar_inscricoes);
        $stmt_inscricoes->bind_param("i", $row["Id_Anuncios"]);
        $stmt_inscricoes->execute();
        $result_inscricoes = $stmt_inscricoes->get_result();
        $total_inscricoes = $result_inscricoes ? $result_inscricoes->fetch_assoc()['total_inscricoes'] : 0;
        $stmt_inscricoes->close();

        // Exibir a vaga e o número de inscritos
        $htmlVagas .= '<a class="postLink" href="../MinhaVaga/minhaVaga.php?id=' . htmlspecialchars($row["Id_Anuncios"]) . '">';
        $htmlVagas .= '<article class="post">';
        $htmlVagas .=  '<div class="divAcessos">';
        if ($tema == 'noturno') {
            $htmlVagas .= '<img src="../../assets/images/icones_diversos/peopleWhite.svg"></img>';
        } else {
        $htmlVagas .= '<img src="../../assets/images/icones_diversos/people.svg"></img>';
        }
        $htmlVagas .=  '<small class="qntdAcessos">' . $total_inscricoes . '</small>';
        $htmlVagas .=  '</div>';
        $htmlVagas .= '<header>';
        switch ($row["Categoria"]) {
            case "CLT":
                $htmlVagas .= '<img src="../../../imagens/clt.svg">';
                $htmlVagas .= '<label class="tipoVaga">' . htmlspecialchars($row["Categoria"]) . '</label>';
                break;
            case "Estágio":
            case "Jovem Aprendiz":
                $htmlVagas .= '<img src="../../../imagens/estagio.svg">';
                $htmlVagas .= '<label class="tipoVaga">' . htmlspecialchars($row["Categoria"]) . '</label>';
                break;
            case "PJ":
                $htmlVagas .= '<img src="../../../imagens/pj.svg">';
                $htmlVagas .= '<label class="tipoVaga">' . htmlspecialchars($row["Categoria"]) . '</label>';
                break;
            default:
                $htmlVagas .= '<label class="tipoVaga">Categoria não definida</label>';
                break;
        }
        $htmlVagas .= '</header>';
        $htmlVagas .= '<section>';
        $tituloVaga = htmlspecialchars($row["Titulo"]);
        $htmlVagas .= '<h3 class="nomeVaga">' . (strlen($tituloVaga) > 14 ? substr($tituloVaga, 0, 20) . '...' : $tituloVaga) . '</h3>';
        $htmlVagas .= '<p class="empresaVaga"> Empresa: ' . htmlspecialchars($row['Nome_da_Empresa']) . '</p>';
        $dataCriacao = isset($row["Data_de_Criacao"]) ? date("d/m/Y", strtotime($row["Data_de_Criacao"])) : "Data não definida";
        $datadeTermino = isset($row["Data_de_Termino"]) ? date("d/m/Y", strtotime($row["Data_de_Termino"])) : "Data não definida";
        if ($row['Status'] == 'Aberto') {
            $htmlVagas .= '<h4 class="statusVaga" style="color:green">Aberto</h4>';
            $htmlVagas .= '<p class="dataVaga">' . htmlspecialchars($dataCriacao) . '</p>';
        } else {
            $htmlVagas .= '<h4 class="statusVaga" style="color:red">' . htmlspecialchars($row['Status']) . '</h4>';
            $htmlVagas .= '<p class="dataVaga">' . htmlspecialchars($datadeTermino) . '</p>';
        }
        $htmlVagas .= '</section>';
        $htmlVagas .= '</article>';
        $htmlVagas .= '</a>';
    }
} else {
    $htmlVagas .= '<div class="sem-resultados">Nenhuma vaga encontrada</div>';
}

$stmt->close();
echo $htmlVagas;
$_con->close();
?>