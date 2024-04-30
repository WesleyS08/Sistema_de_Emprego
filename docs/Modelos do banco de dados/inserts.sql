-- Inserir um novo questionário na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Ciências', 'Ciências', '2024-04-20', 'Fácil', 'Questionário sobre Ciências');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario = LAST_INSERT_ID();

-- Inserir cinco novas questões na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES 
('Qual é o planeta mais próximo do Sol?', 'Astronomia', @Id_Questionario),
('Qual é o maior osso do corpo humano?', 'Anatomia', @Id_Questionario),
('Qual é o símbolo químico do ouro?', 'Química', @Id_Questionario),
('Qual é a capital da França?', 'Geografia', @Id_Questionario),
('Quem escreveu a obra "Dom Quixote"?', 'Literatura', @Id_Questionario);

-- Recuperar o ID da primeira questão inserida
SET @Id_Questao1 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES 
('Mercúrio', 1, @Id_Questao1), ('Vênus', 0, @Id_Questao1), ('Marte', 0, @Id_Questao1), ('Júpiter', 0, @Id_Questao1), ('Saturno', 0, @Id_Questao1),
('Fêmur', 1, @Id_Questao1 + 1), ('Tíbia', 0, @Id_Questao1 + 1), ('Fíbula', 0, @Id_Questao1 + 1), ('Úmero', 0, @Id_Questao1 + 1), ('Rádio', 0, @Id_Questao1 + 1),
('Au', 1, @Id_Questao1 + 2), ('Ag', 0, @Id_Questao1 + 2), ('Pt', 0, @Id_Questao1 + 2), ('Cu', 0, @Id_Questao1 + 2), ('Fe', 0, @Id_Questao1 + 2),
('Paris', 0, @Id_Questao1 + 3), ('Londres', 0, @Id_Questao1 + 3), ('Berlim', 0, @Id_Questao1 + 3), ('Madri', 0, @Id_Questao1 + 3), ('Roma', 1, @Id_Questao1 + 3),
('Miguel de Cervantes', 1, @Id_Questao1 + 4), ('William Shakespeare', 0, @Id_Questao1 + 4), ('Jorge Luis Borges', 0, @Id_Questao1 + 4), ('Machado de Assis', 0, @Id_Questao1 + 4), ('Gabriel García Márquez', 0, @Id_Questao1 + 4);

--
-- Segundo questionário 
--

-- Inserir um novo questionário na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('História', 'História', '2024-04-20', 'Médio', 'Questionário sobre o Japão Feudal');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario = LAST_INSERT_ID();

-- Inserir cinco novas questões na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES 
('Quem foi o primeiro xogum do Japão Feudal?', 'História', @Id_Questionario),
('Qual era o sistema de governo predominante no Japão Feudal?', 'História', @Id_Questionario),
('O que eram os samurais no contexto do Japão Feudal?', 'História', @Id_Questionario),
('Quem eram os daimyos no Japão Feudal?', 'História', @Id_Questionario),
('O que foi o código de conduta seguido pelos samurais?', 'História', @Id_Questionario);

-- Recuperar o ID da primeira questão inserida
SET @Id_Questao11 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES 
('Minamoto no Yoritomo', 1, @Id_Questao11), ('Tokugawa Ieyasu', 0, @Id_Questao11), ('Oda Nobunaga', 0, @Id_Questao11), ('Toyotomi Hideyoshi', 0, @Id_Questao11), ('Taira no Kiyomori', 0, @Id_Questao11),
('Monarquia absolutista', 1, @Id_Questao11 + 1), ('Feudalismo', 0, @Id_Questao11 + 1), ('República', 0, @Id_Questao11 + 1), ('Democracia', 0, @Id_Questao11 + 1), ('Teocracia', 0, @Id_Questao11 + 1),
('Guerreiros samurais', 1, @Id_Questao11 + 2), ('Comerciantes', 0, @Id_Questao11 + 2), ('Camponeses', 0, @Id_Questao11 + 2), ('Artesãos', 0, @Id_Questao11 + 2), ('Nobres', 0, @Id_Questao11 + 2),
('Senhores feudais', 1, @Id_Questao11 + 3), ('Guerrilheiros', 0, @Id_Questao11 + 3), ('Camponeses', 0, @Id_Questao11 + 3), ('Comerciantes', 0, @Id_Questao11 + 3), ('Artistas', 0, @Id_Questao11 + 3),
('Bushido', 1, @Id_Questao11 + 4), ('Hagakure', 0, @Id_Questao11 + 4), ('Ninjutsu', 0, @Id_Questao11 + 4), ('Kamikaze', 0, @Id_Questao11 + 4), ('Geishas', 0, @Id_Questao11 + 4);

--
-- Terceiro questionário
--

