<?php
include "../../services/conexão_com_banco.php";

// Verificar se os dados JSON foram recebidos
$json_data = file_get_contents('php://input');
$data = json_decode($json_data);

// Iniciar uma transação
$pdo->beginTransaction();

// Obtém o ID da pessoa associada ao email do usuário
$emailUsuario = $data->emailUsuario; 
$query = "SELECT Id_Pessoas FROM Tb_Pessoas WHERE Email = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$emailUsuario]);
$idPessoa = $stmt->fetchColumn();

// Verificar se a consulta foi bem-sucedida
if (!$idPessoa) {
    // Se não encontrar a pessoa, retorne uma resposta de erro    
    echo json_encode(array("message" => "Pessoa não encontrada."));
    exit;
}

// Obtém o CNPJ da empresa associada ao usuário
$query = "SELECT CNPJ FROM Tb_Empresa WHERE Tb_Pessoas_Id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$idPessoa]); // Correção: substituído $idUsuario por $idPessoa
$idEmpresa = $stmt->fetchColumn();

// Verificar se a consulta foi bem-sucedida
if (!$idEmpresa) {
    // Se não encontrar a empresa, retorne uma resposta de erro
    echo json_encode(array("message" => "Empresa não encontrada para este usuário."));
    exit;
}

try {
    // Inserir os detalhes básicos do questionário na tabela Tb_Questionarios
    $query = "INSERT INTO Tb_Questionarios (Nome, Area, DataQuestionario, Nivel, Descricao, Tempo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$data->titulo, $data->area, $data->data, $data->nivel, $data->competencias, $data->duracao]);
    $idQuestionario = $pdo->lastInsertId();

    // Verifica se a inserção do questionário foi bem-sucedida
    if (!$idQuestionario) {
        throw new Exception("Erro ao inserir questionário.");
    }

    // Associa o questionário à empresa na tabela Tb_Empresa_Questionario
    $query = "INSERT INTO Tb_Empresa_Questionario (Id_Empresa, Id_Questionario) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$idEmpresa, $idQuestionario]);

    // Verifica se a associação foi bem-sucedida
    if (!$stmt->rowCount()) {
        throw new Exception("Erro ao associar questionário à empresa.");
    }

    // Inserir as questões e alternativas
    foreach ($data->questionario->questoes as $questao) {
        // Inserir a questão na tabela Tb_Questoes
        $query = "INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$questao->pergunta, $data->area, $idQuestionario]);
        $idQuestao = $pdo->lastInsertId();

        // Verifica se a inserção da questão foi bem-sucedida
        if (!$idQuestao) {
            throw new Exception("Erro ao inserir questão.");
        }

        // Inserir as alternativas da questão na tabela Tb_Alternativas
        foreach ($questao->alternativas as $alternativa) {
            $query = "INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$alternativa->resposta, $alternativa->correta, $idQuestao]);

            // Verifica se a inserção da alternativa foi bem-sucedida
            if (!$stmt->rowCount()) {
                throw new Exception("Erro ao inserir alternativa.");
            }
        }
    }

    // Commit da transação
    $pdo->commit();

    // Retorna uma resposta de sucesso
    echo json_encode(array("message" => "Questionário criado com sucesso."));
} catch (Exception $e) {
    // Se ocorrer algum erro, rollback da transação e retorna uma resposta de erro
    $pdo->rollBack();
    echo json_encode(array("message" => $e->getMessage()));
}
