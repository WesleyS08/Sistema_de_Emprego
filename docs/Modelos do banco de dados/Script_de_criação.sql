-- Inserir dados na tabela Tb_Pessoas
INSERT INTO `SIAS`.`Tb_Pessoas` (`Email`, `Senha`, `Nome`, `Token`, `Verificado`)
VALUES
('john.doe@example.com', SHA1('123'), 'John Doe', NULL, 1),
('jane.doe@example.com', SHA1('123'), 'Jane Doe', NULL, 1),
('alice.smith@example.com', SHA1('123'), 'Alice Smith', NULL, 1),
('bob.johnson@example.com', SHA1('123'), 'Bob Johnson', NULL, 1),
('chris.jones@example.com', SHA1('123'), 'Chris Jones', NULL, 1),
('karen.white@example.com', SHA1('123'), 'Karen White', NULL, 0),
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
('bruce.wright@example.com', SHA1('123'), 'Bruce Wright', NULL, 0),
('patrick.hill@example.com', SHA1('123'), 'Patrick Hill', NULL, 1),
('kathy.martin@example.com', SHA1('123'), 'Kathy Martin', NULL, 1),
('rachel.hall@example.com', SHA1('123'), 'Rachel Hall', NULL, 1),
('victor.green@example.com', SHA1('123'), 'Victor Green', NULL, 1),
('laura.adams@example.com', SHA1('123'), 'Laura Adams', NULL, 1),
('tom.baker@example.com', SHA1('123'), 'Tom Baker', NULL, 1),
('nancy.thomas@example.com', SHA1('123'), 'Nancy Thomas', NULL, 1),
('linda.brown@example.com', SHA1('123'), 'Linda Brown', NULL, 1),
('steve.wilson@example.com', SHA1('123'), 'Steve Wilson', NULL, 1),
('lisa.martin@example.com', SHA1('123'), 'Lisa Martin', NULL, 1),
('eric.garcia@example.com', SHA1('123'), 'Eric Garcia', NULL, 1),
('emma.rodriguez@example.com', SHA1('123'), 'Emma Rodriguez', NULL, 1),
('david.harris@example.com', SHA1('123'), 'David Harris', NULL, 1),
('susan.clark@example.com', SHA1('123'), 'Susan Clark', NULL, 1),
('paul.lee@example.com', SHA1('123'), 'Paul Lee', NULL, 1),
('karen.walker@example.com', SHA1('123'), 'Karen Walker', NULL, 0),
('william.moore@example.com', SHA1('123'), 'William Moore', NULL, 1),
('maria.turner@example.com', SHA1('123'), 'Maria Turner', NULL, 1),
('joseph.young@example.com', SHA1('123'), 'Joseph Young', NULL, 1),
('carolyn.king@example.com', SHA1('123'), 'Carolyn King', NULL, 1),
('jason.hill@example.com', SHA1('123'), 'Jason Hill', NULL, 1),
('rachel.wright@example.com', SHA1('123'), 'Rachel Wright', NULL, 1),
('patrick.lopez@example.com', SHA1('123'), 'Patrick Lopez', NULL, 1),
('diana.scott@example.com', SHA1('123'), 'Diana Scott', NULL, 1),
('frank.green@example.com', SHA1('123'), 'Frank Green', NULL, 1),
('michael.adams@example.com', SHA1('123'), 'Michael Adams', NULL, 1),
('theresa.wright@example.com', SHA1('123'), 'Theresa Wright', NULL, 1),
('gregory.campbell@example.com', SHA1('123'), 'Gregory Campbell', NULL, 1),
('sharon.phillips@example.com', SHA1('123'), 'Sharon Phillips', NULL, 1),
('raymond.evans@example.com', SHA1('123'), 'Raymond Evans', NULL, 1);

-- Criar uma tabela temporária para armazenar os primeiros 50 IDs sequenciais
CREATE TEMPORARY TABLE `temp_ids` AS
SELECT `Id_Pessoas`
FROM `SIAS`.`Tb_Pessoas`
ORDER BY `Id_Pessoas` ASC
LIMIT 50;

-- Dividir os IDs de `Tb_Pessoas` em dois conjuntos
CREATE TEMPORARY TABLE `temp_ids_candidato` AS
SELECT `Id_Pessoas`
FROM `temp_ids`
ORDER BY RAND()
LIMIT 30;

CREATE TEMPORARY TABLE `temp_ids_empresa` AS
SELECT `Id_Pessoas`
FROM `temp_ids`
WHERE `Id_Pessoas` NOT IN (SELECT `Id_Pessoas` FROM `temp_ids_candidato`)
ORDER BY RAND()
LIMIT 20;

-- Inserir dados em Tb_Candidato
INSERT INTO `SIAS`.`Tb_Candidato`
(`CPF`, `Tb_Pessoas_Id`, `Area_de_Interesse`, `Tipo_de_Contratacao`, `Descricao`, `Experiencia`, `Motivacoes`, `Cursos`, `Escolaridade`, `Genero`, `Estado_Civil`, `Idade`, `Telefone`, `Data_Nascimento`, `Cidade`, `PCD`, `Img_Perfil`, `Banner`)
SELECT
  RIGHT(MD5(CONCAT(`Id_Pessoas`, RAND())), 11),  -- CPF fictício
  `Id_Pessoas`,
  'Tecnologia',
  'CLT',
  'Descrição exemplo',
  'Experiência exemplo',
  'Motivação exemplo',
  'Cursos exemplo',
  'Graduação',
  'Masculino',
  'Solteiro',
  30,
  '5511987654321',
  '1990-01-01',
  'São Paulo',
  false,
  NULL,
  NULL
FROM `temp_ids_candidato`;

-- Inserir dados em Tb_Empresa
INSERT INTO `SIAS`.`Tb_Empresa`
(`CNPJ`, `Tb_Pessoas_Id`, `Img_Banner`, `Facebook`, `Github`, `Linkedin`, `Instagram`, `Nome_da_Empresa`, `Sobre_a_Empresa`, `Area_da_Empresa`, `Avaliacao_de_Funcionarios`, `Avaliacao_Geral`, `Telefone`, `Img_Perfil`)
SELECT
  RIGHT(MD5(CONCAT(`Id_Pessoas`, RAND())), 14),  -- CNPJ fictício
  `Id_Pessoas`,
  NULL,  -- Imagem de banner
  'facebook.com/empresa',  -- Link para Facebook
  'github.com/empresa',  -- Link para GitHub
  'linkedin.com/empresa',  -- Link para LinkedIn
  'instagram.com/empresa',  -- Link para Instagram
  'Empresa Exemplo',  -- Nome da empresa
  'Texto sobre a empresa',  -- Sobre a empresa
  'Área de exemplo',  -- Área de atuação da empresa
  '4',  -- Avaliação de funcionários
  '5',  -- Avaliação geral
  '5511987654321',  -- Telefone
  NULL  -- Imagem de perfil
FROM `temp_ids_empresa`;