-- Inserir um novo questionário sobre Fórmula 1 na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Fórmula 1', 'Esporte', '2024-04-21', 'Médio', 'Questionário sobre Fórmula 1');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario2 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre Fórmula 1 na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES 
('Quem é o piloto com mais títulos na história da Fórmula 1?', 'Esporte', @Id_Questionario2),
('Qual é a equipe com mais títulos de construtores na Fórmula 1?', 'Esporte', @Id_Questionario2),
('Qual é o circuito mais longo do calendário da Fórmula 1?', 'Esporte', @Id_Questionario2),
('Quantos títulos mundiais de pilotos Michael Schumacher conquistou?', 'Esporte', @Id_Questionario2),
('Quem foi o último piloto brasileiro a vencer o campeonato mundial de Fórmula 1?', 'Esporte', @Id_Questionario2);

-- Recuperar o ID da primeira questão inserida sobre Fórmula 1
SET @Id_Questao6 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre Fórmula 1 na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES 
('Michael Schumacher', 1, @Id_Questao6), ('Ayrton Senna', 0, @Id_Questao6), ('Lewis Hamilton', 0, @Id_Questao6), ('Sebastian Vettel', 0, @Id_Questao6), ('Juan Manuel Fangio', 0, @Id_Questao6),
('Ferrari', 1, @Id_Questao6 + 1), ('Mercedes', 0, @Id_Questao6 + 1), ('McLaren', 0, @Id_Questao6 + 1), ('Red Bull Racing', 0, @Id_Questao6 + 1), ('Williams', 0, @Id_Questao6 + 1),
('Circuito de Spa-Francorchamps', 1, @Id_Questao6 + 2), ('Circuito de Mônaco', 0, @Id_Questao6 + 2), ('Circuito de Suzuka', 0, @Id_Questao6 + 2), ('Circuito de Interlagos', 0, @Id_Questao6 + 2), ('Circuito de Silverstone', 0, @Id_Questao6 + 2),
('Sete títulos', 1, @Id_Questao6 + 3), ('Cinco títulos', 0, @Id_Questao6 + 3), ('Três títulos', 0, @Id_Questao6 + 3), ('Quatro títulos', 0, @Id_Questao6 + 3), ('Seis títulos', 0, @Id_Questao6 + 3),
('Emerson Fittipaldi', 0, @Id_Questao6 + 4), ('Ayrton Senna', 1, @Id_Questao6 + 4), ('Rubens Barrichello', 0, @Id_Questao6 + 4), ('Nelson Piquet', 0, @Id_Questao6 + 4), ('Felipe Massa', 0, @Id_Questao6 + 4);

--
-- Quarto Questionario
--

-- Inserir um novo questionário sobre Scania na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Scania', 'Indústria Automotiva', '2024-04-22', 'Fácil', 'Questionário sobre a marca Scania');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario3 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre Scania na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES 
('Em qual país a Scania tem sede?', 'Indústria Automotiva', @Id_Questionario3),
('Qual é o principal produto da Scania?', 'Indústria Automotiva', @Id_Questionario3),
('Qual é a origem da marca Scania?', 'Indústria Automotiva', @Id_Questionario3),
('Quem é o fundador da Scania?', 'Indústria Automotiva', @Id_Questionario3),
('Em que ano a Scania foi fundada?', 'Indústria Automotiva', @Id_Questionario3);

-- Recuperar o ID da primeira questão inserida sobre Scania
SET @Id_Questao11 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre Scania na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES 
('Suécia', 1, @Id_Questao11), ('Alemanha', 0, @Id_Questao11), ('Itália', 0, @Id_Questao11), ('Estados Unidos', 0, @Id_Questao11), ('França', 0, @Id_Questao11),
('Caminhões', 1, @Id_Questao11 + 1), ('Carros de passeio', 0, @Id_Questao11 + 1), ('Motos', 0, @Id_Questao11 + 1), ('Ônibus', 0, @Id_Questao11 + 1), ('Aviões', 0, @Id_Questao11 + 1),
('Fundição', 1, @Id_Questao11 + 2), ('Indústria de papel', 0, @Id_Questao11 + 2), ('Construção civil', 0, @Id_Questao11 + 2), ('Alimentícia', 0, @Id_Questao11 + 2), ('Eletrônica', 0, @Id_Questao11 + 2),
('Gustaf Erikson', 1, @Id_Questao11 + 3), ('Erik Scania', 0, @Id_Questao11 + 3), ('Anders Wall', 0, @Id_Questao11 + 3), ('Lars Magnus Ericsson', 0, @Id_Questao11 + 3), ('Sven Wingquist', 0, @Id_Questao11 + 3),
('1891', 0, @Id_Questao11 + 4), ('1901', 0, @Id_Questao11 + 4), ('1911', 0, @Id_Questao11 + 4), ('1921', 1, @Id_Questao11 + 4), ('1931', 0, @Id_Questao11 + 4);

-- Quinto Questionário

-- Inserir um novo questionário sobre a história do Corinthians na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('História do Corinthians', 'Esportes', '2024-04-25', 'Médio', 'Questionário sobre a história do Sport Club Corinthians Paulista');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario4 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre a história do Corinthians na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Em que ano foi fundado o Sport Club Corinthians Paulista?', 'Esportes', @Id_Questionario4),
('Qual é o apelido dado ao Corinthians?', 'Esportes', @Id_Questionario4),
('Quantos títulos da Copa Libertadores da América o Corinthians possui?', 'Esportes', @Id_Questionario4),
('Quem foi o primeiro presidente do Corinthians?', 'Esportes', @Id_Questionario4),
('Qual é o estádio oficial do Corinthians?', 'Esportes', @Id_Questionario4);

