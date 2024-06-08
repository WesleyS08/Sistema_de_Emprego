# SIAS (Sistema de Inovação e Avanço Socioeconômico)

O SIAS é um sistema web inovador projetado para transformar a forma como as oportunidades de emprego são oferecidas. Nosso foco principal é a criação de um sistema de seleção baseado em mérito, onde os candidatos são avaliados apenas pelos resultados de testes técnicos. Isso garante uma seleção imparcial, sem considerar fatores como idade, gênero ou outras características pessoais.

Além disso, o SIAS promove o desenvolvimento contínuo dos usuários ao oferecer sugestões personalizadas de cursos ou os cursos mais populares para os visitantes, ajudando-os a aprimorar suas habilidades e aumentar suas chances de sucesso no mercado de trabalho

## Tecnologias Utilizadas

O desenvolvimento do SIAS envolve várias tecnologias essenciais que garantem seu funcionamento robusto e eficiente. Essas tecnologias incluem linguagens de marcação e estilo para construir a interface do usuário, bem como linguagens de programação e bancos de dados para gerenciar a lógica do servidor e o armazenamento de dados. Ferramentas adicionais são usadas para facilitar o desenvolvimento, teste e etc.

| Tecnologia    | Descrição                                                           | Uso no Projeto                                                |
|---------------|---------------------------------------------------------------------|---------------------------------------------------------------|
| **HTML5**     | Linguagem de marcação padrão para a criação de páginas web.         | Estrutura básica das páginas do SIAS.                         |
| **CSS3**      | Linguagem de estilo para a apresentação de documentos HTML.         | Estilização e layout da interface do usuário.                 |
| **JavaScript** | Linguagem de programação amplamente usada para desenvolvimento web. | Utilizada no front-end para adicionar interatividade e dinamismo às páginas web. |
| **PHP**       | Linguagem de script de servidor usada principalmente para desenvolvimento web. | Utilizada para o back-end, gerenciando a lógica do servidor e a interação com o banco de dados. |
| **MySQL**     | Sistema de gerenciamento de banco de dados relacional.              | Armazenamento estruturado de dados do sistema, como informações de usuários e resultados de testes. |
| **XAMPP**     | Plataforma de servidor que inclui Apache, MySQL, PHP e Perl.        | Facilita o desenvolvimento local ao fornecer um ambiente de servidor completo. |
| **Figma**     | Ferramenta de design colaborativo para a criação de interfaces de usuário. | Utilizada para o design e prototipagem das interfaces do SIAS. |
| **Microsoft Word** | Aplicativo de processamento de texto da Microsoft.            | Utilizado para documentar e gerar relatórios textuais relacionados ao projeto. |


## APIs Integradas

Para oferecer funcionalidades avançadas e serviços adicionais, o SIAS integra várias APIs externas. Essas APIs fornecem suporte para autenticação, geolocalização e outros serviços que complementam a experiência do usuário e melhoram a eficiência do sistema.

| API                            | Descrição                                                         | Uso no Projeto                                               |
|--------------------------------|-------------------------------------------------------------------|--------------------------------------------------------------|
| **Google**                     | API que fornece serviços de autenticação e autorização.           | Permite login seguro e controle de acesso dentro do SIAS.    |
| **ViaCEP**                     | API brasileira para consulta de CEPs.                             | Facilita a obtenção de endereços baseados no CEP dos usuários. |
| **IBGE**                       | API que oferece dados estatísticos e demográficos do Brasil.      | Utilizada para obter informações demográficas que dão validação a cidades digitadas no sistema. |
| **TomTom**                     | API de mapas e geolocalização.                                    | Oferece funcionalidades de mapeamento e localização dentro do SIAS. |
| **OpenStreetMap**              | API de mapas que fornece dados de localização geográfica.         | Utilizada para visualizar e integrar mapas e dados de localização nas interfaces do sistema. |

## Equipe de Desenvolvimento

Este projeto está sendo desenvolvido por uma equipe dedicada de estudantes da faculdade. Cada membro desempenha um papel crucial no desenvolvimento e contribui para o sucesso do SIAS.

