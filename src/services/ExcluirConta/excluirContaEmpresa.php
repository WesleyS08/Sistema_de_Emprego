<?php
// Inclui o arquivo de conexão com o banco de dados
include "../../../src/services/conexão_com_banco.php";

// Inicia a sessão
session_start();

try {
    // Verifica se o ID da pessoa foi fornecido na URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $idUsuario = intval($_GET['id']);  // Converte para número inteiro

        echo "ID do Usuário: $idUsuario<br>";

        // Inicia a transação
        $_con->begin_transaction();

        // Obter o CNPJ da empresa usando o Tb_Pessoas_Id
        $sql = "SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $stmt->bind_result($cnpj);
        $stmt->fetch();
        $stmt->close();

        echo "CNPJ: $cnpj<br>";

        // Verificar se o CNPJ foi encontrado
        if (!$cnpj) {
            throw new Exception("Nenhuma empresa encontrada para o ID do usuário fornecido.");
        }

        // 1. Excluir todas as inscrições associadas às vagas de emprego da empresa
        $sql_delete_inscricoes = "DELETE FROM Tb_Inscricoes WHERE Tb_Vagas_Tb_Empresa_CNPJ = ?";
        $stmt_delete_inscricoes = $_con->prepare($sql_delete_inscricoes);
        $stmt_delete_inscricoes->bind_param('s', $cnpj);
        $stmt_delete_inscricoes->execute();
        $stmt_delete_inscricoes->close();
        echo "Inscrições excluídas com sucesso<br>";

        // 2. Excluir as vagas de emprego há vai excluir os anuncios com fé em Deus 
        $sql_delete_vagas = "DELETE FROM Tb_Vagas WHERE Tb_Empresa_CNPJ = ?";
        $stmt_delete_vagas = $_con->prepare($sql_delete_vagas);
        $stmt_delete_vagas->bind_param('s', $cnpj);
        $stmt_delete_vagas->execute();
        $stmt_delete_vagas->close();
        echo "Vagas de emprego excluídas com sucesso<br>";


        // 3. Excluir alternativas associadas às questões
        $sql = "DELETE FROM Tb_Alternativas WHERE Tb_Questoes_Id_Questao IN (
                    SELECT Id_Questao
                    FROM Tb_Questoes
                    WHERE Id_Questionario IN (
                        SELECT Id_Questionario
                        FROM Tb_Questionario_Questoes
                        WHERE Id_Questionario IN (
                            SELECT Id_Questionario
                            FROM Tb_Empresa_Questionario
                            WHERE Id_Empresa = ?
                        )
                    )
                )";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 4. Excluir questões associadas aos questionários
        $sql = "DELETE FROM Tb_Questoes WHERE Id_Questionario IN (
                    SELECT Id_Questionario
                    FROM Tb_Empresa_Questionario
                    WHERE Id_Empresa = ?
                )";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 5. Excluir questionários associados à empresa
        $sql_select_images = "SELECT Q.ImagemQuestionario
                    FROM Tb_Questionarios Q
                    JOIN Tb_Empresa_Questionario EQ ON Q.Id_Questionario = EQ.Id_Questionario
                    WHERE EQ.Id_Empresa = ?";
        $stmt_select_images = $_con->prepare($sql_select_images);
        $stmt_select_images->bind_param('s', $cnpj);
        $stmt_select_images->execute();
        $stmt_select_images->bind_result($caminhoImagem);
        while ($stmt_select_images->fetch()) {
            if (file_exists($caminhoImagem)) {
                unlink($caminhoImagem);
                echo "Imagem $caminhoImagem excluída com sucesso<br>";
            } else {
                echo "Imagem $caminhoImagem não encontrada<br>";
            }
        }
        $stmt_select_images->close();

        $sql_delete_questionarios = "DELETE FROM Tb_Questionarios WHERE Id_Questionario IN (
                                SELECT Id_Questionario
                                FROM Tb_Empresa_Questionario
                                WHERE Id_Empresa = ?
                            )";
        $stmt_delete_questionarios = $_con->prepare($sql_delete_questionarios);
        $stmt_delete_questionarios->bind_param('s', $cnpj);
        $stmt_delete_questionarios->execute();
        $stmt_delete_questionarios->close();
        echo "Questionários excluídos com sucesso<br>";

        // 6. Excluir a relação entre empresa e questionário
        $sql = "DELETE FROM Tb_Empresa_Questionario WHERE Id_Empresa = ?";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 7. Excluir avaliações
        $sql = "DELETE FROM Tb_Avaliacoes WHERE Tb_Pessoas_Id IN (
                    SELECT Id_Pessoas
                    FROM Tb_Pessoas
                    WHERE Id_Pessoas IN (
                        SELECT Tb_Pessoas_Id
                        FROM Tb_Empresa
                        WHERE CNPJ = ?
                    )
                )";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 8. Excluir recomendações
        $sql = "DELETE FROM Tb_Recomendacoes WHERE Tb_Candidato_CPF IN (
                    SELECT DISTINCT Tb_Candidato_CPF
                    FROM Tb_Inscricoes
                    WHERE Tb_Vagas_Tb_Empresa_CNPJ = ?
                )";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 9. Excluir candidatos
        $sql_delete_candidatos = "DELETE FROM Tb_Candidato WHERE Tb_Pessoas_Id IN(
            SELECT Id_Pessoas
            FROM Tb_Pessoas
            WHERE Tb_Pessoas.Id_Pessoas IN (
                SELECT Tb_Pessoas_Id
                FROM Tb_Empresa
                WHERE CNPJ = ?
            )
        )";
        $stmt_delete_candidatos = $_con->prepare($sql_delete_candidatos);
        $stmt_delete_candidatos->bind_param('s', $cnpj);
        $stmt_delete_candidatos->execute();
        $stmt_delete_candidatos->close();

        // 10. Excluir todos os resultados da tabela Tb_Resultados
        $sql_delete_resultados = "DELETE FROM Tb_Resultados WHERE Tb_Candidato_CPF IN (
                    SELECT DISTINCT Tb_Candidato_CPF
                    FROM Tb_Inscricoes
                    WHERE Tb_Vagas_Tb_Empresa_CNPJ = ?
                )";
        $stmt_delete_resultados = $_con->prepare($sql_delete_resultados);
        $stmt_delete_resultados->bind_param('s', $cnpj);
        $stmt_delete_resultados->execute();
        $stmt_delete_resultados->close();

        // 11. Excluir empresa

        $sql_select_empresa_images = "SELECT Img_Perfil, Img_Banner FROM Tb_Empresa WHERE CNPJ = ?";
        $stmt_select_empresa_images = $_con->prepare($sql_select_empresa_images);
        $stmt_select_empresa_images->bind_param('s', $cnpj);
        $stmt_select_empresa_images->execute();
        $stmt_select_empresa_images->bind_result($imgPerfil, $imgBanner);
        $stmt_select_empresa_images->fetch();
        $stmt_select_empresa_images->close();

        if (file_exists($imgPerfil)) {
            unlink($imgPerfil);
            echo "Imagem de perfil $imgPerfil excluída com sucesso<br>";
        } else {
            echo "Imagem de perfil $imgPerfil não encontrada<br>";
        }

        if (file_exists($imgBanner)) {
            unlink($imgBanner);
            echo "Imagem de banner $imgBanner excluída com sucesso<br>";
        } else {
            echo "Imagem de banner $imgBanner não encontrada<br>";
        }
        $sql = "DELETE FROM Tb_Empresa WHERE CNPJ = ?";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('s', $cnpj);
        $stmt->execute();
        $stmt->close();

        // 12. Excluir pessoa
        $sql = "DELETE FROM Tb_Pessoas WHERE Id_Pessoas = ?";
        $stmt = $_con->prepare($sql);
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $stmt->close();

        // Commit a transação
        $_con->commit();
        session_destroy();
        header("Location: ../../../index.php");
        exit();
    } else {
        throw new Exception("ID de usuário inválido.");
    }
} catch (Exception $e) {
    // Rollback a transação em caso de erro
    $_con->rollback();
    echo "Falha ao apagar dados: " . $e->getMessage();
}

// Fechar a conexão
$_con->close();
?>