-- Recuperar o ID da primeira questão inserida sobre a história do Corinthians
SET @Id_Questao16 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre a história do Corinthians na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('1910', 1, @Id_Questao16), ('1908', 0, @Id_Questao16), ('1920', 0, @Id_Questao16), ('1912', 0, @Id_Questao16), ('1906', 0, @Id_Questao16),
('Timão', 1, @Id_Questao16 + 1), ('Alvinegro', 0, @Id_Questao16 + 1), ('Gavião', 0, @Id_Questao16 + 1), ('Fiel', 0, @Id_Questao16 + 1), ('Poderoso', 0, @Id_Questao16 + 1),
('1', 0, @Id_Questao16 + 2), ('2', 0, @Id_Questao16 + 2), ('3', 0, @Id_Questao16 + 2), ('4', 1, @Id_Questao16 + 2), ('5', 0, @Id_Questao16 + 2),
('João da Silva', 0, @Id_Questao16 + 3), ('Antônio Pereira', 0, @Id_Questao16 + 3), ('Manoel Nunes', 1, @Id_Questao16 + 3), ('Carlos José', 0, @Id_Questao16 + 3), ('Pedro Gomes', 0, @Id_Questao16 + 3),
('Arena Corinthians', 1, @Id_Questao16 + 4), ('Estádio do Pacaembu', 0, @Id_Questao16 + 4), ('Estádio do Morumbi', 0, @Id_Questao16 + 4), ('Estádio São Januário', 0, @Id_Questao16 + 4), ('Estádio Beira-Rio', 0, @Id_Questao16 + 4);

-- Sexto Questionário

-- Inserir um novo questionário sobre a história da corrida espacial na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('História da Corrida Espacial', 'Ciência e Tecnologia', '2024-04-25', 'Difícil', 'Questionário sobre os eventos e marcos da corrida espacial');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario5 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre a história da corrida espacial na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Qual foi o primeiro satélite artificial lançado pelo ser humano?', 'Ciência e Tecnologia', @Id_Questionario5),
('Quem foi o primeiro ser humano a viajar para o espaço?', 'Ciência e Tecnologia', @Id_Questionario5),
('Qual foi o programa espacial que levou o primeiro homem à Lua?', 'Ciência e Tecnologia', @Id_Questionario5),
('Quais foram as duas nações que lideraram a corrida espacial durante a Guerra Fria?', 'Ciência e Tecnologia', @Id_Questionario5),
('Em que ano o homem pisou na Lua pela primeira vez?', 'Ciência e Tecnologia', @Id_Questionario5);

-- Recuperar o ID da primeira questão inserida sobre a história da corrida espacial
SET @Id_Questao21 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre a história da corrida espacial na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Sputnik 1', 1, @Id_Questao21), ('Explorer 1', 0, @Id_Questao21), ('Vostok 1', 0, @Id_Questao21), ('Apollo 11', 0, @Id_Questao21), ('Voyager 1', 0, @Id_Questao21),
('Yuri Gagarin', 1, @Id_Questao21 + 1), ('Alan Shepard', 0, @Id_Questao21 + 1), ('John Glenn', 0, @Id_Questao21 + 1), ('Neil Armstrong', 0, @Id_Questao21 + 1), ('Buzz Aldrin', 0, @Id_Questao21 + 1),
('Programa Apollo', 1, @Id_Questao21 + 2), ('Programa Mercury', 0, @Id_Questao21 + 2), ('Programa Gemini', 0, @Id_Questao21 + 2), ('Programa Skylab', 0, @Id_Questao21 + 2), ('Programa Sputnik', 0, @Id_Questao21 + 2),
('Estados Unidos e União Soviética', 1, @Id_Questao21 + 3), ('Estados Unidos e China', 0, @Id_Questao21 + 3), ('Estados Unidos e Reino Unido', 0, @Id_Questao21 + 3), ('Estados Unidos e Alemanha', 0, @Id_Questao21 + 3), ('China e União Soviética', 0, @Id_Questao21 + 3),
('1969', 1, @Id_Questao21 + 4), ('1961', 0, @Id_Questao21 + 4), ('1971', 0, @Id_Questao21 + 4), ('1957', 0, @Id_Questao21 + 4), ('1972', 0, @Id_Questao21 + 4);

-- Sétimo Questionário

-- Inserir um novo questionário sobre a história do Rio Grande do Sul na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('História do Rio Grande do Sul', 'História', '2024-04-25', 'Médio', 'Questionário sobre os eventos históricos do estado do Rio Grande do Sul');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario6 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre a história do Rio Grande do Sul na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Quem liderou a Revolução Farroupilha?', 'História', @Id_Questionario6),
('Em que ano começou a Revolução Farroupilha?', 'História', @Id_Questionario6),
('Qual foi a duração aproximada da Revolução Farroupilha?', 'História', @Id_Questionario6),
('Quando o Rio Grande do Sul se tornou uma província brasileira?', 'História', @Id_Questionario6),
('Qual é a capital do Rio Grande do Sul?', 'Geografia', @Id_Questionario6);

