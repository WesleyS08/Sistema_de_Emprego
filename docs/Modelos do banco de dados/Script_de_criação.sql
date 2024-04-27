
-- MySQL Script generated by MySQL Workbench
-- Thu Mar 21 17:08:25 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=`ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION`;

-- -----------------------------------------------------
-- Schema SIAS
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema SIAS
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `SIAS` DEFAULT CHARACTER SET utf8 ;
USE `SIAS` ;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Pessoas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Pessoas` (
  `Id_Pessoas` INT NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(45) NULL,
  `Senha` VARCHAR(255) NULL,
  `Nome` VARCHAR(45) NULL,
  `Token` TEXT NULL,
  `Verificado` BLOB NULL,
  PRIMARY KEY (`Id_Pessoas`)
)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Candidato`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Candidato` (
  `CPF` VARCHAR(11) NOT NULL,
  `Tb_Pessoas_Id` INT NOT NULL AUTO_INCREMENT,
  `Area_de_Interesse` VARCHAR(50) NULL,
  `Tipo_de_Contratacao` VARCHAR(45) NULL,
  `Descricao` VARCHAR(255) NULL,  
  `Experiencia` VARCHAR(255) NULL,
  `Motivacoes` VARCHAR(255) NULL,
  `Cursos` VARCHAR(255) NULL,
  `Escolaridade` VARCHAR(255) NULL,
  `Genero` VARCHAR(45) NULL,
  `Estado_Civil` VARCHAR(45) NULL,
  `Idade` INT(2) NULL,
  `Autodefinicao` VARCHAR(255) NULL,
  `Telefone` VARCHAR(15) NULL,  
  `Data_Nascimento` DATE NULL,
  `Cidade` VARCHAR(255) NULL,
  `PCD` BOOLEAN NULL,
  `Img_Perfil` VARCHAR(255) NULL,
  `Banner` VARCHAR(255) NULL,
  PRIMARY KEY (`CPF`),
  INDEX `fk_Tb_Candidato_Tb_Pessoas_idx` (`Tb_Pessoas_Id` ASC),
  CONSTRAINT `fk_Tb_Candidato_Tb_Pessoas`
    FOREIGN KEY (`Tb_Pessoas_Id`)
    REFERENCES `SIAS`.`Tb_Pessoas` (`Id_Pessoas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Empresa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Empresa` (
  `CNPJ` VARCHAR(14) NOT NULL,
  `Tb_Pessoas_Id` INT NOT NULL,
  `Img_Banner` VARCHAR(255) NULL,
  `Area_de_Atuacao` VARCHAR(45) NULL,
  `Facebook` VARCHAR(45) NULL,
  `Github` VARCHAR(45) NULL,
  `Linkedin` VARCHAR(45) NULL,
  `Instagram` VARCHAR(45) NULL,
  `Nome_da_Empresa` VARCHAR(255) NULL,
  `Sobre_a_Empresa` VARCHAR(255) NULL,
  `Area_da_Empresa` VARCHAR(255) NULL,
  `Avaliacao_de_Funcionarios` VARCHAR(45) NULL,
  `Avaliacao_Geral` VARCHAR(45) NULL,
  `Telefone` VARCHAR(15) NULL,
  `Img_Perfil` VARCHAR(255) NULL,
  PRIMARY KEY (`CNPJ`),
  INDEX `fk_Tb_Empresa_Tb_Pessoas1_idx` (`Tb_Pessoas_Id` ASC),
  CONSTRAINT `fk_Tb_Empresa_Tb_Pessoas1`
    FOREIGN KEY (`Tb_Pessoas_Id`)
    REFERENCES `SIAS`.`Tb_Pessoas` (`Id_Pessoas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB;



