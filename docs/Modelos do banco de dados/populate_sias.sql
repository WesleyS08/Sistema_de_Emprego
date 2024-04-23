-- Inserir 25 registros na tabela `Tb_Pessoas` com a senha em SHA1
INSERT INTO `SIAS`.`Tb_Pessoas` (`Email`, `Senha`, `Nome`, `Token`, `Verificado`)
VALUES
('john.doe@example.com', SHA1('123'), 'John Doe', NULL, 1),
('jane.doe@example.com', SHA1('123'), 'Jane Doe', NULL, 1),
('alice.smith@example.com', SHA1('123'), 'Alice Smith', NULL, 1),
('bob.johnson@example.com', SHA1('123'), 'Bob Johnson', NULL, 1),
('chris.jones@example.com', SHA1('123'), 'Chris Jones', NULL, 1),
('karen.white@example.com', SHA1('123'), 'Karen White', NULL, 1),
('steve.brown@example.com', SHA1('123'), 'Steve Brown', NULL, 1),
('nancy.taylor@example.com', SHA1('123'), 'Nancy Taylor', NULL, 1),
('tom.wilson@example.com', SHA1('123'), 'Tom Wilson', NULL, 1),
('mike.davis@example.com', SHA1('123'), 'Mike Davis', NULL, 1),
('linda.clark@example.com', SHA1('123'), 'Linda Clark', NULL, 1),
('paul.roberts@example.com', SHA1('123'), 'Paul Roberts', NULL, 1),
('emma.harris@example.com', SHA1('123'), 'Emma Harris', NULL, 1),
('matthew.thompson@example.com', SHA1('123'), 'Matthew Thompson', NULL, 1),
('david.moore@example.com', SHA1('123'), 'David Moore', NULL, 1),
('lucy.lewis@example.com', SHA1('123'), 'Lucy Lewis', NULL, 1),
('susan.walker@example.com', SHA1('123'), 'Susan Walker', NULL, 1),
('george.king@example.com', SHA1('123'), 'George King', NULL, 1),
('sarah.allen@example.com', SHA1('123'), 'Sarah Allen', NULL, 1),
('bruce.wright@example.com', SHA1('123'), 'Bruce Wright', NULL, 1),
('patrick.hill@example.com', SHA1('123'), 'Patrick Hill', NULL, 1),
('kathy.martin@example.com', SHA1('123'), 'Kathy Martin', NULL, 1),
('rachel.hall@example.com', SHA1('123'), 'Rachel Hall', NULL, 1),
('victor.green@example.com', SHA1('123'), 'Victor Green', NULL, 1),
('laura.adams@example.com', SHA1('123'), 'Laura Adams', NULL, 1);

-- Criar uma tabela temporária para armazenar IDs
CREATE TEMPORARY TABLE `temp_ids` AS
SELECT `Id_Pessoas` FROM `SIAS`.`Tb_Pessoas`
ORDER BY RAND()
LIMIT 25;

-- Inserir 13 IDs aleatórios em `Tb_Candidato`
INSERT INTO `SIAS`.`Tb_Candidato` (`CPF`, `Tb_Pessoas_Id`, `Area_de_Interesse`, `Tipo_de_Contratacao`, `Descricao`, `Experiencia`, `Motivacoes`, `Cursos`, `Escolaridade`, `Genero`, `Estado_Civil`, `Idade`, `Autodefinicao`, `Telefone`, `Data_Nascimento`, `Cidade`, `PCD`, `Img_Perfil`, `Banner`)
SELECT RIGHT(MD5(CONCAT(`Id_Pessoas`, RAND())), 11), `Id_Pessoas`, 'Tecnologia', 'CLT', 'Descrição exemplo', 'Experiência exemplo', 'Motivação exemplo', 'Cursos exemplo', 'Graduação', 'Masculino', 'Solteiro', 30, 'Autodefinição exemplo', '5511987654321', '1990-01-01', 'São Paulo', false, NULL, NULL
FROM `temp_ids`
ORDER BY RAND()
LIMIT 13;