-- Recuperar o ID da primeira questão inserida sobre a história do Rio Grande do Sul
SET @Id_Questao26 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre a história do Rio Grande do Sul na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Bento Gonçalves', 1, @Id_Questao26), ('Giuseppe Garibaldi', 0, @Id_Questao26), ('David Canabarro', 0, @Id_Questao26), ('Anita Garibaldi', 0, @Id_Questao26), ('Joaquim Teixeira Nunes', 0, @Id_Questao26),
('1835', 1, @Id_Questao26 + 1), ('1845', 0, @Id_Questao26 + 1), ('1825', 0, @Id_Questao26 + 1), ('1855', 0, @Id_Questao26 + 1), ('1815', 0, @Id_Questao26 + 1),
('10 anos', 0, @Id_Questao26 + 2), ('2 anos', 0, @Id_Questao26 + 2), ('20 anos', 1, @Id_Questao26 + 2), ('5 anos', 0, @Id_Questao26 + 2), ('15 anos', 0, @Id_Questao26 + 2),
('1822', 0, @Id_Questao26 + 3), ('1808', 0, @Id_Questao26 + 3), ('1763', 0, @Id_Questao26 + 3), ('1830', 1, @Id_Questao26 + 3), ('1889', 0, @Id_Questao26 + 3),
('Porto Alegre', 1, @Id_Questao26 + 4), ('Caxias do Sul', 0, @Id_Questao26 + 4), ('Pelotas', 0, @Id_Questao26 + 4), ('Santa Maria', 0, @Id_Questao26 + 4), ('Rio Grande', 0, @Id_Questao26 + 4);


-- Oitavo Questionário

-- Inserir um novo questionário sobre a história do Barcelona na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('História do Barcelona', 'Esportes', '2024-04-25', 'Médio', 'Questionário sobre os eventos históricos do clube de futebol Barcelona');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario7 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre a história do Barcelona na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Em que ano foi fundado o Barcelona?', 'Esportes', @Id_Questionario7),
('Quem é considerado o fundador do Barcelona?', 'Esportes', @Id_Questionario7),
('Qual é o estádio oficial do Barcelona?', 'Esportes', @Id_Questionario7),
('Quantas Ligas dos Campeões da UEFA o Barcelona conquistou até agora?', 'Esportes', @Id_Questionario7),
('Quem é o jogador que mais vezes vestiu a camisa do Barcelona?', 'Esportes', @Id_Questionario7);

-- Recuperar o ID da primeira questão inserida sobre a história do Barcelona
SET @Id_Questao31 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre a história do Barcelona na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('1899', 1, @Id_Questao31), ('1901', 0, @Id_Questao31), ('1903', 0, @Id_Questao31), ('1905', 0, @Id_Questao31), ('1908', 0, @Id_Questao31),
('Joan Gamper', 1, @Id_Questao31 + 1), ('Pep Guardiola', 0, @Id_Questao31 + 1), ('Lionel Messi', 0, @Id_Questao31 + 1), ('Johan Cruyff', 0, @Id_Questao31 + 1), ('Xavi Hernández', 0, @Id_Questao31 + 1),
('Camp Nou', 1, @Id_Questao31 + 2), ('Santiago Bernabéu', 0, @Id_Questao31 + 2), ('Estadi Olímpic Lluís Companys', 0, @Id_Questao31 + 2), ('Estadi Montilivi', 0, @Id_Questao31 + 2), ('Estadio San Mamés', 0, @Id_Questao31 + 2),
('5', 0, @Id_Questao31 + 3), ('3', 0, @Id_Questao31 + 3), ('4', 1, @Id_Questao31 + 3), ('2', 0, @Id_Questao31 + 3), ('6', 0, @Id_Questao31 + 3),
('Xavi Hernández', 0, @Id_Questao31 + 4), ('Andrés Iniesta', 0, @Id_Questao31 + 4), ('Lionel Messi', 1, @Id_Questao31 + 4), ('Carles Puyol', 0, @Id_Questao31 + 4), ('Sergio Busquets', 0, @Id_Questao31 + 4);

-- Nono Questionário

-- Inserir um novo questionário sobre como amansar um burro pampa na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Como amansar um burro pampa', 'Agricultura', '2024-04-25', 'Difícil', 'Questionário sobre técnicas de doma de burros pampa');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario8 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre como amansar um burro pampa na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Qual é o primeiro passo ao começar a domar um burro pampa?', 'Agricultura', @Id_Questionario8),
('Por que é importante construir confiança com o burro durante o processo de doma?', 'Agricultura', @Id_Questionario8),
('Quais são algumas técnicas comuns para acostumar o burro com a presença humana?', 'Agricultura', @Id_Questionario8),
('Como você pode ensinar um burro a aceitar uma sela?', 'Agricultura', @Id_Questionario8),
('Por que é fundamental ser paciente durante o processo de amansar um burro pampa?', 'Agricultura', @Id_Questionario8);