-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Questionarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Questionarios` (
  `Id_Questionario` INT NOT NULL AUTO_INCREMENT,
  `Nome` VARCHAR(255) NOT NULL,
  `Area` VARCHAR(255) NULL,
  `Data` DATE NOT NULL,
  `Nivel` VARCHAR(55) NOT NULL,
  `Descricao` VARCHAR(255) NULL,
  PRIMARY KEY (`Id_Questionario`)
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Resultados`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Resultados` (
  `Id_Resultado` INT NOT NULL AUTO_INCREMENT,
  `Tb_Questionarios_ID` INT NOT NULL,
  `Tb_Candidato_CPF` VARCHAR(11) NOT NULL,
  `Pontuacao` INT NULL,
  `Quantidade_de_Acertos` INT NULL,
  `Data_do_Questionario` DATETIME NULL,
  `Numero_de_Tentativas` INT NULL,
  PRIMARY KEY (`Id_Resultado`),
  INDEX `fk_Tb_Resultados_Tb_Candidato1_idx` (`Tb_Candidato_CPF` ASC),
  INDEX `fk_Tb_Resultados_Tb_Questionarios1_idx` (`Tb_Questionarios_ID` ASC),
  CONSTRAINT `fk_Tb_Resultados_Tb_Candidato1`
    FOREIGN KEY (`Tb_Candidato_CPF`)
    REFERENCES `SIAS`.`Tb_Candidato` (`CPF`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tb_Resultados_Tb_Questionarios1`
    FOREIGN KEY (`Tb_Questionarios_ID`)
    REFERENCES `SIAS`.`Tb_Questionarios` (`Id_Questionario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Anuncios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Anuncios` (
  `Id_Anuncios` INT NOT NULL AUTO_INCREMENT,
  `Categoria` varchar(45) DEFAULT NULL,
  `Titulo` varchar(50) DEFAULT NULL,
  `Descricao` varchar(255) DEFAULT NULL,
  `Area` varchar(45) DEFAULT NULL,
  `Cidade` varchar(45) DEFAULT NULL,
  `Nivel_Operacional` varchar(45) DEFAULT NULL,
  `Data_de_Criacao` datetime DEFAULT NULL,
  `Modalidade` varchar(45) DEFAULT NULL,
  `Beneficios` varchar(255) DEFAULT NULL,
  `Requisitos` varchar(255) DEFAULT NULL,
  `Horario` varchar(45) NOT NULL,
  `Estado` varchar(45) NOT NULL,
  `Jornada` varchar(45) NOT NULL,
  `CEP`VARCHAR(9) NOT NULL,
  `Rua` varchar(45) NOT NULL,
  `Numero` TEXT NOT NULL,
  `Bairro` varchar(45) NOT NULL,
  PRIMARY KEY (`Id_Anuncios`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Vagas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Vagas` (
  `Tb_Anuncios_Id` INT NOT NULL,
  `Tb_Empresa_CNPJ` VARCHAR(14) NOT NULL,
  `Status` VARCHAR(45) NULL,
  `Data_de_Termino` DATETIME NULL,
  PRIMARY KEY (`Tb_Anuncios_Id`, `Tb_Empresa_CNPJ`),
  INDEX `fk_Tb_Anuncios_has_Tb_Empresa_Tb_Empresa1_idx` (`Tb_Empresa_CNPJ` ASC) ,
  INDEX `fk_Tb_Anuncios_has_Tb_Empresa_Tb_Anuncios1_idx` (`Tb_Anuncios_Id` ASC) ,
  CONSTRAINT `fk_Tb_Anuncios_has_Tb_Empresa_Tb_Anuncios1`
    FOREIGN KEY (`Tb_Anuncios_Id`)
    REFERENCES `SIAS`.`Tb_Anuncios` (`Id_Anuncios`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tb_Anuncios_has_Tb_Empresa_Tb_Empresa1`
    FOREIGN KEY (`Tb_Empresa_CNPJ`)
    REFERENCES `SIAS`.`Tb_Empresa` (`CNPJ`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Inscricoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Inscricoes` (
  `Tb_Vagas_Tb_Anuncios_Id` INT NOT NULL,
  `Tb_Vagas_Tb_Empresa_CNPJ` VARCHAR(14) NOT NULL,
  `Tb_Candidato_CPF` VARCHAR(11) NOT NULL,
  `Data_de_Inscricao` DATETIME NULL,
  PRIMARY KEY (`Tb_Vagas_Tb_Anuncios_Id`, `Tb_Vagas_Tb_Empresa_CNPJ`, `Tb_Candidato_CPF`),
  INDEX `fk_Tb_Vagas_has_Tb_Candidato_Tb_Candidato1_idx` (`Tb_Candidato_CPF` ASC) ,
  INDEX `fk_Tb_Vagas_has_Tb_Candidato_Tb_Vagas1_idx` (`Tb_Vagas_Tb_Anuncios_Id` ASC, `Tb_Vagas_Tb_Empresa_CNPJ` ASC) ,
  CONSTRAINT `fk_Tb_Vagas_has_Tb_Candidato_Tb_Vagas1`
    FOREIGN KEY (`Tb_Vagas_Tb_Anuncios_Id` , `Tb_Vagas_Tb_Empresa_CNPJ`)
    REFERENCES `SIAS`.`Tb_Vagas` (`Tb_Anuncios_Id` , `Tb_Empresa_CNPJ`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tb_Vagas_has_Tb_Candidato_Tb_Candidato1`
    FOREIGN KEY (`Tb_Candidato_CPF`)
    REFERENCES `SIAS`.`Tb_Candidato` (`CPF`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Cursos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Cursos` (
  `Id_Cursos` INT NOT NULL AUTO_INCREMENT,
  `Tema` VARCHAR(45) NULL,
  `Duracao` VARCHAR(45) NULL,
  `Certificado` VARCHAR(45) NULL,
  `Site` VARCHAR(45) NULL,
  `Gratuito` VARCHAR(45) NULL,
  `Titulo` VARCHAR(45) NULL,
  `Descricao` VARCHAR(255) NULL,
  PRIMARY KEY (`Id_Cursos`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Recomendacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Recomendacoes` (
  `Tb_Candidato_CPF` VARCHAR(11) NOT NULL,
  `Tb_Cursos_Id` INT NOT NULL,
  PRIMARY KEY (`Tb_Candidato_CPF`, `Tb_Cursos_Id`),
  INDEX `fk_Tb_Candidato_has_Tb_Cursos_Tb_Cursos1_idx` (`Tb_Cursos_Id` ASC) ,
  INDEX `fk_Tb_Candidato_has_Tb_Cursos_Tb_Candidato1_idx` (`Tb_Candidato_CPF` ASC) ,
  CONSTRAINT `fk_Tb_Candidato_has_Tb_Cursos_Tb_Candidato1`
    FOREIGN KEY (`Tb_Candidato_CPF`)
    REFERENCES `SIAS`.`Tb_Candidato` (`CPF`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Tb_Candidato_has_Tb_Cursos_Tb_Cursos1`
    FOREIGN KEY (`Tb_Cursos_Id`)
    REFERENCES `SIAS`.`Tb_Cursos` (`Id_Cursos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Questoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Questoes` (
    `Id_Questao` INT NOT NULL AUTO_INCREMENT,
    `Enunciado` TEXT NOT NULL,
    `Area` VARCHAR(255),
    `Id_Questionario` INT NOT NULL,
    PRIMARY KEY (`Id_Questao`),
    FOREIGN KEY (`Id_Questionario`) REFERENCES `Tb_Questionarios` (`Id_Questionario`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Alternativas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Alternativas` (
    `Id_Alternativa` INT NOT NULL AUTO_INCREMENT,
    `Texto` VARCHAR(255) NOT NULL,
    `Correta` BOOLEAN NOT NULL,
    `Tb_Questoes_Id_Questao` INT NOT NULL,
    PRIMARY KEY (`Id_Alternativa`),
    FOREIGN KEY (`Tb_Questoes_Id_Questao`) REFERENCES `Tb_Questoes` (`Id_Questao`) ON DELETE CASCADE
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Questionario_Questoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Questionario_Questoes` (
    `Id_Questionario_Questoes` INT NOT NULL AUTO_INCREMENT,
    `Id_Questionario` INT NOT NULL,
    `Tb_Questoes_Id_Questao` INT NOT NULL,
    PRIMARY KEY (`Id_Questionario_Questoes`),
    FOREIGN KEY (`Id_Questionario`) REFERENCES `Tb_Questionarios` (`Id_Questionario`) ON DELETE CASCADE,
    FOREIGN KEY (`Tb_Questoes_Id_Questao`) REFERENCES `Tb_Questoes` (`Id_Questao`) ON DELETE CASCADE
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `SIAS`.`Tb_Respostas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SIAS`.`Tb_Respostas` (
  `Id_Resposta` INT NOT NULL AUTO_INCREMENT,
  `Tb_Questoes_Id_Questao` INT NOT NULL,
  `Tb_Candidato_CPF` VARCHAR(11) NOT NULL,
  `Tb_Alternativas_Id_Alternativa` INT NOT NULL,
  PRIMARY KEY (`Id_Resposta`),
  FOREIGN KEY (`Tb_Questoes_Id_Questao`) REFERENCES `Tb_Questoes` (`Id_Questao`) ON DELETE CASCADE,
  FOREIGN KEY (`Tb_Candidato_CPF`) REFERENCES `Tb_Candidato` (`CPF`) ON DELETE CASCADE,
  FOREIGN KEY (`Tb_Alternativas_Id_Alternativa`) REFERENCES `Tb_Alternativas` (`Id_Alternativa`) ON DELETE CASCADE
)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