-- Limpar tabelas temporárias
DROP TABLE `temp_ids`;
DROP TABLE `temp_ids_candidato`;
DROP TABLE `temp_ids_empresa`;

insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Designer Gráfico', 'Pellentesque at nulla. Suspendisse potenti. Cras in purus eu magna vulputate luctus.

Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', 'Psicologia Clínica', 'Talisayan', 'Ensino Médio', '2020/06/20', 'Remoto', 'Flexibilidade de horário', 'Conhecimento em marketing digital', '07:45 - 16:45', 'Bahia (BA)', 'Tempo integral', '01000-000', 'Avenida Paulista', 1, 'Centro');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Especialista em Recursos Humanos', 'Pellentesque at nulla. Suspendisse potenti. Cras in purus eu magna vulputate luctus.

Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.

Etiam vel augue. Vestibulum rutrum rutrum neque. Aenean auctor gravida sem.', 'Engenharia de Software', 'Vendargues', 'Ensino Superior', '2021/10/29', 'Remoto', 'Assistência odontológica', 'Conhecimento em marketing digital', '07:10 - 16:10', 'Ceará (CE)', 'Meio período', '20000-000', 'Rua Augusta', 2, 'Bela Vista');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Analista de Marketing Digital', 'Duis aliquam convallis nunc. Proin at turpis a pede posuere nonummy. Integer non velit.', 'Assistência Social', 'Yulin', 'Ensino Superior', '2020/12/09', 'Remoto', 'Assistência odontológica', 'Conhecimento em informática', '09:00 - 18:00', 'Rio de Janeiro (RJ)', 'Meio período', '30100-000', 'Avenida Brasil', 3, 'Consolação');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ('Jovem Aprendiz', 'Técnico de Suporte de TI', 'In hac habitasse platea dictumst. Etiam faucibus cursus urna. Ut tellus.

Nulla ut erat id mauris vulputate elementum. Nullam varius. Nulla facilisi.', 'Tecnologia da Informação', 'Pedroso', 'Ensino Superior', '2024/09/21', 'Remoto', 'Seguro de vida', 'Conhecimento em informática', '09:40 - 18:40', 'Tocantins (TO)', 'Meio período', '40000-000', 'Rua XV de Novembro', 4, 'Pinheiros');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Consultor Financeiro', 'Integer tincidunt ante vel ipsum. Praesent blandit lacinia erat. Vestibulum sed magna at nunc commodo placerat.', 'Engenharia Mecânica', 'São Gabriel da Cachoeira', 'Ensino Superior', '2020/08/14', 'Remoto', 'Assistência odontológica', 'Organização e proatividade', '10:45 - 19:45', 'Rio Grande do Sul (RS)', 'Meio período', '60000-000', 'Rua das Flores', 5, 'Copacabana');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Especialista em Recursos Humanos', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin risus. Praesent lectus.', 'Gestão de Recursos Humanos', 'Graneros', 'Ensino Médio', '2022/02/24', 'Remoto', 'Vale-transporte', 'Habilidade em trabalho em equipe', '10:15 - 19:15', 'Mato Grosso do Sul (MS)', 'Meio período', '70000-000', 'Avenida Sete de Setembro', 6, 'Meireles');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Desenvolvedor Full Stack', 'Praesent blandit. Nam nulla. Integer pede justo, lacinia eget, tincidunt eget, tempus vel, pede.', 'Assistência Social', 'Milltown', 'Ensino Técnico', '2021/06/17', 'Presencial', 'Flexibilidade de horário', 'Experiência em vendas', '08:10 - 17:10', 'Acre (AC)', 'Meio período', '80000-000', 'Rua dos Andradas', 7, 'Boa Vista');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Quisque porta volutpat erat. Quisque erat eros, viverra eget, congue eget, semper rutrum, nulla. Nunc purus.', 'Fisioterapia', 'Linshanhe', 'Ensino Médio', '2022/03/11', 'Presencial', 'Assistência odontológica', 'Conhecimento em marketing digital', '10:00 - 19:00', 'Alagoas (AL)', 'Meio período', '90000-000', 'Avenida Beira-Mar', 8, 'Barra');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.

Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.

In quis justo. Maecenas rhoncus aliquam lacus. Morbi quis tortor id nulla ultrices aliquet.', 'Gestão de Recursos Humanos', 'Simo', 'Ensino Superior', '2020/07/10', 'Remoto', 'Participação nos lucros', 'Organização e proatividade', '07:30 - 16:30', 'Roraima (RR)', 'Tempo integral', '50000-000', 'Rua da Aurora', 9, 'Batel');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris.

Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.

Fusce posuere felis sed lacus. Morbi sem mauris, laoreet ut, rhoncus aliquet, pulvinar sed, nisl. Nunc rhoncus dui vel sem.', 'Design Gráfico', 'Sebegen', 'Ensino Técnico', '2020/08/13', 'Presencial', 'Vale-transporte', 'Experiência em vendas', '08:15 - 17:15', 'Paraíba (PB)', 'Tempo integral', '65000-000', 'Avenida Rio Branco', 10, 'Savassi');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ('PJ', 'Técnico de Suporte de TI', 'Quisque id justo sit amet sapien dignissim vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla dapibus dolor vel est. Donec odio justo, sollicitudin ut, suscipit a, feugiat et, eros.

Vestibulum ac est lacinia nisi venenatis tristique. Fusce congue, diam id ornare imperdiet, sapien urna pretium nisl, ut volutpat sapien arcu sed augue. Aliquam erat volutpat.

In congue. Etiam justo. Etiam pretium iaculis justo.', 'Engenharia Civil', 'Qingyang', 'Ensino Médio', '2020/11/25', 'Presencial', 'Seguro de vida', 'Experiência em vendas', '09:50 - 18:50', 'Paraíba (PB)', 'Meio período', '78000-000', 'Rua Oscar Freire', 11, 'Liberdade');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Coordenador de Projetos', 'Cras non velit nec nisi vulputate nonummy. Maecenas tincidunt lacus at velit. Vivamus vel nulla eget eros elementum pellentesque.', 'Consultoria Financeira', 'Nytva', 'Ensino Médio', '2022/06/10', 'Presencial', 'Plano de carreira', 'Inglês avançado', '07:15 - 16:15', 'Espírito Santo (ES)', 'Tempo integral', '69000-000', 'Avenida Afonso Pena', 12, 'Moema');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Assistente Administrativo', 'Sed ante. Vivamus tortor. Duis mattis egestas metus.

Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.', 'Moda e Estilo', 'Chabu', 'Ensino Médio', '2021/04/06', 'Remoto', 'Vale-transporte', 'Experiência em vendas', '10:00 - 19:00', 'Maranhão (MA)', 'Tempo integral', '66000-000', 'Rua dos Pinheiros', 13, 'Ipanema');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Curabitur in libero ut massa volutpat convallis. Morbi odio odio, elementum eu, interdum eu, tincidunt in, leo. Maecenas pulvinar lobortis est.', 'Engenharia Civil', 'Xiashe', 'Ensino Superior', '2023/07/02', 'Remoto', 'Flexibilidade de horário', 'Organização e proatividade', '07:10 - 16:10', 'Acre (AC)', 'Meio período', '77000-000', 'Avenida Atlântica', 14, 'Leblon');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Designer Gráfico', 'Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.', 'Gastronomia', 'Banzhong', 'Ensino Médio', '2023/11/14', 'Remoto', 'Assistência odontológica', 'Experiência em vendas', '07:30 - 16:30', 'Tocantins (TO)', 'Meio período', '57000-000', 'Rua Barão de Itapetininga', 15, 'Higienópolis');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Consultor Financeiro', 'Curabitur gravida nisi at nibh. In hac habitasse platea dictumst. Aliquam augue quam, sollicitudin vitae, consectetuer eget, rutrum at, lorem.

