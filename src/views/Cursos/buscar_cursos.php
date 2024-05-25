<?php
include "../../services/conexão_com_banco.php";

// Obtendo o parâmetro de busca
$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';

// Verificando o tema atual (noturno ou claro)
$idPessoa = isset($_GET['idPessoa']) ? $_GET['idPessoa'] : '';

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
        if ($row && isset($row['Tema'])) {
            $tema = $row['Tema'];
        } else {
            $tema = null;
        }
    } else {
        $tema = null;
    }
} else {
    // ! Arrumar questão de tratamento de Erros !! 
    die("Erro ao preparar a query.");
}

$cor_texto = ($tema === 'noturno') ? 'color: whitesmoke;' : 'color: black;';

// Preparando a query SQL
$sql = "SELECT Nome_do_Curso as nome, Duração as duracao, Nivel as nivel, Link as link, Descricao as descricao, URL_da_Imagem as url_imagem, Categoria as categoria 
        FROM Tb_Cursos 
        WHERE Nome_do_Curso LIKE ?";

// Preparando a declaração
$stmt = $_con->prepare($sql);
$param = "%" . $titulo . "%";
$stmt->bind_param("s", $param);

// Executando a declaração
$stmt->execute();

// Obtendo o resultado
$resultado = $stmt->get_result();

// Inicializando a variável para armazenar o HTML de saída
$html_output = '';

// Iterando sobre os resultados e gerando o HTML
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        // Verifica se o tema é noturno e define a cor do texto
        $estilo_texto = ($tema === 'noturno') ? 'style="' . $cor_texto . '"' : '';
        // Abre o link do curso
        $html_output .= '<a class="cursoLink" style="cursor: pointer;" href="' . $row['link'] . '" title="' . $row['nome'] . '" target="_blank">';
        // Abre o artigo do curso
        if ($tema === 'noturno') {
            $html_output .= '<article class="curso" style="box-shadow: none;">';
        } else {
            $html_output .= '<article class="curso">';
        }

        // Abre a div do logo do curso
        $html_output .= '<div class="divLogoCurso">';
        // Adiciona a imagem do curso
        $html_output .= '<img src="' . $row['url_imagem'] . '" alt="Imagem do curso ' . $row['nome'] . '" style="width: 100px; height: 100px;">';
        // Fecha a div do logo do curso
        $html_output .= '</div>';
        // Abre a seção do curso
        $html_output .= '<section>';
        // Adiciona o parágrafo da empresa do curso
        $html_output .= '<p id="empresaCurso" ' . $estilo_texto . '>' . $row['categoria'] . '</p>';
        // Adiciona o título do curso com o estilo definido
        $html_output .= '<h3 ' . $estilo_texto . '>' . substr($row['nome'], 0, 18) . '...</h3>';
        // Abre a div para os elementos flexíveis
        $html_output .= '<div class="divFlexSpace">';
        // Adiciona o nível do curso
        $html_output .= '<p ' . $estilo_texto . '>' . $row['nivel'] . '</p>';
        // Adiciona a duração do curso
        $html_output .= '<p ' . $estilo_texto . '>Duração: ' . $row['duracao'] . '</p>';
        // Fecha a div dos elementos flexíveis
        $html_output .= '</div>';
        // Fecha a seção do curso
        $html_output .= '</section>';
        // Fecha o artigo do curso
        $html_output .= '</article>';
        // Fecha o link do curso
        $html_output .= '</a>';
        // Fecha a div principal do curso
        $html_output .= '</div>';
    }
} else {
    if ($tema === 'noturno') {
        $html_output = '<p class="infos" style="text-align: center;
    margin-top: 5%;
    position: absolute; color: silver">Nenhum Curso encontrado com os filtros selecionados.</p>';
    } else {
        $html_output = '<p class="infos" style="text-align: center;
    margin-top: 5%;
    position: absolute; color: black">Nenhum Curso encontrado com os filtros selecionados.</p>';
    }
}

// Fechando a declaração e a conexão
$stmt->close();
$_con->close();

// Retornando o HTML gerado
echo $html_output;
?>