-- Inserir 12 IDs aleatórios restantes em `Tb_Empresa`
INSERT INTO `SIAS`.`Tb_Empresa` (`CNPJ`, `Tb_Pessoas_Id`, `Img_Banner`, `Facebook`, `Github`, `Linkedin`, `Instagram`, `Nome_da_Empresa`, `Sobre_a_Empresa`, `Area_da_Empresa`, `Avaliacao_de_Funcionarios`, `Avaliacao_Geral`, `Telefone`, `Img_Perfil`)
SELECT RIGHT(MD5(CONCAT(`Id_Pessoas`, RAND())), 14), `Id_Pessoas`, NULL, 'facebook.com/empresa', 'github.com/empresa', 'linkedin.com/empresa', 'instagram.com/empresa', 'Empresa Exemplo', 'Texto sobre a empresa', 'Área de exemplo', '4', '5', '5511987654321', NULL
FROM `temp_ids`
ORDER BY RAND()
LIMIT 12;

-- Limpar tabela temporária
DROP TEMPORARY TABLE `temp_ids`;

-- Inserir 50 anúncios na tabela `SIAS`.`Tb_Anuncios`
INSERT INTO `SIAS`.`Tb_Anuncios`
(`Categoria`, `Titulo`, `Descricao`, `Area`, `Cidade`, `Nivel_Operacional`, `Data_de_Criacao`, `Modalidade`, `Beneficios`, `Requisitos`, `Horario`, `Estado`, `Jornada`, `CEP`, `Rua`, `Numero`, `Bairro`)
VALUES
('CLT', 'Desenvolvedor Frontend', 'Desenvolvimento de aplicações web.', 'TI', 'São Paulo', 'Pleno', NOW(), 'Remoto', 'Vale alimentação, Plano de saúde', 'Experiência com React', '09:00 - 18:00', 'SP', 'Integral', '01000-000', 'Av. Paulista', '123', 'Bela Vista'),
('PJ', 'Analista de Dados', 'Análise de dados em grande escala.', 'TI', 'Rio de Janeiro', 'Sênior', NOW(), 'Híbrido', 'Vale alimentação, Plano de saúde', 'Experiência com SQL e Python', '09:00 - 18:00', 'RJ', 'Integral', '02000-000', 'Av. Rio Branco', '456', 'Centro'),
('Jovem Aprendiz', 'Assistente Administrativo', 'Atendimento a clientes.', 'Administração', 'São Paulo', 'Júnior', NOW(), 'Presencial', 'Vale transporte, Seguro de vida', 'Comunicação clara', '08:00 - 17:00', 'SP', 'Integral', '03000-000', 'Rua Augusta', '789', 'Consolação'),
('Estágio', 'Analista de Marketing', 'Elaboração de campanhas.', 'Marketing', 'Curitiba', 'Pleno', NOW(), 'Remoto', 'Vale alimentação, Plano de saúde', 'Experiência com redes sociais', '10:00 - 19:00', 'PR', 'Integral', '04000-000', 'Rua Oscar Freire', '123', 'Pinheiros'),
('CLT', 'Analista Financeiro', 'Análise de dados financeiros.', 'Finanças', 'Belo Horizonte', 'Pleno', NOW(), 'Presencial', 'Vale transporte, Seguro de vida', 'Experiência com Excel avançado', '09:00 - 18:00', 'MG', 'Integral', '05000-000', 'Av. Amazonas', '789', 'Savassi'),
('PJ', 'Especialista em RH', 'Recrutamento e seleção.', 'Recursos Humanos', 'Porto Alegre', 'Sênior', NOW(), 'Remoto', 'Plano de saúde, Seguro de vida', 'Experiência em RH', '09:00 - 18:00', 'RS', 'Integral', '06000-000', 'Rua dos Andradas', '321', 'Centro'),
('CLT', 'Enfermeiro', 'Cuidados de saúde.', 'Saúde', 'Salvador', 'Pleno', NOW(), 'Presencial', 'Plano de saúde, Vale transporte', 'Experiência em enfermagem', '08:00 - 17:00', 'BA', 'Integral', '07000-000', 'Av. Sete de Setembro', '654', 'Barra'),
('Estágio', 'Desenvolvedor Backend', 'Desenvolvimento de APIs e serviços.', 'TI', 'Recife', 'Sênior', NOW(), 'Remoto', 'Vale alimentação, Plano de saúde', 'Experiência com Node.js', '10:00 - 19:00', 'PE', 'Integral', '08000-000', 'Rua da Aurora', '789', 'Boa Vista'),
('CLT', 'Especialista em SEO', 'Otimização de mecanismos de busca.', 'Marketing', 'Florianópolis', 'Pleno', NOW(), 'Híbrido', 'Plano de saúde, Vale transporte', 'Experiência em SEO', '09:00 - 18:00', 'SC', 'Integral', '09000-000', 'Av. Beira-Mar', '456', 'Centro'),
('PJ', 'Contador', 'Gestão contábil.', 'Finanças', 'São Paulo', 'Sênior', NOW(), 'Remoto', 'Vale alimentação, Seguro de vida', 'Experiência com contabilidade', '09:00 - 18:00', 'SP', 'Integral', '01000-000', 'Rua Augusta', '321', 'Consolação'),
('CLT', 'Analista de RH', 'Processos de RH.', 'Recursos Humanos', 'São Paulo', 'Pleno', NOW(), 'Presencial', 'Plano de saúde, Vale transporte', 'Experiência com recrutamento', '08:00 - 17:00', 'SP', 'Integral', '02000-000', 'Av. Paulista', '654', 'Bela Vista'),
('Jovem Aprendiz', 'Desenvolvedor Mobile', 'Desenvolvimento de aplicativos móveis.', 'TI', 'São Paulo', 'Pleno', NOW(), 'Híbrido', 'Vale alimentação, Plano de saúde', 'Experiência com Flutter', '10:00 - 19:00', 'SP', 'Integral', '04000-000', 'Rua das Flores', '123', 'Pinheiros'),
('PJ', 'Gerente de Marketing', 'Gestão de campanhas de marketing.', 'Marketing', 'Rio de Janeiro', 'Sênior', NOW(), 'Remoto', 'Plano de saúde, Seguro de vida', 'Experiência em gestão de marketing', '09:00 - 18:00', 'RJ', 'Integral', '05000-000', 'Av. Atlântica', '456', 'Copacabana'),
('CLT', 'Assistente Administrativo', 'Atendimento a clientes e suporte.', 'Administração', 'Salvador', 'Júnior', NOW(), 'Presencial', 'Vale transporte, Seguro de vida', 'Habilidade em comunicação', '08:00 - 17:00', 'BA', 'Integral', '06000-000', 'Rua Carlos Gomes', '789', 'Centro'),
('Estágio', 'Coordenador de RH', 'Gestão de processos de RH.', 'Recursos Humanos', 'Curitiba', 'Sênior', NOW(), 'Remoto', 'Plano de saúde, Vale alimentação', 'Experiência em RH', '09:00 - 18:00', 'PR', 'Integral', '07000-000', 'Av. Batel', '123', 'Batel'),
('CLT', 'Analista Financeiro', 'Análise e gestão financeira.', 'Finanças', 'Porto Alegre', 'Pleno', NOW(), 'Presencial', 'Vale alimentação, Seguro de vida', 'Experiência com análise financeira', '08:00 - 17:00', 'RS', 'Integral', '08000-000', 'Rua dos Andradas', '456', 'Centro'),
('PJ', 'Administrador de Redes', 'Gerenciamento de redes e infraestrutura.', 'TI', 'Belo Horizonte', 'Sênior', NOW(), 'Híbrido', 'Plano de saúde, Vale transporte', 'Experiência em administração de redes', '09:00 - 18:00', 'MG', 'Integral', '09000-000', 'Av. Afonso Pena', '789', 'Centro'),
('Estágio', 'Analista de Conteúdo', 'Criação de conteúdo para campanhas.', 'Marketing', 'São Paulo', 'Pleno', NOW(), 'Remoto', 'Plano de saúde, Seguro de vida', 'Habilidade em escrita criativa', '09:00 - 18:00', 'SP', 'Integral', '10000-000', 'Av. Paulista', '321', 'Bela Vista'),
('CLT', 'Desenvolvedor Full Stack', 'Desenvolvimento de software.', 'TI', 'Fortaleza', 'Pleno', NOW(), 'Híbrido', 'Vale alimentação, Plano de saúde', 'Experiência com Node.js e React', '10:00 - 19:00', 'CE', 'Integral', '11000-000', 'Av. Beira-Mar', '123', 'Meireles'),
('PJ', 'Médico Clínico Geral', 'Atendimento clínico geral.', 'Saúde', 'Recife', 'Pleno', NOW(), 'Presencial', 'Plano de saúde, Vale transporte', 'Formação em Medicina', '09:00 - 18:00', 'PE', 'Integral', '12000-000', 'Rua da Aurora', '456', 'Boa Vista'),
('CLT', 'Suporte Técnico', 'Suporte a sistemas e usuários.', 'TI', 'Porto Alegre', 'Júnior', NOW(), 'Híbrido', 'Vale transporte, Seguro de vida', 'Experiência com suporte técnico', '08:00 - 17:00', 'RS', 'Integral', '13000-000', 'Rua dos Andradas', '789', 'Centro'),
('Estágio', 'Especialista em Treinamento', 'Desenvolvimento e implementação de treinamentos.', 'Recursos Humanos', 'Curitiba', 'Pleno', NOW(), 'Presencial', 'Plano de saúde, Vale alimentação', 'Experiência em treinamento', '09:00 - 18:00', 'PR', 'Integral', '14000-000', 'Av. Batel', '321', 'Batel'),
('PJ', 'Gerente Financeiro', 'Gestão financeira e orçamentária.', 'Finanças', 'São Paulo', 'Sênior', NOW(), 'Remoto', 'Plano de saúde, Vale transporte', 'Experiência em gestão financeira', '09:00 - 18:00', 'SP', 'Integral', '15000-000', 'Av. Paulista', '654', 'Bela Vista'),
('CLT', 'Desenvolvedor de Jogos', 'Desenvolvimento de jogos digitais.', 'TI', 'Florianópolis', 'Sênior', NOW(), 'Remoto', 'Vale alimentação, Seguro de vida', 'Experiência com desenvolvimento de jogos', '10:00 - 19:00', 'SC', 'Integral', '16000-000', 'Av. Beira-Mar', '123', 'Centro'),
('Jovem Aprendiz', 'Gerente de Comunicação', 'Gestão de comunicação interna e externa.', 'Marketing', 'Fortaleza', 'Sênior', NOW(), 'Híbrido', 'Plano de saúde, Seguro de vida', 'Experiência em comunicação', '09:00 - 18:00', 'CE', 'Integral', '17000-000', 'Av. Beira-Mar', '456', 'Meireles'),
('CLT', 'Engenheiro de Dados', 'Construção e otimização de pipelines de dados.', 'TI', 'São Paulo', 'Pleno', NOW(), 'Remoto', 'Vale alimentação, Plano de saúde', 'Experiência com ETL', '10:00 - 19:00', 'SP', 'Integral', '18000-000', 'Rua Augusta', '789', 'Consolação'),
('PJ', 'Analista Administrativo', 'Gestão de processos administrativos.', 'Administração', 'Rio de Janeiro', 'Pleno', NOW(), 'Presencial', 'Vale transporte, Seguro de vida', 'Experiência em processos administrativos', '08:00 - 17:00', 'RJ', 'Integral', '19000-000', 'Av. Atlântica', '321', 'Copacabana'),
('CLT', 'Enfermeiro Cirúrgico', 'Apoio em procedimentos cirúrgicos.', 'Saúde', 'Belo Horizonte', 'Sênior', NOW(), 'Presencial', 'Plano de saúde, Vale transporte', 'Formação em enfermagem cirúrgica', '09:00 - 18:00', 'MG', 'Integral', '20000-000', 'Av. Amazonas', '654', 'Savassi'),
('Jovem Aprendiz', 'Contador', 'Gestão de contabilidade e impostos.', 'Finanças', 'Curitiba', 'Sênior', NOW(), 'Híbrido', 'Plano de saúde, Seguro de vida', 'Experiência em contabilidade', '09:00 - 18:00', 'PR', 'Integral', '21000-000', 'Av. Batel', '123', 'Batel'),
('Estágio', 'Especialista em Redes Sociais', 'Gestão de redes sociais.', 'Marketing', 'São Paulo', 'Pleno', NOW(), 'Remoto', 'Vale transporte, Seguro de vida', 'Experiência com redes sociais', '10:00 - 19:00', 'SP', 'Integral', '22000-000', 'Av. Paulista', '456', 'Bela Vista'),
('PJ', 'Administrador de Sistemas', 'Gerenciamento de sistemas e servidores.', 'TI', 'Recife', 'Sênior', NOW(), 'Remoto', 'Plano de saúde, Vale alimentação', 'Experiência com administração de sistemas', '09:00 - 18:00', 'PE', 'Integral', '23000-000', 'Rua da Aurora', '123', 'Boa Vista');