Integer tincidunt ante vel ipsum. Praesent blandit lacinia erat. Vestibulum sed magna at nunc commodo placerat.

Praesent blandit. Nam nulla. Integer pede justo, lacinia eget, tincidunt eget, tempus vel, pede.', 'Assistência Social', 'Rio Branco', 'Ensino Médio', '2021/11/03', 'Presencial', 'Home office', 'Formação em Administração', '09:45 - 18:45', 'Alagoas (AL)', 'Tempo integral', '58000-000', 'Rua João Pessoa', 16, 'Perdizes');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Assistente Administrativo', 'Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.

Fusce posuere felis sed lacus. Morbi sem mauris, laoreet ut, rhoncus aliquet, pulvinar sed, nisl. Nunc rhoncus dui vel sem.

Sed sagittis. Nam congue, risus semper porta volutpat, quam pede lobortis ligula, sit amet eleifend pede libero quis orci. Nullam molestie nibh in lectus.', 'Arquitetura de Interiores', 'Bhadrapur', 'Ensino Superior', '2023/05/31', 'Remoto', 'Plano de carreira', 'Formação em Administração', '10:45 - 19:45', 'Amazonas (AM)', 'Tempo integral', '64000-000', 'Avenida Nossa Senhora de Copacabana', 17, 'Itaim Bibi');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Designer Gráfico', 'Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.

Quisque id justo sit amet sapien dignissim vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla dapibus dolor vel est. Donec odio justo, sollicitudin ut, suscipit a, feugiat et, eros.', 'Fisioterapia', 'Dhankutā', 'Ensino Técnico', '2020/05/13', 'Remoto', 'Plano de carreira', 'Experiência em atendimento ao cliente', '07:45 - 16:45', 'Rio Grande do Sul (RS)', 'Tempo integral', '49000-000', 'Rua do Comércio', 18, 'Vila Madalena');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Desenvolvedor Full Stack', 'In sagittis dui vel nisl. Duis ac nibh. Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.

Suspendisse potenti. In eleifend quam a odio. In hac habitasse platea dictumst.

Maecenas ut massa quis augue luctus tincidunt. Nulla mollis molestie lorem. Quisque ut erat.', 'Assistência Jurídica', 'Karlovo', 'Ensino Técnico', '2023/10/16', 'Remoto', 'Assistência odontológica', 'Boa comunicação oral e escrita', '09:30 - 18:30', 'Amazonas (AM)', 'Meio período', '79000-000', 'Avenida Presidente Vargas', 19, 'Jardins');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Especialista em Recursos Humanos', 'Duis consequat dui nec nisi volutpat eleifend. Donec ut dolor. Morbi vel lectus in quam fringilla rhoncus.

Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.

Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.', 'Medicina Veterinária', 'Rokiškis', 'Ensino Superior', '2023/02/16', 'Presencial', 'Bônus por desempenho', 'Disponibilidade de horário', '10:45 - 19:45', 'São Paulo (SP)', 'Tempo integral', '80230-000', 'Rua Conselheiro Aguiar', 20, 'Botafogo');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Especialista em Recursos Humanos', 'Aliquam quis turpis eget elit sodales scelerisque. Mauris sit amet eros. Suspendisse accumsan tortor quis turpis.

Sed ante. Vivamus tortor. Duis mattis egestas metus.

Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.', 'Design Gráfico', 'Long Beach', 'Ensino Técnico', '2021/08/01', 'Presencial', 'Flexibilidade de horário', 'Conhecimento em marketing digital', '09:30 - 18:30', 'Goiás (GO)', 'Tempo integral', '69900-000', 'Rua Domingos Ferreira', 21, 'Flamengo');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Engenheiro de Software', 'Phasellus sit amet erat. Nulla tempus. Vivamus in felis eu sapien cursus vestibulum.

Proin eu mi. Nulla ac enim. In tempor, turpis nec euismod scelerisque, quam turpis adipiscing lorem, vitae mattis nibh ligula nec sem.

Duis aliquam convallis nunc. Proin at turpis a pede posuere nonummy. Integer non velit.', 'Engenharia Mecânica', 'København', 'Ensino Médio', '2020/01/27', 'Remoto', 'Assistência odontológica', 'Formação em Administração', '09:40 - 18:40', 'Distrito Federal (DF)', 'Meio período', '76800-000', 'Rua Padre Cícero', 22, 'Vila Mariana');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Engenheiro de Software', 'Duis bibendum. Morbi non quam nec dui luctus rutrum. Nulla tellus.

In sagittis dui vel nisl. Duis ac nibh. Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.', 'Arquitetura de Interiores', 'Pierzchnica', 'Ensino Técnico', '2023/02/28', 'Presencial', 'Assistência odontológica', 'Inglês avançado', '09:00 - 18:00', 'Bahia (BA)', 'Tempo integral', '77015-000', 'Rua Carlos Gomes', 23, 'Lapa');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Técnico de Suporte de TI', 'Curabitur in libero ut massa volutpat convallis. Morbi odio odio, elementum eu, interdum eu, tincidunt in, leo. Maecenas pulvinar lobortis est.

Phasellus sit amet erat. Nulla tempus. Vivamus in felis eu sapien cursus vestibulum.', 'Design Gráfico', 'Makati City', 'Ensino Técnico', '2021/05/30', 'Presencial', 'Vale-refeição', 'Inglês avançado', '10:20 - 19:20', 'Ceará (CE)', 'Tempo integral', '58900-000', 'Avenida Ipiranga', 24, 'Santa Cecília');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Coordenador de Projetos', 'Phasellus in felis. Donec semper sapien a libero. Nam dui.

Proin leo odio, porttitor id, consequat in, consequat ut, nulla. Sed accumsan felis. Ut at dolor quis odio consequat varius.

Integer ac leo. Pellentesque ultrices mattis odio. Donec vitae nisi.', 'Educação Infantil', 'Xialu', 'Ensino Técnico', '2020/02/05', 'Remoto', 'Plano de saúde', 'Experiência em atendimento ao cliente', '07:00 - 16:00', 'Paraíba (PB)', 'Tempo integral', '96000-000', 'Rua 25 de Março', 25, 'Vila Olímpia');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Analista de Marketing Digital', 'Donec diam neque, vestibulum eget, vulputate ut, ultrices vel, augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec pharetra, magna vestibulum aliquet ultrices, erat tortor sollicitudin mi, sit amet lobortis sapien sapien non mi. Integer ac neque.

