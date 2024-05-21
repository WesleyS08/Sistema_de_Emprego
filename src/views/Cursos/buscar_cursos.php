<?php

include "../../services/conexão_com_banco.php";

// Obtendo o parâmetro de busca
$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';

// Obtendo o parâmetro de busca
$titulo = isset($_GET['titulo']) ? $_GET['titulo'] : '';

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

// Array para armazenar os cursos
$cursos = [];

// Iterando sobre os resultados e armazenando em um array
while ($row = $resultado->fetch_assoc()) {
    $cursos[] = $row;
}

// Fechando a declaração e a conexão
$stmt->close();
$_con->close();

// Retornando os cursos como JSON
header('Content-Type: application/json');
echo json_encode($cursos);
?>