- **[DAVI DE BRITO JUNIOR:](https://github.com/DaveBrito)** Desenvolvedor Back-end
- **[ERIC PENERES CARNEIRO:](https://github.com/EricW900)** Desenvolvedor Back-end
- **[PEDRO BORGES DE JESUS:](https://github.com/B0rga)** Desenvolvedor Front-end
- **[JEFFERSON MOREIRA EVANGELISTA:](https://github.com/JeffersonEvangelista)** Designer UX/UI, Desenvolvedor Front-end
- **[WESLEY SILVA DOS SANTOS:](https://github.com/WesleyS08)** Designer UX/UI, Desenvolvedor Back-end

Gostaria de agradecer a todos os membros da equipe do SIAS por sua dedicação e esforço extraordinário. Cada um de vocês desempenhou um papel crucial na realização deste projeto.

Agradeço imensamente a todos vocês por sua paixão, compromisso e trabalho árduo. Juntos, vocês tornaram o SIAS uma plataforma inovadora e eficaz, pronta para transformar a forma como as oportunidades de emprego são oferecidas e acessadas. Este sucesso é fruto do esforço coletivo e da excelência de cada membro da equipe.

Obrigado por sua colaboração e por tornarem este projeto uma realidade!


## Figma do Projeto 

Explore nosso projeto no Figma para visualizar o design da plataforma e junte-se a nós nesta jornada de inovação e avanço socioeconômico!

Explore o design do projeto no Figma [aqui](https://www.figma.com/file/QxHUCHfEumTYtu1RjLUTRf/SIAS?type=design&node-id=0%3A1&mode=design&t=LC4dzX5s7Ux52FZI-1).

![Design do Projeto SIAS](/imagens/figma.png)

## Estrutura de Pastas 

O projeto SIAS apresenta uma estrutura de páginas cuidadosamente projetada para fornecer uma experiência de usuário intuitiva e eficiente. Cada página foi desenvolvida com o objetivo de facilitar a navegação dos usuários e fornecer acesso rápido às informações essenciais. Abaixo, destacamos a estrutura do projeto:

```
docs/
|---- apresentação/
|    |---- SIAS.pptx

|---- excel/
|    |---- orcamento_sias.xlsx

|---- Modelos do banco de dados/
|    |---- inserts.sql
|    |---- populate_sias.sql
|    |---- Script_de_criação.sql
|    |---- SIAS.brM3
|    |---- SIAS.mwb
|    |---- SIAS.mwb.bak
|    |---- SIAS.png

|---- UML/
|    |---- SIAS diagrama de caso de uso.asta

|---- word/
|    |---- DWS-FZS-012024-EMPRESA-SIAS-V9.docx


imagens/
|---- ...

src/
|---- assets/
|    |---- banners/
|    |      |---- ...

|    |---- imagensPerfil/
|    |      |---- ...


|    |---- images/
|    |    |---- figuras/
|    |    |    |---- ...

|    |    |---- icones_diversos/
|    |    |    |---- ...

|    |    |---- icones_regimes/
|    |    |    |---- ...

|    |    |---- icones_vaga/
|    |    |    |---- ...

|    |    |---- logos_cursos/
|    |    |    |---- ...

|    |    |---- logos_empresa/
|    |    |    |---- ...

|    |    |---- logos_parceiros/
|    |    |    |---- ...

|---- styles/
|   |    |---- ...

|---- components/
|   |    |---- ...

|---- PHPMailer-master/
|   |---- ...

|---- language/
|   |---- ...

mailer.lang-fi.php
|   |---- ...

|---- services/
|    |---- conexão_com_banco.php
|    |---- criar_banco.php
|    |---- google_credentials.php
|    |---- Obter_cursos.php

|    |---- auth/
|    |    |---- exempro_de_senssion.php
|    |    |---- processar_login.php
|    |    |---- processar_login_google.php
|    |    |---- verificarRecrutador.php
|    |    |---- verificarUsuario.php

|    |---- Avaliaçao/
|    |    |---- Avaliaçao.php

|    |---- cadastros/
|    |    |---- cadastroRecrutador.php
|    |    |---- cadastroUsuario.php
|    |    |---- encvio_Recrutador.php
|    |    |---- encvio_Usuario.php
|    |    |---- processar_candidatura.php
|    |    |---- processar_retirada_candidatura.php
|    |    |---- vaga.php

|    |---- CandidatarVaga/
|    |    |---- candidatarVaga.php

|    |---- deletar/
|    |    |---- DeletarVaga.php

|    |---- Edição/
|    |    |---- vagas.php

|    |---- ExcluirConta/
|    |    |---- excluirContaCandidato.php
|    |    |---- excluirContaEmpresa.php

|    |---- Perfil/
|    |    |---- PerfilCandidato.php
|    |    |---- PerfilEmpresa.php

|    |---- Senhas/
|    |    |---- recuperasenhas.php

|    |---- Temas/
|    |    |---- atualizar_tema.php

|    |---- Testes/
|    |    |---- processarTeste.php
|    |    |---- processarQuestoes.php

|---- views/
|    |---- AvalieNos/
|    |    |---- adicionarEstrelas.js
|    |    |---- avalieNos.php
|    |    |---- hoverEstrelas.js
|    |    |---- verificar-palavras.php

|    |---- AvisoQuestionarioCriado/
|    |    |---- AvisoQuestionarioCriado.html

|    |---- AvisoVerificaEmail/
|    |    |---- avisoVerificaEmail.html

|    |---- Cadastro/
|    |    |---- cadastro.css
|    |    |---- cadastro.html
|    |    |---- mostrarSenha.js
|    |    |---- trocaDeForms.js
|    |    |---- verificar_cnpj.php
|    |    |---- Verificar_cpf.php
|    |    |---- verificar_email_recrutador.php
|    |    |---- verificar_email.php
|    |    |---- Web.gitattributes

|    |---- CriarTaga/
|    |    |---- adicionarQuestao.js
|    |    |---- criarTeste.php
|    |    |---- imagemTeste.js
|    |    |---- mostraIcone.js
|    |    |---- processarQuestoes.js
|    |    |---- salvar_imagem.php
|    |    |---- verificar-palavra.php
|    |    |---- verificar-palavras.php

|    |---- criarVaga/
|    |    |---- criarVaga.php
|    |    |---- criaVaga.css
|    |    |---- radioButtons.js
|    |    |---- verificar-palavra.php

|    |---- Cursos/
|    |    |---- buscar_cursos.php
|    |    |---- carrosselCertificacoes.js
|    |    |---- carrosselCursosGratuitos.js
|    |    |---- carrosselCursosPagos.js
|    |    |---- cursos.css
|    |    |---- cursos.php
|    |    |---- obter_sugestoes.php
|    |    |---- registrar_clique.php
|    |    |---- tituloDigitavel.js
|    |

---- EditarPerfilCandidato/
|    |    |---- adicionaElementos.js
|    |    |---- avisoInicial.js
|    |    |---- editarPerfilCandidato.php
|    |    |---- mascaras.js
|    |    |---- mostraIcone.js

|    |---- EditarPerfilRecrutador/
|    |    |---- adicionaElementos.js
|    |    |---- avisoInicial.js
|    |    |---- editarPerfilRecrutador.php
|    |    |---- mascaras.js
|    |    |---- mostraIcone.js

|    |---- EditarVagaRecrutador/
|    |    |---- editarvagaRecrutador.php
|    |    |---- radioButtons.js

|    |---- EmailVerificado/
|    |    |---- emailVerificado.php

|    |---- homeCandidato/
|    |    |---- carrosselMinhasVagas.js
|    |    |---- carrosselTestes.js
|    |    |---- carrosselUltimosAnuncios.js
|    |    |---- homeCandidato.php
|    |    |---- tituloDigitavel.js

|    |---- HomeRecrutador/
|    |    |---- carrosselAnuncios.js
|    |    |---- carrosselPerfis.js
|    |    |---- carrosselTestes.js
|    |    |---- homeRecrutador.php
|    |    |---- tituloDigitavel.js

|    |---- Login/
|    |    |---- avisos.js
|    |    |---- login.html
|    |    |---- mostrarSenha.js

|    |---- MeusTestes/
|    |    |---- buscar_questionario_filtros.php
|    |    |---- checkButtons.js
|    |    |---- meusTestes.php
|    |    |---- mostrarFiltros.js
|    |    |---- obter_sugestoes.php
|    |    |---- tituloDigitavel.js

|    |---- MinhasVagas/
|    |    |---- buscar_vagas_filtros.php
|    |    |---- buscar_vaga_por_titulo.php
|    |    |---- checkButtons.js
|    |    |---- minhasVagas.php
|    |    |---- mostrarFiltros.js
|    |    |---- obter_sugestoes.php
|    |    |---- tituloDigitavel.js

|    |---- MinhaVaga/
|    |    |---- carrosselPerfis.js
|    |    |---- minhaVaga.css
|    |    |---- minhaVaga.php
|    |    |---- trocaIcones.js

|    |---- NossoContado/
|    |    |---- nossoContado.html

|    |---- PaginaErro/
|    |    |---- paginaErro.html

|    |---- PerfilCandidato/
|    |    |---- acumuloDePontos.js
|    |    |---- perfilCandidato.php

|    |---- PerfilRecrutador/
|    |    |---- perfilRecrutador.php

|    |---- PoliticadePrivcidade/
|    |    |---- PoliticadePrivacidade.css
|    |    |---- PoliticadePrivacidade.php

|    |---- PreparaTeste/
|    |    |---- preparaTeste.css
|    |    |---- preparaTeste.php

|    |---- RecuperarSenha/
|    |    |---- recuperarSenha.html
|    |    |---- verificar_email.php

|    |---- RedefinirSenha/
|    |    |---- mostrarSenha.js
|    |    |---- redefinirSenha.php

|    |---- ResultadosQuestionario/
|    |    |---- paginaResultado.php

|    |---- Teste/
|    |    |---- contagemQuestoes.js
|    |    |---- cronometro.js
|    |    |---- finalizarTeste.js
|    |    |---- teste.css
|    |    |---- teste.php

|    |---- TodasVagas/
|    |    |---- buscar_vagas_filtros.php
|    |    |---- buscar_vaga_por_titulo.php
|    |    |---- checkButtons.js
|    |    |---- mostrarFiltros.js
|    |    |---- obter_sugestoes.php
|    |    |---- tituloDigitavel.js
|    |    |---- todasVagas.php

|    |---- TodosTestes/
|    |    |---- checkButtons.js
|    |    |---- mostrarFiltros.js
|    |    |---- tituloDigitavel.js
|    |    |---- todosTestes.php

|    |---- Vaga/
|    |    |---- trocaIcones.js
|    |    |---- vaga.css
|    |    |---- vaga.php

| -- .gitignore/
|-- carrossel.js
|-- index.php
|-- README.md
|-- tituloDigitavel.js
 ```


## Descrição das Pastas Principais:

- public/: Contém arquivos estáticos públicos.

- - index.php: Página principal PHP do sistema.


- src/: Contém o código-fonte do sistema.

- - assets/: Arquivos estáticos como imagens e estilos.
- - components/: Componentes reutilizáveis.
- - services/: Módulos para interações com serviços, como API.

- - views/: Componentes específicos de visualização.


-  .gitignore: Lista de arquivos e pastas ignorados pelo Git.

## Por que essa Arquitetura?

A escolha desta arquitetura segue princípios importantes para o desenvolvimento de Progressive Web Apps:

## 1 Separation of Concerns (Separação de Responsabilidades):

- A estrutura divide claramente as responsabilidades entre diferentes diretórios. O código-fonte, recursos estáticos e arquivos públicos são organizados de maneira lógica e fácil de entender.


## 2 Componentização para Reutilização e Manutenção:

- A organização em components/ facilita a reutilização de elementos de interface do usuário, promovendo uma manutenção mais eficiente e um código mais limpo.

## 3 Centralização de Serviços em services/:

- O diretório services/ concentra módulos relacionados a serviços, como interações com APIs. Isso promove uma arquitetura mais escalável e fácil de testar.

## 4 Configurações Padrão para Navegadores:

- A presença do arquivo manifest.json destaca a intenção de criar uma aplicação web progressiva, com configurações para ícones, cores e outros detalhes de apresentação.

Adotar esta arquitetura busca equilibrar eficiência de desenvolvimento, manutenção e a conformidade com as melhores práticas para PWAs, proporcionando uma base sólida para a expansão e evolução do projeto ao longo do tempo.


## Como Contribuir 
Sinta-se à vontade para contribuir para o desenvolvimento do SIAS! Você pode clonar o repositório, abrir issues ou enviar pull requests.


## Instalação e Configuração
Para configurar e executar o projeto localmente, siga estas etapas:

1. Clone o repositório: `git clone https://github.com/WesleyS08/Sistema_de_Emprego.git`
2. Navegue até o diretório do projeto
3. Instale as dependências do projeto: `npm install`
4. Execute o aplicativo: `npm start`

## Documentação
A documentação completa do projeto está disponível em [docs/](https://github.com/WesleyS08/Sistema_de_Emprego/tree/main/docs). Certifique-se de verificar para obter informações detalhadas sobre a arquitetura, API e outros aspectos do SIAS.