Duis bibendum. Morbi non quam nec dui luctus rutrum. Nulla tellus.', 'Engenharia de Software', 'Haozigang', 'Ensino Médio', '2022/09/25', 'Remoto', 'Vale-transporte', 'Experiência em atendimento ao cliente', '09:30 - 18:30', 'Goiás (GO)', 'Meio período', '88000-000', 'Avenida Brigadeiro Faria Lima', 26, 'Glória');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Analista de Marketing Digital', 'In hac habitasse platea dictumst. Etiam faucibus cursus urna. Ut tellus.', 'Gastronomia', 'Golubinci', 'Ensino Superior', '2023/03/17', 'Remoto', 'Seguro de vida', 'Experiência em vendas', '10:45 - 19:45', 'Alagoas (AL)', 'Meio período', '01100-000', 'Rua Haddock Lobo', 27, 'Lagoa');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Assistente Administrativo', 'In congue. Etiam justo. Etiam pretium iaculis justo.', 'Tecnologia da Informação', 'Żarnów', 'Ensino Técnico', '2022/08/01', 'Remoto', 'Home office', 'Conhecimento em informática', '07:20 - 16:20', 'Mato Grosso (MT)', 'Meio período', '66000-600', 'Avenida Presidente Kennedy', 28, 'Santo Amaro');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Coordenador de Projetos', 'Fusce consequat. Nulla nisl. Nunc nisl.', 'Fisioterapia', 'Miringa', 'Ensino Superior', '2020/10/05', 'Remoto', 'Vale-refeição', 'Organização e proatividade', '09:20 - 18:20', 'Amazonas (AM)', 'Meio período', '64001-000', 'Rua Gomes de Freitas', 29, 'Morumbi');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Gerente de Vendas', 'Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris.

Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.

Fusce posuere felis sed lacus. Morbi sem mauris, laoreet ut, rhoncus aliquet, pulvinar sed, nisl. Nunc rhoncus dui vel sem.', 'Educação Infantil', 'Sanbaishan', 'Ensino Médio', '2021/07/20', 'Presencial', 'Seguro de vida', 'Experiência em vendas', '08:10 - 17:10', 'Rio Grande do Sul (RS)', 'Meio período', '86000-000', 'Rua Dona Maria Paula', 30, 'Tijuca');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Analista de Marketing Digital', 'Duis bibendum. Morbi non quam nec dui luctus rutrum. Nulla tellus.

In sagittis dui vel nisl. Duis ac nibh. Fusce lacus purus, aliquet at, feugiat non, pretium quis, lectus.', 'Desenvolvimento de Produto', 'Gardēz', 'Ensino Superior', '2020/03/30', 'Presencial', 'Bônus por desempenho', 'Organização e proatividade', '08:15 - 17:15', 'Sergipe (SE)', 'Meio período', '59000-000', 'Avenida Domingos Olímpio', 31, 'Grajaú');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Desenvolvedor Full Stack', 'Proin leo odio, porttitor id, consequat in, consequat ut, nulla. Sed accumsan felis. Ut at dolor quis odio consequat varius.

Integer ac leo. Pellentesque ultrices mattis odio. Donec vitae nisi.

Nam ultrices, libero non mattis pulvinar, nulla pede ullamcorper augue, a suscipit nulla elit ac nulla. Sed vel enim sit amet nunc viverra dapibus. Nulla suscipit ligula in lacus.', 'Engenharia de Software', 'Welchman Hall', 'Ensino Técnico', '2020/12/31', 'Presencial', 'Participação nos lucros', 'Formação em Administração', '10:00 - 19:00', 'Rondônia (RO)', 'Meio período', '79000-000', 'Rua Sapucaí', 32, 'Freguesia');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Assistente Administrativo', 'Nulla ut erat id mauris vulputate elementum. Nullam varius. Nulla facilisi.

Cras non velit nec nisi vulputate nonummy. Maecenas tincidunt lacus at velit. Vivamus vel nulla eget eros elementum pellentesque.', 'Assistência Jurídica', 'Yima', 'Ensino Médio', '2024/04/30', 'Remoto', 'Home office', 'Habilidade em trabalho em equipe', '10:45 - 19:45', 'Mato Grosso (MT)', 'Tempo integral', '78000-200', 'Avenida Pedro Álvares Cabral', 33, 'Jardim Botânico');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Especialista em Recursos Humanos', 'Phasellus in felis. Donec semper sapien a libero. Nam dui.

Proin leo odio, porttitor id, consequat in, consequat ut, nulla. Sed accumsan felis. Ut at dolor quis odio consequat varius.

Integer ac leo. Pellentesque ultrices mattis odio. Donec vitae nisi.', 'Psicologia Clínica', 'Ciudad del Este', 'Ensino Médio', '2021/03/18', 'Presencial', 'Vale-refeição', 'Formação em Administração', '09:30 - 18:30', 'São Paulo (SP)', 'Tempo integral', '64020-000', 'Rua Marechal Deodoro', 34, 'Tijuca');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Gerente de Vendas', 'Vestibulum ac est lacinia nisi venenatis tristique. Fusce congue, diam id ornare imperdiet, sapien urna pretium nisl, ut volutpat sapien arcu sed augue. Aliquam erat volutpat.', 'Design Gráfico', 'Banjarmasin', 'Ensino Técnico', '2023/02/23', 'Presencial', 'Assistência odontológica', 'Disponibilidade de horário', '10:00 - 19:00', 'Amazonas (AM)', 'Meio período', '66010-000', 'Avenida Engenheiro Luiz Carlos Berrini', 35, 'São Cristóvão');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Assistente Administrativo', 'Morbi porttitor lorem id ligula. Suspendisse ornare consequat lectus. In est risus, auctor sed, tristique in, tempus sit amet, sem.', 'Fisioterapia', 'Dafeng', 'Ensino Médio', '2024/01/04', 'Remoto', 'Seguro de vida', 'Formação em Administração', '09:00 - 18:00', 'Tocantins (TO)', 'Meio período', '02000-000', 'Rua Visconde de Pirajá', 36, 'Alto de Pinheiros');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Coordenador de Projetos', 'Duis bibendum, felis sed interdum venenatis, turpis enim blandit mi, in porttitor pede justo eu massa. Donec dapibus. Duis at velit eu est congue elementum.

In hac habitasse platea dictumst. Morbi vestibulum, velit id pretium iaculis, diam erat fermentum justo, nec condimentum neque sapien placerat ante. Nulla justo.

Aliquam quis turpis eget elit sodales scelerisque. Mauris sit amet eros. Suspendisse accumsan tortor quis turpis.', 'Jornalismo', 'Qixing', 'Ensino Superior', '2024/04/16', 'Remoto', 'Plano de carreira', 'Organização e proatividade', '10:15 - 19:15', 'Piauí (PI)', 'Tempo integral', '88030-000', 'Avenida Santo Amaro', 37, 'Paraíso');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Técnico de Suporte de TI', 'In congue. Etiam justo. Etiam pretium iaculis justo.

