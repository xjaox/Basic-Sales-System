-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tempo de Geração: Jan 12, 2011 as 04:03 AM
-- Versão do Servidor: 5.1.49
-- Versão do PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `projectname`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acessologin`
--

CREATE TABLE IF NOT EXISTS `acessologin` (
  `idAcessoLogin` int(5) NOT NULL AUTO_INCREMENT,
  `idLogin` int(5) DEFAULT NULL,
  `dataHoraEntradaAcessoLogin` datetime DEFAULT NULL,
  `dataHoraSaidaAcessoLogin` datetime DEFAULT NULL,
  `ipAcessoLoginSaida` varchar(15) DEFAULT NULL,
  `ipAcessoLoginEntrada` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idAcessoLogin`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Extraindo dados da tabela `acessologin`
--

INSERT INTO `acessologin` (`idAcessoLogin`, `idLogin`, `dataHoraEntradaAcessoLogin`, `dataHoraSaidaAcessoLogin`, `ipAcessoLoginSaida`, `ipAcessoLoginEntrada`) VALUES
(1, 1, '2011-01-11 22:37:00', NULL, NULL, '127.0.0.1'),
(2, 2, '2011-01-12 00:02:00', '2011-01-12 00:12:00', '127.0.0.1', '127.0.0.1'),
(3, 2, '2011-01-12 00:13:00', '2011-01-12 00:13:00', '127.0.0.1', '127.0.0.1'),
(4, 2, '2011-01-12 00:14:00', '2011-01-12 00:14:00', '127.0.0.1', '127.0.0.1'),
(5, 2, '2011-01-12 00:14:00', '2011-01-12 00:15:00', '127.0.0.1', '127.0.0.1'),
(6, 2, '2011-01-12 00:16:00', NULL, NULL, '127.0.0.1'),
(7, 3, '2011-01-12 00:19:00', NULL, NULL, '127.0.0.1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE IF NOT EXISTS `clientes` (
  `idClientes` int(5) NOT NULL AUTO_INCREMENT,
  `apelidoClientes` varchar(30) DEFAULT NULL,
  `nomeClientes` varchar(70) DEFAULT NULL,
  `enderecoClientes` varchar(100) DEFAULT NULL,
  `numeroClientes` int(5) DEFAULT NULL,
  `bairroClientes` varchar(30) DEFAULT NULL,
  `cepClientes` varchar(9) DEFAULT NULL,
  `cidadeClientes` varchar(30) DEFAULT NULL,
  `estadoClientes` varchar(2) DEFAULT NULL,
  `cpfClientes` varchar(14) DEFAULT NULL,
  `rgClientes` varchar(20) DEFAULT NULL,
  `dataNascimentoClientes` date DEFAULT NULL,
  `telefoneClientes` varchar(14) DEFAULT NULL,
  `emailClientes` varchar(100) DEFAULT NULL,
  `profissaoClientes` varchar(50) DEFAULT NULL,
  `dataCadastroClientes` datetime DEFAULT NULL,
  `statusClientes` int(1) DEFAULT NULL,
  PRIMARY KEY (`idClientes`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`idClientes`, `apelidoClientes`, `nomeClientes`, `enderecoClientes`, `numeroClientes`, `bairroClientes`, `cepClientes`, `cidadeClientes`, `estadoClientes`, `cpfClientes`, `rgClientes`, `dataNascimentoClientes`, `telefoneClientes`, `emailClientes`, `profissaoClientes`, `dataCadastroClientes`, `statusClientes`) VALUES
(1, 'Rd', 'Rodrigo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(2, 'Rf', 'Rafaela', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionarios`
--

CREATE TABLE IF NOT EXISTS `funcionarios` (
  `idFuncionarios` int(5) NOT NULL AUTO_INCREMENT,
  `apelidoFuncionarios` varchar(30) DEFAULT NULL,
  `nomeFuncionarios` varchar(70) DEFAULT NULL,
  `enderecoFuncionarios` varchar(100) DEFAULT NULL,
  `numeroFuncionarios` int(5) DEFAULT NULL,
  `bairroFuncionarios` varchar(30) DEFAULT NULL,
  `cepFuncionarios` varchar(9) DEFAULT NULL,
  `cidadeFuncionarios` varchar(30) DEFAULT NULL,
  `estadoFuncionarios` varchar(2) DEFAULT NULL,
  `cpfFuncionarios` varchar(14) DEFAULT NULL,
  `rgFuncionarios` varchar(20) DEFAULT NULL,
  `dataNascimentoFuncionarios` date DEFAULT NULL,
  `telefoneFuncionarios` varchar(14) DEFAULT NULL,
  `emailFuncionarios` varchar(100) DEFAULT NULL,
  `profissaoFuncionarios` varchar(50) DEFAULT NULL,
  `dataCadastroFuncionarios` datetime DEFAULT NULL,
  `statusFuncionarios` int(1) DEFAULT NULL,
  PRIMARY KEY (`idFuncionarios`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `funcionarios`
--

INSERT INTO `funcionarios` (`idFuncionarios`, `apelidoFuncionarios`, `nomeFuncionarios`, `enderecoFuncionarios`, `numeroFuncionarios`, `bairroFuncionarios`, `cepFuncionarios`, `cidadeFuncionarios`, `estadoFuncionarios`, `cpfFuncionarios`, `rgFuncionarios`, `dataNascimentoFuncionarios`, `telefoneFuncionarios`, `emailFuncionarios`, `profissaoFuncionarios`, `dataCadastroFuncionarios`, `statusFuncionarios`) VALUES
(1, 'Jussara', 'Jussara', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(2, 'R', 'Rodrigo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0),
(3, 'G', 'Gilson', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `histpagamento`
--

CREATE TABLE IF NOT EXISTS `histpagamento` (
  `idHistPagamento` int(11) NOT NULL AUTO_INCREMENT,
  `idPagamento` int(11) DEFAULT NULL,
  `valorHistPagamento` float DEFAULT NULL,
  `dataCadastroHistPagamento` datetime DEFAULT NULL,
  `statusHistPagamento` int(11) DEFAULT NULL,
  PRIMARY KEY (`idHistPagamento`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `histpagamento`
--

INSERT INTO `histpagamento` (`idHistPagamento`, `idPagamento`, `valorHistPagamento`, `dataCadastroHistPagamento`, `statusHistPagamento`) VALUES
(1, 1, 1400, '2011-01-12 00:21:00', 0),
(2, 4, 100, '2011-01-12 00:21:00', 0),
(3, 5, 1000, '2011-01-12 03:22:00', 0),
(4, 5, 900, '2011-01-12 03:34:00', 0),
(5, 4, 1000, '2011-01-12 03:35:00', 0),
(6, 5, 1000, '2011-01-12 03:35:00', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `idLogin` int(5) NOT NULL AUTO_INCREMENT,
  `idFuncionarios` int(5) DEFAULT NULL,
  `loginLogin` varchar(10) DEFAULT NULL,
  `senhaLogin` varchar(15) DEFAULT NULL,
  `nivelLogin` int(1) DEFAULT NULL,
  `dataCadastroLogin` datetime DEFAULT NULL,
  `statusLogin` int(1) DEFAULT NULL,
  PRIMARY KEY (`idLogin`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `login`
--

INSERT INTO `login` (`idLogin`, `idFuncionarios`, `loginLogin`, `senhaLogin`, `nivelLogin`, `dataCadastroLogin`, `statusLogin`) VALUES
(1, 1, 'Jussara', '123', 1, '2011-01-11 18:12:56', 0),
(2, 1, 'oloco', '123', 0, '2011-01-11 18:25:25', 0),
(3, 3, 'gilson', '123', 0, '2011-01-11 21:18:31', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `modificacao`
--

CREATE TABLE IF NOT EXISTS `modificacao` (
  `idModificacao` int(5) NOT NULL AUTO_INCREMENT,
  `idVendas` int(5) DEFAULT NULL,
  `idClientes` int(5) DEFAULT NULL,
  `idPagamento` int(5) DEFAULT NULL,
  `idFuncionarios` int(5) DEFAULT NULL,
  `tipoModificacao` text,
  `dataModificacao` datetime DEFAULT NULL,
  PRIMARY KEY (`idModificacao`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `modificacao`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `pagamento`
--

CREATE TABLE IF NOT EXISTS `pagamento` (
  `idPagamento` int(5) NOT NULL AUTO_INCREMENT,
  `idClientes` int(5) DEFAULT NULL,
  `valorTotalPagamento` float DEFAULT NULL,
  `limitePagamento` float DEFAULT NULL,
  `jurosPagamento` float DEFAULT NULL,
  `statusPagamento` int(1) DEFAULT NULL,
  `formaPagamento` varchar(8) DEFAULT NULL,
  `dataVencimentoPagamento` date DEFAULT NULL,
  `dataCadastroPagamento` datetime DEFAULT NULL,
  `pagoPagamento` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`idPagamento`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `pagamento`
--

INSERT INTO `pagamento` (`idPagamento`, `idClientes`, `valorTotalPagamento`, `limitePagamento`, `jurosPagamento`, `statusPagamento`, `formaPagamento`, `dataVencimentoPagamento`, `dataCadastroPagamento`, `pagoPagamento`) VALUES
(1, 1, 0, 1500, NULL, 0, NULL, '2011-02-01', '2011-01-07 00:28:00', 'sim'),
(4, 1, 0, 1000, NULL, 0, NULL, '2011-03-09', NULL, 'sim'),
(5, 1, 0, 1000, NULL, 0, NULL, '2011-04-12', '2011-01-12 00:07:23', 'sim');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

CREATE TABLE IF NOT EXISTS `vendas` (
  `idVendas` int(5) NOT NULL AUTO_INCREMENT,
  `idFuncionarios` int(5) DEFAULT NULL,
  `idClientes` int(5) DEFAULT NULL,
  `idPagamento` int(5) DEFAULT NULL,
  `valorCompraVendas` float DEFAULT NULL,
  `dataVendas` datetime DEFAULT NULL,
  `statusVendas` int(1) DEFAULT NULL,
  `pcVendas` int(11) NOT NULL,
  PRIMARY KEY (`idVendas`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`idVendas`, `idFuncionarios`, `idClientes`, `idPagamento`, `valorCompraVendas`, `dataVendas`, `statusVendas`, `pcVendas`) VALUES
(1, 1, 1, 1, 150, '2011-01-07 00:29:00', 0, 1),
(2, 1, 1, 1, 150, '2011-01-07 00:30:00', 0, 1),
(3, 1, 1, 1, 800, '2011-01-07 00:30:00', 0, 1),
(4, 1, 1, 1, 50, '2011-01-07 00:31:00', 0, 1),
(5, 1, 1, 1, 500, '2011-02-07 00:31:00', 0, 1);