-- Recuperar o ID da primeira questão inserida sobre como amansar um burro pampa
SET @Id_Questao36 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre como amansar um burro pampa na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Estabelecer uma área de trabalho segura', 1, @Id_Questao36), ('Montar no burro imediatamente', 0, @Id_Questao36), ('Ignorar o burro até que ele se acalme', 0, @Id_Questao36), ('Utilizar chicotes para mostrar domínio', 0, @Id_Questao36), ('Alimentar o burro com guloseimas para ganhar sua confiança', 0, @Id_Questao36),
('Para garantir uma relação segura e produtiva', 1, @Id_Questao36 + 1), ('Não é importante construir confiança com o burro', 0, @Id_Questao36 + 1), ('Para provar sua superioridade sobre o animal', 0, @Id_Questao36 + 1), ('Para desafiar o instinto natural do burro', 0, @Id_Questao36 + 1), ('Para evitar o desperdício de tempo', 0, @Id_Questao36 + 1),
('Passar tempo próximo ao burro sem ameaçá-lo', 1, @Id_Questao36 + 2), ('Evitar a presença humana durante a doma', 0, @Id_Questao36 + 2), ('Utilizar apenas métodos de força para controlar o burro', 0, @Id_Questao36 + 2), ('Exigir obediência imediata', 0, @Id_Questao36 + 2), ('Deixar o burro sozinho até que ele se acalme', 0, @Id_Questao36 + 2),
('Gradualmente introduzindo o peso de um cobertor', 1, @Id_Questao36 + 3), ('Forçando o burro a aceitar a sela rapidamente', 0, @Id_Questao36 + 3), ('Ignorando qualquer resistência do burro', 0, @Id_Questao36 + 3), ('Montando no burro sem preparação prévia', 0, @Id_Questao36 + 3), ('Utilizando um método de doma agressivo', 0, @Id_Questao36 + 3),
('Porque cada burro tem seu próprio ritmo de aprendizado', 1, @Id_Questao36 + 4), ('Porque é possível domar um burro pampa rapidamente', 0, @Id_Questao36 + 4), ('Porque a pressa é essencial na doma', 0, @Id_Questao36 + 4), ('Porque é importante demonstrar impaciência', 0, @Id_Questao36 + 4), ('Porque é mais fácil domar um burro pampa do que outros tipos de animais', 0, @Id_Questao36 + 4);

-- Décimo Questionário

-- Inserir um novo questionário sobre manobrar uma enorme carreta na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Manobrando uma Enorme Carreta', 'Transporte', '2024-04-25', 'Difícil', 'Questionário sobre técnicas de manobra e segurança ao dirigir uma carreta');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario9 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre manobrar uma enorme carreta na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Qual é o princípio básico ao fazer uma curva com uma carreta?', 'Transporte', @Id_Questionario9),
('O que é "efeito tesoura" ao dirigir uma carreta?', 'Transporte', @Id_Questionario9),
('Quando se deve usar o freio de estacionamento ao manobrar uma carreta?', 'Transporte', @Id_Questionario9),
('O que é um "ponto cego" ao dirigir uma carreta?', 'Transporte', @Id_Questionario9),
('Por que é importante fazer verificações regulares nos pneus de uma carreta?', 'Transporte', @Id_Questionario9);

-- Recuperar o ID da primeira questão inserida sobre manobrar uma enorme carreta
SET @Id_Questao41 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre manobrar uma enorme carreta na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Reduzir a velocidade antes de entrar na curva', 1, @Id_Questao41), ('Acelerar bruscamente durante a curva', 0, @Id_Questao41), ('Ignorar as condições da estrada', 0, @Id_Questao41), ('Manter a mesma velocidade ao entrar na curva', 0, @Id_Questao41), ('Virar o volante rapidamente', 0, @Id_Questao41),
('Um movimento perigoso em que o reboque se move em direção à cabine do veículo', 1, @Id_Questao41 + 1), ('Um procedimento seguro durante a manobra de ré', 0, @Id_Questao41 + 1), ('Um método eficaz para aumentar a estabilidade da carreta', 0, @Id_Questao41 + 1), ('Uma técnica de frenagem especialmente projetada para carretas', 0, @Id_Questao41 + 1), ('Uma manobra recomendada para fazer curvas mais fechadas', 0, @Id_Questao41 + 1),
('Sempre que o veículo estiver estacionado em uma superfície inclinada', 1, @Id_Questao41 + 2), ('Apenas em situações de emergência', 0, @Id_Questao41 + 2), ('Durante a condução em estradas planas', 0, @Id_Questao41 + 2), ('Ao fazer curvas', 0, @Id_Questao41 + 2), ('Nunca se deve usar o freio de estacionamento ao manobrar uma carreta', 0, @Id_Questao41 + 2),
('Uma área da estrada que não pode ser vista pelos espelhos retrovisores do motorista', 1, @Id_Questao41 + 3), ('Um local de estacionamento seguro para carretas', 0, @Id_Questao41 + 3), ('O ponto onde a carreta está conectada ao veículo de reboque', 0, @Id_Questao41 + 3), ('O ponto mais seguro para ultrapassagens em uma rodovia', 0, @Id_Questao41 + 3), ('O ponto onde a carreta é mais visível para outros motoristas', 0, @Id_Questao41 + 3),
('Para garantir a aderência adequada à estrada e evitar falhas nos pneus', 1, @Id_Questao41 + 4), ('Para reduzir a eficiência do veículo e aumentar o consumo de combustível', 0, @Id_Questao41 + 4), ('Para criar desafios adicionais ao dirigir', 0, @Id_Questao41 + 4), ('Porque é uma exigência legal para carretas', 0, @Id_Questao41 + 4), ('Porque os pneus de carreta nunca precisam de verificação', 0, @Id_Questao41 + 4);