In hac habitasse platea dictumst. Etiam faucibus cursus urna. Ut tellus.', 'Fisioterapia', 'Chipinge', 'Ensino Técnico', '2020/11/24', 'Remoto', 'Plano de carreira', 'Boa comunicação oral e escrita', '09:30 - 18:30', 'Acre (AC)', 'Meio período', '45000-000', 'Rua São Bento', 38, 'Vila Clementino');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Coordenador de Projetos', 'Curabitur gravida nisi at nibh. In hac habitasse platea dictumst. Aliquam augue quam, sollicitudin vitae, consectetuer eget, rutrum at, lorem.', 'Assistência Jurídica', 'Sidrolândia', 'Ensino Técnico', '2023/05/05', 'Presencial', 'Bônus por desempenho', 'Boa comunicação oral e escrita', '10:20 - 19:20', 'Ceará (CE)', 'Meio período', '60300-000', 'Avenida Jornalista Ricardo Marinho', 39, 'Barra Funda');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Coordenador de Projetos', 'Maecenas leo odio, condimentum id, luctus nec, molestie sed, justo. Pellentesque viverra pede ac diam. Cras pellentesque volutpat dui.

Maecenas tristique, est et tempus semper, est quam pharetra magna, ac consequat metus sapien ut nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris viverra diam vitae quam. Suspendisse potenti.', 'Marketing Digital', 'Paço', 'Ensino Superior', '2024/03/04', 'Presencial', 'Plano de carreira', 'Conhecimento em informática', '07:30 - 16:30', 'Pará (PA)', 'Meio período', '69300-000', 'Rua Vinte e Quatro de Maio', 40, 'Jardim Europa');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Coordenador de Projetos', 'Duis bibendum, felis sed interdum venenatis, turpis enim blandit mi, in porttitor pede justo eu massa. Donec dapibus. Duis at velit eu est congue elementum.', 'Fisioterapia', 'Muang Long', 'Ensino Superior', '2022/12/17', 'Remoto', 'Seguro de vida', 'Experiência em atendimento ao cliente', '09:00 - 18:00', 'Bahia (BA)', 'Meio período', '52000-000', 'Avenida Vereador José Diniz', 41, 'Maracanã');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Duis consequat dui nec nisi volutpat eleifend. Donec ut dolor. Morbi vel lectus in quam fringilla rhoncus.

Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.

Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.', 'Moda e Estilo', 'Cimadang', 'Ensino Superior', '2022/08/20', 'Remoto', 'Seguro de vida', 'Disponibilidade de horário', '07:30 - 16:30', 'São Paulo (SP)', 'Tempo integral', '68000-000', 'Rua Almirante Barroso', 42, 'Catete');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ('Jovem Aprendiz', 'Engenheiro de Software', 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', 'Educação Infantil', 'Suure-Jaani', 'Ensino Médio', '2021/06/01', 'Remoto', 'Plano de carreira', 'Experiência em atendimento ao cliente', '10:20 - 19:20', 'Amapá (AP)', 'Meio período', '66000-100', 'Rua Senador Pompeu', 43, 'Barra da Tijuca');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Especialista em Recursos Humanos', 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vivamus vestibulum sagittis sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.

Etiam vel augue. Vestibulum rutrum rutrum neque. Aenean auctor gravida sem.', 'Gastronomia', 'Sohag', 'Ensino Técnico', '2022/11/02', 'Remoto', 'Home office', 'Habilidade em trabalho em equipe', '10:00 - 19:00', 'Pernambuco (PE)', 'Tempo integral', '70070-000', 'Rua Dom Pedro II', 44, 'Vila Isabel');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Designer Gráfico', 'Quisque id justo sit amet sapien dignissim vestibulum. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla dapibus dolor vel est. Donec odio justo, sollicitudin ut, suscipit a, feugiat et, eros.

Vestibulum ac est lacinia nisi venenatis tristique. Fusce congue, diam id ornare imperdiet, sapien urna pretium nisl, ut volutpat sapien arcu sed augue. Aliquam erat volutpat.', 'Engenharia Mecânica', 'Vientiane', 'Ensino Médio', '2021/01/18', 'Remoto', 'Participação nos lucros', 'Boa comunicação oral e escrita', '07:20 - 16:20', 'Acre (AC)', 'Tempo integral', '88700-000', 'Avenida Washington Soares', 45, 'Madureira');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Técnico de Suporte de TI', 'Donec diam neque, vestibulum eget, vulputate ut, ultrices vel, augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec pharetra, magna vestibulum aliquet ultrices, erat tortor sollicitudin mi, sit amet lobortis sapien sapien non mi. Integer ac neque.', 'Consultoria Financeira', 'Wuli', 'Ensino Médio', '2024/03/03', 'Presencial', 'Vale-transporte', 'Conhecimento em informática', '08:45 - 17:45', 'Goiás (GO)', 'Meio período', '69000-400', 'Rua Voluntários da Pátria', 46, 'Penha');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Engenheiro de Software', 'In congue. Etiam justo. Etiam pretium iaculis justo.', 'Engenharia Mecânica', 'Atbasar', 'Ensino Médio', '2023/04/25', 'Presencial', 'Flexibilidade de horário', 'Organização e proatividade', '08:10 - 17:10', 'Distrito Federal (DF)', 'Meio período', '57000-300', 'Avenida Indianópolis', 47, 'Vila Prudente');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Analista de Marketing Digital', 'Cras non velit nec nisi vulputate nonummy. Maecenas tincidunt lacus at velit. Vivamus vel nulla eget eros elementum pellentesque.', 'Assistência Jurídica', 'Oropéndolas', 'Ensino Médio', '2022/12/01', 'Remoto', 'Vale-transporte', 'Experiência em atendimento ao cliente', '09:40 - 18:40', 'Amazonas (AM)', 'Meio período', '79100-000', 'Rua dos Timbiras', 48, 'Santana');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Técnico de Suporte de TI', 'Curabitur gravida nisi at nibh. In hac habitasse platea dictumst. Aliquam augue quam, sollicitudin vitae, consectetuer eget, rutrum at, lorem.', 'Design Gráfico', 'Aranitas', 'Ensino Superior', '2021/05/20', 'Presencial', 'Plano de saúde', 'Experiência em vendas', '09:30 - 18:30', 'Paraíba (PB)', 'Meio período', '80010-000', 'Avenida Prefeito Dulcídio Cardoso', 49, 'Jabaquara');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Analista de Marketing Digital', 'Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris.

Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.

Fusce posuere felis sed lacus. Morbi sem mauris, laoreet ut, rhoncus aliquet, pulvinar sed, nisl. Nunc rhoncus dui vel sem.', 'Consultoria Financeira', 'Goya', 'Ensino Técnico', '2022/12/02', 'Presencial', 'Flexibilidade de horário', 'Conhecimento em informática', '09:30 - 18:30', 'Amapá (AP)', 'Meio período', '01010-000', 'Rua Bento Freitas', 50, 'Santa Teresa');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Coordenador de Projetos', 'Donec diam neque, vestibulum eget, vulputate ut, ultrices vel, augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec pharetra, magna vestibulum aliquet ultrices, erat tortor sollicitudin mi, sit amet lobortis sapien sapien non mi. Integer ac neque.

