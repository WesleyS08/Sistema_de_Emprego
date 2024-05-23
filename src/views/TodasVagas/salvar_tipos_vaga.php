<?php
// Inicializa a resposta como vazia
$response = "";

// Verifica se foi recebido o ID da pessoa e os tipos de vaga selecionados
if (isset($_POST['id_pessoa']) && isset($_POST['tipos_vaga'])) {
    // Conecta ao banco de dados
    include "../../services/conexão_com_banco.php";

    // Captura o ID da pessoa e os tipos de vaga selecionados
    $idPessoa = $_POST['id_pessoa'];
    $tiposVagaSelecionados = $_POST['tipos_vaga'];

    // Consulta para obter o CPF com base no ID da pessoa
    $sqlCpf = "SELECT CPF FROM Tb_Candidato WHERE Tb_Pessoas_Id = ?";
    $stmtCpf = $_con->prepare($sqlCpf);
    $stmtCpf->bind_param("i", $idPessoa);
    $stmtCpf->execute();
    $resultCpf = $stmtCpf->get_result();

    // Verifica se a consulta retornou resultados
    if ($resultCpf->num_rows > 0) {
        // Obtém o CPF associado ao ID da pessoa
        $rowCpf = $resultCpf->fetch_assoc();
        $cpf = $rowCpf['CPF'];

        // Define os critérios de seleção de cursos com base nos tipos de vaga selecionados
        $nivelCurso = "";
        $categoriaCurso = "";

        foreach ($tiposVagaSelecionados as $tipoVaga) {
            switch ($tipoVaga) {
                case "Jovem Aprendiz":
                    $nivelCurso = "Técnico";
                    $categoriaCurso = "Bradesco";
                    break;
                case "Estágio":
                case "CLT":
                case "PJ":
                    $nivelCurso = "Superior";
                    $categoriaCurso = "Bradesco";
                    break;
                default:
                    // Caso não seja nenhum dos tipos de vaga esperados
                    break;
            }

            // Consulta para selecionar cursos com base nos critérios definidos
            $sqlCursos = "SELECT * FROM Tb_Cursos WHERE Nivel = ? OR Categoria = ?";
            $stmtCursos = $_con->prepare($sqlCursos);
            $stmtCursos->bind_param("ss", $nivelCurso, $categoriaCurso);
            $stmtCursos->execute();
            $resultCursos = $stmtCursos->get_result();

            // Verifica se há cursos recomendados
            if ($resultCursos->num_rows > 0) {
                // Loop através dos cursos recomendados
                while ($rowCurso = $resultCursos->fetch_assoc()) {
                    $idCurso = $rowCurso['Id_Cursos'];

                    // Verifica se já existe uma recomendação para este candidato, tipo de vaga e curso
                    $sqlCheckExisting = "SELECT * FROM Tb_Recomendacoes WHERE Tb_Candidato_CPF = ? AND Tipo_Vaga = ? AND Tb_Cursos_Id = ?";
                    $stmtCheckExisting = $_con->prepare($sqlCheckExisting);
                    $stmtCheckExisting->bind_param("ssi", $cpf, $tipoVaga, $idCurso);
                    $stmtCheckExisting->execute();
                    $resultCheckExisting = $stmtCheckExisting->get_result();

                    // Se já existir, atualiza o registro existente, caso contrário, insere um novo registro
                    if ($resultCheckExisting->num_rows > 0) {
                        $sqlUpdate = "UPDATE Tb_Recomendacoes SET Tb_Candidato_CPF = ?, Tipo_Vaga = ?, Tb_Cursos_Id = ? WHERE Tb_Candidato_CPF = ? AND Tipo_Vaga = ? AND Tb_Cursos_Id = ?";
                        $stmtUpdate = $_con->prepare($sqlUpdate);
                        $stmtUpdate->bind_param("ssisssi", $cpf, $tipoVaga, $idCurso, $cpf, $tipoVaga, $idCurso);
                        $stmtUpdate->execute();
                    } else {
                        // Salva o resultado da recomendação na tabela Tb_Recomendacoes
                        $sqlInsert = "INSERT INTO Tb_Recomendacoes (Tb_Candidato_CPF, Tipo_Vaga, Tb_Cursos_Id) VALUES (?, ?, ?)";
                        $stmtInsert = $_con->prepare($sqlInsert);
                        $stmtInsert->bind_param("ssi", $cpf, $tipoVaga, $idCurso);
                        $stmtInsert->execute();
                    }
                }
            }
        }
        // Define a resposta como sucesso
        $response = "Dados salvos com sucesso.";
    } else {
        // Define a resposta como erro
        $response = "Erro: ID da pessoa não encontrado.";
    }

    // Fecha as conexões com o banco de dados
    $stmtCpf->close();
    $_con->close();
} else {
    // Caso não tenha recebido todas as informações necessárias
    $response = "Erro: Informações incompletas.";
}

// Retorna a resposta para o cliente
echo $response;
?>
