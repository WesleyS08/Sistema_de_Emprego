# SIAS (Sistema de Inovação e Avanço Socioeconômico)

Este projeto foi desenvolvido como parte da disciplina de Laboratório Web da faculdade, com o objetivo de criar um sistema de empregos baseado nos méritos dos candidatos às vagas, visando a imparcialidade em relação a idade, sexo, localização, entre outros.

## Equipe de Desenvolvimento

Este projeto está sendo desenvolvido por uma equipe dedicada de estudantes da faculdade. Cada membro desempenha um papel crucial no desenvolvimento e contribui para o sucesso do SIAS.

- **[DAVI DE BRITO JUNIOR:](https://github.com/DaveBrito)** Desenvolvedor Back-end
- **[ERIC PENDERES CARNEIRO:](https://github.com/EricW900)** Desenvolvedor Back-end
- **[PEDRO BORGE DE JESUS:](https://github.com/B0rga)** Desenvolvedor Front-end
- **[JEFFERSON MOREIRA EVANGELISTA:](https://github.com/JeffersonEvangelista)** Designer UX/UI, Desenvolvedor Front-end
- **[WESLEY SILVA DOS SANTOS:](https://github.com/WesleyS08)** Designer UX/UI, Desenvolvedor Back-end

Agradecemos a todos os membros da equipe por sua dedicação e esforço em tornar o SIAS uma realidade.

## Tecnologias Utilizadas 

- HTML 5
- CSS 3
- PHP 8
- Javascript
- MySQL
- APIs

## Figma do Projeto 
Explore o design do projeto no Figma [aqui](https://www.figma.com/file/QxHUCHfEumTYtu1RjLUTRf/SIAS?type=design&node-id=0%3A1&mode=design&t=LC4dzX5s7Ux52FZI-1).

## Estrutura de Pastas 

 Estrutura de Pastas:

 ```
| -- docs/
|   | -- word/
|   | -- excel/
|   | -- Modelos do banco de dados/
 |-- public/
|   |-- index.html
|   |-- manifest.json
|   |-- sw.js
|-- src/
|   |-- assets/
|   |   |-- images/
|   |   |-- styles/
|   |-- components/
|   |-- services/
|   |   |-- auth/
|   |   |   |-- processar_login.php
|   |   |-- api.js
|   |-- views/
|   |-- App.js
|   |-- index.js
|-- .gitignore
|-- README.md
 ```
## Descrição das Pastas Principais:

- public/: Contém arquivos estáticos públicos.

- - index.html: Página principal HTML do aplicativo.
- - manifest.json: Arquivo de manifesto da web.
- - sw.js: Arquivo do Service Worker.

- src/: Contém o código-fonte do aplicativo.

- - assets/: Arquivos estáticos como imagens e estilos.
- - components/: Componentes reutilizáveis.
- - services/: Módulos para interações com serviços, como API.

- - views/: Componentes específicos de visualização.
- - App.js: Componente principal.

-  .gitignore: Lista de arquivos e pastas ignorados pelo Git.

## Por que essa Arquitetura?

A escolha desta arquitetura segue princípios importantes para o desenvolvimento de Progressive Web Apps:

## 1 Separation of Concerns (Separação de Responsabilidades):

- A estrutura divide claramente as responsabilidades entre diferentes diretórios. O código-fonte, recursos estáticos e arquivos públicos são organizados de maneira lógica e fácil de entender.

## 2 Service Worker para Funcionalidade Offline:

- A presença do arquivo sw.js destaca a importância do Service Worker para possibilitar funcionalidades offline, caching e atualizações automáticas.

## 3 Componentização para Reutilização e Manutenção:

- A organização em components/ facilita a reutilização de elementos de interface do usuário, promovendo uma manutenção mais eficiente e um código mais limpo.

## 4 Centralização de Serviços em services/:

- O diretório services/ concentra módulos relacionados a serviços, como interações com APIs. Isso promove uma arquitetura mais escalável e fácil de testar.

## 5 Configurações Padrão para Navegadores:

- A presença do arquivo manifest.json destaca a intenção de criar uma aplicação web progressiva, com configurações para ícones, cores e outros detalhes de apresentação.

Adotar esta arquitetura busca equilibrar eficiência de desenvolvimento, manutenção e a conformidade com as melhores práticas para PWAs, proporcionando uma base sólida para a expansão e evolução do projeto ao longo do tempo.

## Como Contribuir
Sinta-se à vontade para contribuir para o desenvolvimento do SIAS! Você pode clonar o repositório, abrir issues ou enviar pull requests. Certifique-se de seguir as diretrizes de contribuição.

## Instalação e Configuração
Para configurar e executar o projeto localmente, siga estas etapas:

1. Clone o repositório: `git clone https://github.com/seu-usuario/sias.git`
2. Navegue até o diretório do projeto
3. Configure o ambiente (certifique-se de ter as tecnologias mencionadas instaladas)
4. Execute o aplicativo

## Documentação
A documentação completa do projeto está disponível em [docs/](docs/word). Certifique-se de verificar para obter informações detalhadas sobre a arquitetura, API e outros aspectos do SIAS.