-- Décimo Primeiro Questionário

-- Inserir um novo questionário sobre a Revolução Constitucionalista na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Revolução Constitucionalista de 1932', 'História', '2024-04-25', 'Médio', 'Questionário sobre os eventos e consequências da Revolução Constitucionalista de 1932');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario10 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre a Revolução Constitucionalista na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Qual foi o motivo principal que levou à Revolução Constitucionalista de 1932?', 'História', @Id_Questionario10),
('Em que estado brasileiro a Revolução Constitucionalista de 1932 teve seu epicentro?', 'História', @Id_Questionario10),
('Quem era o presidente do Brasil durante a Revolução Constitucionalista de 1932?', 'História', @Id_Questionario10),
('Qual foi o desfecho da Revolução Constitucionalista de 1932?', 'História', @Id_Questionario10),
('Qual é o feriado estadual em São Paulo que relembra a Revolução Constitucionalista de 1932?', 'História', @Id_Questionario10);

-- Recuperar o ID da primeira questão inserida sobre a Revolução Constitucionalista
SET @Id_Questao46 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre a Revolução Constitucionalista na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('A insatisfação com a nomeação de um interventor federal em São Paulo', 1, @Id_Questao46), ('A disputa territorial entre São Paulo e outros estados', 0, @Id_Questao46), ('A falta de recursos naturais em São Paulo', 0, @Id_Questao46), ('A interferência estrangeira na política brasileira', 0, @Id_Questao46), ('A recusa de São Paulo em pagar impostos federais', 0, @Id_Questao46),
('São Paulo', 1, @Id_Questao46 + 1), ('Rio de Janeiro', 0, @Id_Questao46 + 1), ('Minas Gerais', 0, @Id_Questao46 + 1), ('Bahia', 0, @Id_Questao46 + 1), ('Pernambuco', 0, @Id_Questao46 + 1),
('Getúlio Vargas', 1, @Id_Questao46 + 2), ('Júlio Prestes', 0, @Id_Questao46 + 2), ('Washington Luís', 0, @Id_Questao46 + 2), ('Artur Bernardes', 0, @Id_Questao46 + 2), ('Dutra', 0, @Id_Questao46 + 2),
('A derrota militar dos paulistas', 1, @Id_Questao46 + 3), ('A vitória dos paulistas e a implementação de uma nova constituição', 0, @Id_Questao46 + 3), ('A assinatura de um acordo de paz entre São Paulo e o governo federal', 0, @Id_Questao46 + 3), ('A intervenção das Nações Unidas para encerrar o conflito', 0, @Id_Questao46 + 3), ('A independência de São Paulo do Brasil', 0, @Id_Questao46 + 3),
('9 de Julho', 1, @Id_Questao46 + 4), ('7 de Setembro', 0, @Id_Questao46 + 4), ('12 de Outubro', 0, @Id_Questao46 + 4), ('21 de Abril', 0, @Id_Questao46 + 4), ('15 de Novembro', 0, @Id_Questao46 + 4);

-- Décimo Segundo Questionário

-- Inserir um novo questionário sobre como tocar uma boiada na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Tocando uma Boiada', 'Agricultura', '2024-04-25', 'Difícil', 'Questionário sobre técnicas e práticas para conduzir uma boiada');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario11 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre como tocar uma boiada na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Qual é a função do peão de boiadeiro ao conduzir uma boiada?', 'Agricultura', @Id_Questionario11),
('Quais são os principais cuidados a serem observados ao conduzir uma boiada em estradas?', 'Agricultura', @Id_Questionario11),
('Como lidar com situações de estresse ou agitação dentro da boiada?', 'Agricultura', @Id_Questionario11),
('Quais são os equipamentos essenciais para um boiadeiro durante o trabalho com boiadas?', 'Agricultura', @Id_Questionario11),
('Qual é a importância da comunicação entre os peões ao tocar uma boiada?', 'Agricultura', @Id_Questionario11);