Duis bibendum. Morbi non quam nec dui luctus rutrum. Nulla tellus.', 'Moda e Estilo', 'Causwagan', 'Ensino Médio', '2021/04/28', 'Remoto', 'Vale-transporte', 'Formação em Administração', '09:45 - 18:45', 'Rio Grande do Norte (RN)', 'Tempo integral', '01000-000', 'Avenida Paulista', 51, 'Pacaembu');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Designer Gráfico', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Proin risus. Praesent lectus.

Vestibulum quam sapien, varius ut, blandit non, interdum in, ante. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis faucibus accumsan odio. Curabitur convallis.

Duis consequat dui nec nisi volutpat eleifend. Donec ut dolor. Morbi vel lectus in quam fringilla rhoncus.', 'Consultoria Financeira', 'Tondabayashichō', 'Ensino Técnico', '2024/01/31', 'Remoto', 'Participação nos lucros', 'Conhecimento em informática', '08:10 - 17:10', 'Maranhão (MA)', 'Tempo integral', '20000-000', 'Rua Augusta', 52, 'Centro');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Desenvolvedor Full Stack', 'Vestibulum quam sapien, varius ut, blandit non, interdum in, ante. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis faucibus accumsan odio. Curabitur convallis.

Duis consequat dui nec nisi volutpat eleifend. Donec ut dolor. Morbi vel lectus in quam fringilla rhoncus.', 'Gastronomia', 'Usuki', 'Ensino Superior', '2021/01/26', 'Presencial', 'Assistência odontológica', 'Experiência em atendimento ao cliente', '10:15 - 19:15', 'Mato Grosso (MT)', 'Meio período', '30100-000', 'Avenida Brasil', 53, 'Bela Vista');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Coordenador de Projetos', 'Fusce posuere felis sed lacus. Morbi sem mauris, laoreet ut, rhoncus aliquet, pulvinar sed, nisl. Nunc rhoncus dui vel sem.

Sed sagittis. Nam congue, risus semper porta volutpat, quam pede lobortis ligula, sit amet eleifend pede libero quis orci. Nullam molestie nibh in lectus.', 'Assistência Jurídica', 'Wang Noi', 'Ensino Superior', '2024/04/14', 'Remoto', 'Assistência odontológica', 'Conhecimento em marketing digital', '10:15 - 19:15', 'Amapá (AP)', 'Tempo integral', '40000-000', 'Rua XV de Novembro', 54, 'Consolação');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Assistente Administrativo', 'Proin interdum mauris non ligula pellentesque ultrices. Phasellus id sapien in sapien iaculis congue. Vivamus metus arcu, adipiscing molestie, hendrerit at, vulputate vitae, nisl.

Aenean lectus. Pellentesque eget nunc. Donec quis orci eget orci vehicula condimentum.

Curabitur in libero ut massa volutpat convallis. Morbi odio odio, elementum eu, interdum eu, tincidunt in, leo. Maecenas pulvinar lobortis est.', 'Fisioterapia', 'Bulacan', 'Ensino Médio', '2022/11/02', 'Presencial', 'Vale-transporte', 'Habilidade em trabalho em equipe', '09:30 - 18:30', 'Ceará (CE)', 'Meio período', '60000-000', 'Rua das Flores', 55, 'Pinheiros');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Designer Gráfico', 'Duis consequat dui nec nisi volutpat eleifend. Donec ut dolor. Morbi vel lectus in quam fringilla rhoncus.

Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.

Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.', 'Arquitetura de Interiores', 'Gaowu', 'Ensino Técnico', '2023/05/01', 'Presencial', 'Seguro de vida', 'Habilidade em trabalho em equipe', '07:15 - 16:15', 'Sergipe (SE)', 'Tempo integral', '70000-000', 'Avenida Sete de Setembro', 56, 'Copacabana');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Gerente de Vendas', 'Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.

In quis justo. Maecenas rhoncus aliquam lacus. Morbi quis tortor id nulla ultrices aliquet.

Maecenas leo odio, condimentum id, luctus nec, molestie sed, justo. Pellentesque viverra pede ac diam. Cras pellentesque volutpat dui.', 'Tecnologia da Informação', 'Ropa', 'Ensino Técnico', '2023/05/21', 'Presencial', 'Vale-transporte', 'Formação em Administração', '08:10 - 17:10', 'Maranhão (MA)', 'Meio período', '80000-000', 'Rua dos Andradas', 57, 'Meireles');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'CLT', 'Coordenador de Projetos', 'Maecenas leo odio, condimentum id, luctus nec, molestie sed, justo. Pellentesque viverra pede ac diam. Cras pellentesque volutpat dui.

Maecenas tristique, est et tempus semper, est quam pharetra magna, ac consequat metus sapien ut nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris viverra diam vitae quam. Suspendisse potenti.

Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris.', 'Jornalismo', 'Xiang Ngeun', 'Ensino Médio', '2023/03/06', 'Remoto', 'Participação nos lucros', 'Formação em Administração', '07:45 - 16:45', 'Paraná (PR)', 'Tempo integral', '90000-000', 'Avenida Beira-Mar', 58, 'Boa Vista');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Consultor Financeiro', 'Mauris enim leo, rhoncus sed, vestibulum sit amet, cursus id, turpis. Integer aliquet, massa id lobortis convallis, tortor risus dapibus augue, vel accumsan tellus nisi eu orci. Mauris lacinia sapien quis libero.

Nullam sit amet turpis elementum ligula vehicula consequat. Morbi a ipsum. Integer a nibh.

