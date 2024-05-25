<?php

function conectarBancoDados()
{
    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $banco = "SIAS";

    // Criando conexão
    $conexao = new mysqli($host, $usuario, $senha, $banco);

    // Verificando a conexão
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    return $conexao;
}

function buscarCursos($pesquisa)
{
    // Conecta ao banco de dados
    $conexao = conectarBancoDados();

    // Prepara a consulta SQL para buscar todos os cursos
    $sql = "SELECT Id_Cursos, Nome_do_Curso, Oferecido_Por, Duração, Nivel, Link, URL_da_Imagem, Tipo, Categoria, cliques
            FROM Tb_Cursos";

    // Executa a consulta
    $resultado = $conexao->query($sql);

    // Verifica se há cursos
    if ($resultado->num_rows > 0) {
        // Array para armazenar os cursos
        $cursos = [];

        // Itera sobre os resultados e armazena em um array
        while ($curso = $resultado->fetch_assoc()) {
            // Calcula a distância de Levenshtein entre a pesquisa e o nome do curso
            $curso['distancia'] = levenshtein($pesquisa, $curso['Nome_do_Curso']);
            $cursos[] = $curso;
        }

        // Ordena os cursos pela distância de Levenshtein (menor distância primeiro) e número de cliques (maior primeiro)
        usort($cursos, function ($a, $b) {
            // Se as distâncias são iguais, ordena pelos cliques (maior primeiro)
            if ($a['distancia'] == $b['distancia']) {
                return $b['cliques'] - $a['cliques'];
            }
            // Ordena pela distância de Levenshtein (menor primeiro)
            return $a['distancia'] - $b['distancia'];
        });

        // Seleciona os 10 cursos mais próximos
        $cursos_recomendados = array_slice($cursos, 0, 10);

        // Fecha a conexão
        $conexao->close();

        // Retorna os cursos recomendados
        return $cursos_recomendados;
    } else {
        // Fecha a conexão
        $conexao->close();

        // Retorna um array vazio se nenhum curso for encontrado
        return [];
    }
}

function salvarRecomendacoes($cpfCandidato, $cursos)
{
    // Conecta ao banco de dados
    $conexao = conectarBancoDados();

    // Prepara as consultas SQL para inserir ou atualizar as recomendações
    foreach ($cursos as $curso) {
        $sql = "INSERT INTO Tb_Recomendacoes (Tb_Candidato_CPF, Tb_Cursos_Id)
                VALUES ('$cpfCandidato', {$curso['Id_Cursos']})
                ON DUPLICATE KEY UPDATE Tb_Cursos_Id = VALUES(Tb_Cursos_Id)";
        $conexao->query($sql);
    }

    // Fecha a conexão
    $conexao->close();
}

// Verifica se os dados foram enviados corretamente
if (isset($_POST['pesquisa']) && isset($_POST['idPessoa'])) {
    // Obtém o valor da pesquisa e o ID da pessoa
    $pesquisa = $_POST['pesquisa'];
    $idPessoa = $_POST['idPessoa'];

    // Consulta SQL para obter o CPF do candidato com base no ID da pessoa
    $conexao = conectarBancoDados();
    $sql = "SELECT c.CPF 
    FROM Tb_Pessoas p
    JOIN Tb_Candidato c ON p.Id_Pessoas = c.Tb_Pessoas_Id
    WHERE p.Id_Pessoas = ?";

    // Preparar a declaração
    $stmt = $conexao->prepare($sql);

    // Verificar se a preparação da declaração foi bem-sucedida
    if ($stmt === false) {
        die("Erro na preparação da declaração: " . $conexao->error);
    }

    // Executar a declaração com o ID da pessoa como parâmetro
    $stmt->bind_param("i", $idPessoa);
    $stmt->execute();

    // Obter o resultado da consulta
    $result = $stmt->get_result();

    // Verificar se a consulta retornou alguma linha
    if ($result->num_rows > 0) {
        // Obter o CPF do candidato
        $row = $result->fetch_assoc();
        $cpfCandidato = $row['CPF'];

        // Busca os cursos recomendados
        $cursos = buscarCursos($pesquisa);

        // Salva as recomendações
        salvarRecomendacoes($cpfCandidato, $cursos);

        // Retorna os cursos recomendados como JSON
        echo json_encode($cursos);
    } else {
        // Retorna uma mensagem de erro se os dados não foram recebidos corretamente
        echo json_encode(["error" => "Nenhum CPF encontrado para o ID da pessoa: " . $idPessoa]);
    }

    // Fecha a conexão
    $stmt->close();
    $conexao->close();
} else {
    // Retorna uma mensagem de erro se os dados não foram recebidos corretamente
    echo json_encode(["error" => "Dados não recebidos corretamente."]);
}
?>