-- Recuperar o ID da primeira questão inserida sobre como tocar uma boiada
SET @Id_Questao51 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre como tocar uma boiada na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Guiar e controlar o movimento dos bois', 1, @Id_Questao51), ('Cuidar da alimentação dos animais', 0, @Id_Questao51), ('Realizar inspeções veterinárias', 0, @Id_Questao51), ('Administrar as finanças da fazenda', 0, @Id_Questao51), ('Construir cercas para a pastagem', 0, @Id_Questao51),
('Evitar locais com tráfego intenso de veículos', 1, @Id_Questao51 + 1), ('Não permitir que os bois se afastem muito uns dos outros', 0, @Id_Questao51 + 1), ('Utilizar veículos motorizados para conduzir a boiada', 0, @Id_Questao51 + 1), ('Manter uma velocidade alta para chegar ao destino mais rapidamente', 0, @Id_Questao51 + 1), ('Deixar os bois soltos para se guiarem sozinhos', 0, @Id_Questao51 + 1),
('Manter a calma e evitar gestos bruscos', 1, @Id_Questao51 + 2), ('Utilizar chicotes ou gritos para controlar os animais', 0, @Id_Questao51 + 2), ('Deixar os animais agirem conforme desejam', 0, @Id_Questao51 + 2), ('Ignorar qualquer sinal de desconforto nos bois', 0, @Id_Questao51 + 2), ('Acelerar o passo para terminar o trabalho mais rápido', 0, @Id_Questao51 + 2),
('Cavalo, laço, chapéu, e esporas', 1, @Id_Questao51 + 3), ('Celular, laptop, e GPS', 0, @Id_Questao51 + 3), ('Rádio, binóculos, e mapa', 0, @Id_Questao51 + 3), ('Pá, enxada, e regador', 0, @Id_Questao51 + 3), ('Sela, arreios, e rédeas', 0, @Id_Questao51 + 3),
('Para coordenar movimentos e evitar acidentes', 1, @Id_Questao51 + 4), ('Para competir entre si durante a condução', 0, @Id_Questao51 + 4), ('Para demonstrar habilidades individuais', 0, @Id_Questao51 + 4), ('Para entreter os animais', 0, @Id_Questao51 + 4), ('Para atrair a atenção dos espectadores', 0, @Id_Questao51 + 4);

-- Decimo Terceiro Questionário

-- Inserir um novo questionário sobre como proceder ao ver um lobisomem na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Procedimentos ao Encontrar um Lobisomem', 'Folclore', '2024-04-25', 'Difícil', 'Questionário sobre as medidas apropriadas ao deparar-se com um lobisomem');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario12 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre como proceder ao ver um lobisomem na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('Quais são os sinais de que um indivíduo pode ser um lobisomem?', 'Folclore', @Id_Questionario12),
('O que a tradição folclórica recomenda fazer ao se deparar com um lobisomem?', 'Folclore', @Id_Questionario12),
('Como se proteger de um ataque de lobisomem?', 'Folclore', @Id_Questionario12),
('Quais são os objetos ou substâncias considerados eficazes contra lobisomens?', 'Folclore', @Id_Questionario12),
('Qual é a origem do mito do lobisomem em diferentes culturas?', 'Folclore', @Id_Questionario12);

-- Recuperar o ID da primeira questão inserida sobre como proceder ao ver um lobisomem
SET @Id_Questao56 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre como proceder ao ver um lobisomem na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('Pelos corporais excessivos, olhos amarelos, e aversão à prata', 1, @Id_Questao56), ('Cabelos longos, unhas afiadas, e pele pálida', 0, @Id_Questao56), ('Garras afiadas, presas pontiagudas, e visão noturna aguçada', 0, @Id_Questao56), ('Cicatrizes estranhas, olfato aguçado, e gosto por carne crua', 0, @Id_Questao56), ('Atos violentos, comportamento errático, e aversão ao alho', 0, @Id_Questao56),
('Buscar abrigo em local seguro e evitar áreas afastadas', 1, @Id_Questao56 + 1), ('Convidar o lobisomem para uma conversa pacífica', 0, @Id_Questao56 + 1), ('Desafiar o lobisomem para um combate físico', 0, @Id_Questao56 + 1), ('Rir e tirar fotos com o lobisomem', 0, @Id_Questao56 + 1), ('Correr sem destino específico', 0, @Id_Questao56 + 1),
('Usar objetos de prata, como balas ou facas', 1, @Id_Questao56 + 2), ('Rezar fervorosamente', 0, @Id_Questao56 + 2), ('Acender uma fogueira', 0, @Id_Questao56 + 2), ('Pintar símbolos sagrados no chão', 0, @Id_Questao56 + 2), ('Jogar sal sobre o lobisomem', 0, @Id_Questao56 + 2),
('Prata, alho, e água benta', 1, @Id_Questao56 + 3), ('Ferro, sal, e ervas aromáticas', 0, @Id_Questao56 + 3), ('Madeira, água corrente, e mirra', 0, @Id_Questao56 + 3), ('Ouro, incenso, e vinho', 0, @Id_Questao56 + 3), ('Cruz, crucifixo, e hóstia consagrada', 0, @Id_Questao56 + 3),
('O mito do lobisomem tem origens ancestrais em várias culturas ao redor do mundo, incluindo Europa, América do Norte e do Sul, e Ásia', 1, @Id_Questao56 + 4), ('O mito do lobisomem começou na Grécia Antiga e se espalhou para outras regiões por meio da colonização', 0, @Id_Questao56 + 4), ('O mito do lobisomem é exclusivo da Europa, não sendo encontrado em outras culturas', 0, @Id_Questao56 + 4), ('O mito do lobisomem é uma invenção da Idade Média europeia, sem influência em outras partes do mundo', 0, @Id_Questao56 + 4), ('O mito do lobisomem foi criado no século XIX por escritores de ficção, sem base em tradições anteriores', 0, @Id_Questao56 + 4);