In quis justo. Maecenas rhoncus aliquam lacus. Morbi quis tortor id nulla ultrices aliquet.', 'Educação Infantil', 'Nanam', 'Ensino Técnico', '2020/06/24', 'Presencial', 'Plano de saúde', 'Formação em Administração', '09:20 - 18:20', 'Maranhão (MA)', 'Tempo integral', '50000-000', 'Rua da Aurora', 59, 'Barra');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Coordenador de Projetos', 'Sed ante. Vivamus tortor. Duis mattis egestas metus.

Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.', 'Moda e Estilo', 'Mariatana', 'Ensino Superior', '2024/08/16', 'Presencial', 'Vale-refeição', 'Boa comunicação oral e escrita', '08:15 - 17:15', 'Rio Grande do Sul (RS)', 'Tempo integral', '65000-000', 'Avenida Rio Branco', 60, 'Batel');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Jovem Aprendiz', 'Analista de Marketing Digital', 'Praesent id massa id nisl venenatis lacinia. Aenean sit amet justo. Morbi ut odio.', 'Engenharia de Software', 'Gasa', 'Ensino Técnico', '2022/12/01', 'Remoto', 'Plano de saúde', 'Disponibilidade de horário', '07:00 - 16:00', 'Espírito Santo (ES)', 'Tempo integral', '78000-000', 'Rua Oscar Freire', 61, 'Savassi');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ('CLT', 'Analista de Marketing Digital', 'Aliquam quis turpis eget elit sodales scelerisque. Mauris sit amet eros. Suspendisse accumsan tortor quis turpis.

Sed ante. Vivamus tortor. Duis mattis egestas metus.

Aenean fermentum. Donec ut mauris eget massa tempor convallis. Nulla neque libero, convallis eget, eleifend luctus, ultricies eu, nibh.', 'Design Gráfico', 'Singa', 'Ensino Técnico', '2023/07/02', 'Remoto', 'Plano de saúde', 'Habilidade em trabalho em equipe', '10:20 - 19:20', 'Pernambuco (PE)', 'Meio período', '69000-000', 'Avenida Afonso Pena', 62, 'Liberdade');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'PJ', 'Coordenador de Projetos', 'Maecenas tristique, est et tempus semper, est quam pharetra magna, ac consequat metus sapien ut nunc. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Mauris viverra diam vitae quam. Suspendisse potenti.

Nullam porttitor lacus at turpis. Donec posuere metus vitae ipsum. Aliquam non mauris.

Morbi non lectus. Aliquam sit amet diam in magna bibendum imperdiet. Nullam orci pede, venenatis non, sodales sed, tincidunt eu, felis.', 'Engenharia de Software', 'Haugesund', 'Ensino Médio', '2021/01/31', 'Presencial', 'Flexibilidade de horário', 'Conhecimento em informática', '07:20 - 16:20', 'Ceará (CE)', 'Tempo integral', '66000-000', 'Rua dos Pinheiros', 63, 'Moema');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ( 'Estágio', 'Consultor Financeiro', 'Nam ultrices, libero non mattis pulvinar, nulla pede ullamcorper augue, a suscipit nulla elit ac nulla. Sed vel enim sit amet nunc viverra dapibus. Nulla suscipit ligula in lacus.', 'Tecnologia da Informação', 'Oliveira do Conde', 'Ensino Médio', '2024/12/21', 'Presencial', 'Participação nos lucros', 'Conhecimento em marketing digital', '09:15 - 18:15', 'Rio Grande do Sul (RS)', 'Meio período', '77000-000', 'Avenida Atlântica', 64, 'Ipanema');
insert into Tb_Anuncios (Categoria, Titulo, Descricao, Area, Cidade, Nivel_Operacional, Data_de_Criacao, Modalidade, Beneficios, Requisitos, Horario, Estado, Jornada, CEP, Rua, Numero, Bairro) values ('CLT', 'Coordenador de Projetos', 'Proin interdum mauris non ligula pellentesque ultrices. Phasellus id sapien in sapien iaculis congue. Vivamus metus arcu, adipiscing molestie, hendrerit at, vulputate vitae, nisl.

Aenean lectus. Pellentesque eget nunc. Donec quis orci eget orci vehicula condimentum.', 'Psicologia Clínica', 'Pomahuaca', 'Ensino Técnico', '2020/05/15', 'Remoto', 'Vale-refeição', 'Formação em Administração', '07:15 - 16:15', 'Amazonas (AM)', 'Tempo integral', '57000-000', 'Rua Barão de Itapetininga', 65, 'Leblon');

INSERT INTO `SIAS`.`Tb_Avaliacoes` (`Tb_Pessoas_Id`, `Nota`, `Texto`)
SELECT 
    FLOOR(1 + RAND() * 50) AS `Tb_Pessoas_Id`,  -- IDs aleatórios de 1 a 50
    FLOOR(1 + RAND() * 5) AS `Nota`,  -- Nota aleatória de 1 a 5
    LEFT(CONCAT(
        'Esta é uma avaliação de exemplo. ',
        'O texto desta avaliação é gerado aleatoriamente para fins de teste. ',
        'A quantidade de caracteres é limitada a 150. ',
        'Espero que esta avaliação esteja útil para você. ',
        'Tenha um ótimo dia! '
    ), 100) AS `Texto`  -- Texto aleatório limitado a 150 caracteres
FROM 
    -- Utiliza uma tabela auxiliar para gerar múltiplas linhas
    (SELECT 1 AS `dummy`) AS `d`
CROSS JOIN
    -- Uma tabela com números de 1 a 100 (você pode ajustar o número de acordo com a quantidade de avaliações que deseja gerar)
    (SELECT a.N + b.N * 10 + 1 AS `N` 
    FROM (SELECT 0 AS `N` UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS a
    CROSS JOIN (SELECT 0 AS `N` UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS b
    ) AS `numbers`
LIMIT 50;

-- Variável de controle para o ID de anúncio inicial
SET @next_anuncio_id = 65;

-- Inserir 10 registros na tabela `Tb_Vagas`
-- Inserir 10 registros na tabela `Tb_Vagas`
INSERT INTO `SIAS`.`Tb_Vagas` (`Tb_Anuncios_Id`, `Tb_Empresa_CNPJ`, `Status`, `Data_de_Termino`)
SELECT 
    `Anuncios`.`Id_Anuncios`,  -- Usando IDs válidos da tabela `Tb_Anuncios`
    `Empresas`.`CNPJ`,         -- Vínculo com empresas aleatórias
    CASE WHEN ROW_NUMBER() OVER (ORDER BY RAND()) % 2 = 0 THEN 'Aberto' ELSE 'Encerrado' END AS `Status`, -- Alterna entre 'Aberto' e 'Encerrado'
    NOW() + INTERVAL FLOOR(RAND() * 365) DAY  -- Data de término aleatória entre 0 e 365 dias
FROM 
    `SIAS`.`Tb_Anuncios` AS `Anuncios`  -- Seleção aleatória de IDs de `Tb_Anuncios`
JOIN 
    `SIAS`.`Tb_Empresa` AS `Empresas`  -- Seleção aleatória de CNPJs de `Tb_Empresa`
ORDER BY 
    RAND()  -- Ordenação aleatória para aleatorizar a seleção
LIMIT 50;  -- Inserir 10 registros




-- Inserir 25 inscrições aleatórias na tabela `Tb_Inscricoes`
INSERT INTO `SIAS`.`Tb_Inscricoes`
(`Tb_Vagas_Tb_Anuncios_Id`, `Tb_Vagas_Tb_Empresa_CNPJ`, `Tb_Candidato_CPF`, `Data_de_Inscricao`)
SELECT
  `Vagas`.`Tb_Anuncios_Id`,  -- Id do anúncio da vaga
  `Vagas`.`Tb_Empresa_CNPJ`, -- CNPJ da empresa relacionada à vaga
  `Candidato`.`CPF`,         -- CPF do candidato inscrito
  NOW()                      -- Data da inscrição
FROM
  -- Selecionar 31 vagas aleatórias
  (SELECT `Tb_Anuncios_Id`, `Tb_Empresa_CNPJ` 
   FROM `SIAS`.`Tb_Vagas` 
   ORDER BY RAND() 
   LIMIT 31) AS `Vagas`,
  -- Selecionar 13 candidatos aleatórios
  (SELECT `CPF` 
   FROM `SIAS`.`Tb_Candidato` 
   ORDER BY RAND() 
   LIMIT 13) AS `Candidato`
ORDER BY RAND() -- Ordem aleatória para distribuir as inscrições
LIMIT 45;       -- Inserir 25 inscrições

-- Inserir 10 registros na tabela `Tb_Questionarios` com imagens
INSERT INTO `SIAS`.`Tb_Questionarios` (`Nome`, `Area`, `DataQuestionario`, `Nivel`, `Tempo`, `Descricao`, `ImagemQuestionario`)
VALUES
('Questionário de TI', 'Tecnologia da Informação', CURDATE(), 'Básico', '30', 'Avaliação de conhecimentos em TI.', '../../assets/ImagesTestes/Tecnologia.jpg'),
('Questionário de Marketing', 'Marketing', CURDATE(), 'Intermediário', '40', 'Avaliação de conhecimentos em Marketing.', '../../assets/ImagesTestes/imagem_marketing.png'),
('Questionário de Finanças', 'Finanças', CURDATE(), 'Intermediário', '40', 'Avaliação de conhecimentos em Finanças.', '../../assets/ImagesTestes/imagem_financas.png'),
('Questionário de Recursos Humanos', 'Recursos Humanos', CURDATE(), 'Experiente', '50', 'Avaliação de conhecimentos em RH.', '../../assets/ImagesTestes/imagem_rh.png'),
('Questionário de Gestão de Projetos', 'Gestão de Projetos', CURDATE(), 'Intermediário', '45', 'Avaliação de conhecimentos em Gestão de Projetos.', '../../assets/ImagesTestes/imagem_gestao_projetos.png'),
('Questionário de Liderança', 'Liderança', CURDATE(), 'Experiente', '50', 'Avaliação de conhecimentos em Liderança.', '../../assets/ImagesTestes/imagem_lideranca.png'),
('Questionário de Ética Profissional', 'Ética Profissional', CURDATE(), 'Básico', '30', 'Avaliação de conhecimentos em Ética Profissional.', '../../assets/ImagesTestes/imagem_etica.png'),
('Questionário de Comunicação', 'Comunicação', CURDATE(), 'Intermediário', '40', 'Avaliação de conhecimentos em Comunicação.', '../../assets/ImagesTestes/imagem_comunicacao.png'),
('Questionário de Logística', 'Logística', CURDATE(), 'Básico', '35', 'Avaliação de conhecimentos em Logística.', '../../assets/ImagesTestes/imagem_logistica.png'),
('Questionário de Vendas', 'Vendas', CURDATE(), 'Experiente', '50', 'Avaliação de conhecimentos em Vendas.', '../../assets/ImagesTestes/imagem_vendas.png');

-- Recuperar os Ids dos questionários recém-inseridos
SET @id_questionario_ti = (SELECT `Id_Questionario` FROM `SIAS`.`Tb_Questionarios` WHERE `Nome` = 'Questionário de TI');
SET @id_questionario_marketing = (SELECT `Id_Questionario` FROM `SIAS`.`Tb_Questionarios` WHERE `Nome` = 'Questionário de Marketing');

-- Populando a tabela Tb_Questoes
INSERT INTO `SIAS`.`Tb_Questoes` (`Enunciado`, `Area`, `Id_Questionario`)
VALUES
('O que é um banco de dados relacional?', 'TI', @id_questionario_ti),
('Explique o conceito de marketing digital.', 'Marketing', @id_questionario_marketing),
('Quais são os principais comandos SQL?', 'TI', @id_questionario_ti),
('Quais são os pilares do marketing tradicional?', 'Marketing', @id_questionario_marketing);

-- Recuperar os Ids das questões recém-inseridas
SET @id_questao_1 = (SELECT `Id_Questao` FROM `SIAS`.`Tb_Questoes` WHERE `Enunciado` = 'O que é um banco de dados relacional?');
SET @id_questao_2 = (SELECT `Id_Questao` FROM `SIAS`.`Tb_Questoes` WHERE `Enunciado` = 'Explique o conceito de marketing digital.');
SET @id_questao_3 = (SELECT `Id_Questao` FROM `SIAS`.`Tb_Questoes` WHERE `Enunciado` = 'Quais são os principais comandos SQL?');
SET @id_questao_4 = (SELECT `Id_Questao` FROM `SIAS`.`Tb_Questoes` WHERE `Enunciado` = 'Quais são os pilares do marketing tradicional?');

-- Populando a tabela Tb_Alternativas
INSERT INTO `SIAS`.`Tb_Alternativas` (`Texto`, `Correta`, `Tb_Questoes_Id_Questao`)
VALUES
('Um banco de dados que utiliza tabelas para armazenar dados.', true, @id_questao_1),
('Uma ferramenta para gestão de projetos.', false, @id_questao_1),
('Uma técnica de desenvolvimento ágil.', false, @id_questao_1),
('Promoção de produtos por meio de canais digitais.', true, @id_questao_2),
('Gestão de equipes de vendas.', false, @id_questao_2),
('Estratégia de fidelização de clientes.', false, @id_questao_2),
('SELECT, INSERT, UPDATE, DELETE', true, @id_questao_3),
('JOIN, GROUP BY, ORDER BY', true, @id_questao_3),
('Python, Java, C++', false, @id_questao_3),
('Produto, Preço, Praça, Promoção', true, @id_questao_4),
('Planejamento, Execução, Monitoramento', false, @id_questao_4),
('Segmentação, Targeting, Posicionamento', false, @id_questao_4);

-- Populando a tabela Tb_Questionario_Questoes
INSERT INTO `SIAS`.`Tb_Questionario_Questoes` (`Id_Questionario`, `Tb_Questoes_Id_Questao`)
VALUES
(@id_questionario_ti, @id_questao_1),
(@id_questionario_ti, @id_questao_3),
(@id_questionario_marketing, @id_questao_2),
(@id_questionario_marketing, @id_questao_4);

-- Verifica e apaga o procedimento caso ele já exista
DROP PROCEDURE IF EXISTS insert_empresas_questionarios;

-- Criar procedimento armazenado para inserir registros na tabela Tb_Empresa_Questionario para cada questionário de 1 a 10
DELIMITER //
CREATE PROCEDURE insert_empresas_questionarios()
BEGIN
    DECLARE id_questionario INT DEFAULT 1;
    
    WHILE id_questionario <= 10 DO
        -- Inserir registros na tabela Tb_Empresa_Questionario
        INSERT INTO `SIAS`.`Tb_Empresa_Questionario` (`Id_Empresa`, `Id_Questionario`)
        SELECT `CNPJ`, id_questionario
        FROM `SIAS`.`Tb_Empresa`
        ORDER BY RAND()
        LIMIT 5;
        
        -- Incrementar o valor do id_questionario
        SET id_questionario = id_questionario + 1;
    END WHILE;
END //
DELIMITER ;

-- Executar o procedimento armazenado
CALL insert_empresas_questionarios();