-- Inserir 10 registros na tabela `Tb_Vagas` usando CNPJs aleatórios da tabela `Tb_Empresa`
INSERT INTO `SIAS`.`Tb_Vagas`
(`Tb_Anuncios_Id`, `Tb_Empresa_CNPJ`, `Status`, `Data_de_Termino`)
SELECT 
  `Anuncios`.`Id_Anuncios`, -- Vínculo com o anúncio
  `Empresas`.`CNPJ`,        -- Vínculo com uma empresa aleatória
  'Aberta',                 -- Status padrão
  NOW() + INTERVAL FLOOR(RAND() * 365) DAY -- Data de término aleatória entre 0 e 365 dias
FROM 
  `SIAS`.`Tb_Anuncios` AS `Anuncios`, 
  `SIAS`.`Tb_Empresa` AS `Empresas`
ORDER BY RAND()
LIMIT 31;

-- Inserir inscrições aleatórias na tabela `Tb_Inscricoes`
INSERT INTO `SIAS`.`Tb_Inscricoes`
(`Tb_Vagas_Tb_Anuncios_Id`, `Tb_Vagas_Tb_Empresa_CNPJ`, `Tb_Candidato_CPF`, `Data_de_Inscricao`)
SELECT
  `Vagas`.`Tb_Anuncios_Id`,
  `Vagas`.`Tb_Empresa_CNPJ`,
  `Candidato`.`CPF`,
  NOW() -- Data da inscrição
FROM
  (SELECT DISTINCT `Tb_Anuncios_Id`, `Tb_Empresa_CNPJ` FROM `SIAS`.`Tb_Vagas` ORDER BY RAND() LIMIT 31) AS `Vagas`,
  (SELECT `CPF` FROM `SIAS`.`Tb_Candidato` ORDER BY RAND() LIMIT 13) AS `Candidato`
ORDER BY RAND() -- Ordenação aleatória para variar as inscrições
LIMIT 31; -- Quantidade de inscrições a inserir