-- Décimo Quarto Questionário

-- Inserir um novo questionário sobre como lidar com um desenvolvedor back-end na tabela Tb_Questionarios
INSERT INTO Tb_Questionarios (Nome, Area, Data, Nivel, Descricao)
VALUES ('Lidando com um Dev Back-end', 'Desenvolvimento de Software', '2024-04-25', 'Divertido', 'Questionário divertido sobre os esteriótipos e situações comuns ao lidar com um desenvolvedor back-end');

-- Recuperar o ID do questionário recém-inserido
SET @Id_Questionario13 = LAST_INSERT_ID();

-- Inserir cinco novas questões sobre como lidar com um desenvolvedor back-end na tabela Tb_Questoes
INSERT INTO Tb_Questoes (Enunciado, Area, Id_Questionario)
VALUES
('O que um dev back-end provavelmente diria ao ser questionado sobre o design de uma interface de usuário?', 'Desenvolvimento de Software', @Id_Questionario13),
('Como um dev back-end costuma reagir ao ser interrompido durante a codificação?', 'Desenvolvimento de Software', @Id_Questionario13),
('Qual é o lanche favorito de muitos devs back-end durante uma maratona de codificação?', 'Desenvolvimento de Software', @Id_Questionario13),
('O que acontece quando um dev back-end finalmente resolve um bug difícil?', 'Desenvolvimento de Software', @Id_Questionario13),
('Quais são os hobbies típicos de um dev back-end quando não estão codificando?', 'Desenvolvimento de Software', @Id_Questionario13);

-- Recuperar o ID da primeira questão inserida sobre como lidar com um desenvolvedor back-end
SET @Id_Questao61 = LAST_INSERT_ID();

-- Inserir cinco alternativas para cada questão sobre como lidar com um desenvolvedor back-end na tabela Tb_Alternativas
INSERT INTO Tb_Alternativas (Texto, Correta, Tb_Questoes_Id_Questao)
VALUES
('"Você já viu a documentação? É tudo explicadinho lá."', 1, @Id_Questao61), ('"Ah, design... isso não é comigo, eu só cuido da parte que funciona."', 0, @Id_Questao61), ('"Bom, na minha opinião, menos é mais."', 0, @Id_Questao61), ('"Não se preocupe com a estética, a funcionalidade é o que importa."', 0, @Id_Questao61), ('"Isso parece ser mais um trabalho para o front-end."', 0, @Id_Questao61),
('"Shhh... estou em um estado de fluxo."', 1, @Id_Questao61 + 1), ('Ignora e continua codificando sem dizer nada.', 0, @Id_Questao61 + 1), ('Para tudo e presta atenção na pessoa que o interrompeu.', 0, @Id_Questao61 + 1), ('Levanta a cabeça lentamente e olha fixamente para a pessoa.', 0, @Id_Questao61 + 1), ('Diz "Oi" e volta a codificar.', 0, @Id_Questao61 + 1),
('"Café, energético e pizza."', 1, @Id_Questao61 + 2), ('Salada e água mineral.', 0, @Id_Questao61 + 2), ('Sanduíche natural e suco detox.', 0, @Id_Questao61 + 2), ('Frutas e chá verde.', 0, @Id_Questao61 + 2), ('Barra de cereais e iogurte.', 0, @Id_Questao61 + 2),
('"Bate aquele alívio misturado com um pouco de frustração."', 1, @Id_Questao61 + 3), ('Chama todo mundo para comemorar com uma festa.', 0, @Id_Questao61 + 3), ('Começa a investigar se há outros bugs escondidos.', 0, @Id_Questao61 + 3), ('Tira um cochilo para recarregar as energias.', 0, @Id_Questao61 + 3), ('Começa a procurar por novos desafios para resolver.', 0, @Id_Questao61 + 3),
('Jogar video game, assistir séries e filmes, e participar de grupos de discussão sobre tecnologia.', 1, @Id_Questao61 + 4), ('Jardinagem, culinária e tricô.', 0, @Id_Questao61 + 4), ('Caminhadas ao ar livre, meditação e yoga.', 0, @Id_Questao61 + 4), ('Pintura, música e dança.', 0, @Id_Questao61 + 4), ('Esportes radicais, viagens e leitura de ficção científica.', 0, @Id_Questao61 + 4);
