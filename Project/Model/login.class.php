<?php

class login
{
	private $idFuncionario;
	private $login;
	private $senha;
	private $nivel;
	private $dataCadastro;
	private $status;
	private $sql;
	
	function __construct()//abre a conexao
	{
		// Inclui o arquivo com a classe
		require_once("../Persistence/conexao.class.php");
	 
		// Instancia/chama a classe MeuMySQL
		$this->sql = new conexaoMySql();
 
		// Conecta-se ao banco de dados usando os valores padrões
		$this->sql->conecta();
	}
	
	function setLogin($idFuncionario,$login,$senha,$nivel,$dataCadastro,$status)
	{
		$this->idFuncionario = $idFuncionario;
		$this->login = $login;
		$this->senha = $senha;
		$this->nivel = $nivel;
		$this->dataCadastro = $dataCadastro;
		$this->status = $status;
	}
	
	function inserirLogin()
	{
		$inserir = "INSERT INTO `login` (`idFuncionario`, `login`, `senha`, `nivel`, `dataCadastro`, `status`) VALUES ('".$this->idFuncionario."', '".$this->login."', '".$this->senha."', '".$this->nivel."', '".$this->dataCadastro."', '".$this->status."')";
		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1;//dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function alterarLogin($id)
	{
		$alterar = sprintf("UPDATE `login` SET idFuncionario='".$this->idFuncionario."', login='".$this->login."', senha='".$this->senha."', nivel='".$this->nivel."', dataCadastro='".$this->dataCadastro."' WHERE id='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1;//dado alterar com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function deletarLogin($id)
	{
		$deletar = "UPDATE `loginLogin` SET statusLogin='1' WHERE idLogin='".$id."";
		
		if ($this->sql->consulta($deletar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado deletado com sucesso
		}
			else 
			{
				 return "Erro ao deletar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function logar($login,$senha)// uuu essa função foi boaaaaaaaaaaaaaaaa
	{
		
		// 1 INICIO - consultado o login e senha para ver se existe no banco de dados
		$querylogin = "SELECT idLogin, loginLogin, nivelLogin FROM login 
					   WHERE loginLogin = '".$login."' AND senhaLogin = '".$senha."'";
		$this->sql->consulta($querylogin);
		$okConsulta = $this->sql->registros();
		
		
		if($okConsulta == 1)//Ok existe Login
		{	
			$registros = $this->sql->resultadoArray();
			
			$idLogin = $registros["idLogin"];
			$loginLogin = $registros["loginLogin"];
			$nivelLogin = $registros["nivelLogin"];		
		}
		else{//não existe login	
			return 2;
			header("Location: logar.php");
			exit; //e termina a historia finaliza a pagina atual
		}
		// 1 FIM
		
		// 2 INICIO - iniciar a sessão verificar se existe alguma já iniciada com o login de quem está tentando logar
		if(!isset($_SESSION))
		{
			session_start();
		}
		if(isset($_SESSION) && isset($_SESSION[$loginLogin]) && ($_SESSION[$loginLogin] == $loginLogin))//existe sessão iniciada com o mesmo usuario que logou antes
		{
			
			$_SESSION[$loginLogin] = NULL;
			
			//vamo gravar a data de saida do login so para fechar o idAcessoLogin
			//precisamos saber se o ultimo login logado foi cadastrada a hora final dele
			
			//descobrindo o idLogin
			$queryEncontrarIdLogin = "SELECT idLogin FROM login WHERE loginLogin = '".$loginLogin."'";
			$this->sql->consulta($queryEncontrarIdLogin);
			$okConsulta = $this->sql->registros();
			
			if($okConsulta == 1)
				{	
					$registrosidLogin = $this->sql->resultadoArray();
					$idLogin = $registrosidLogin["idLogin"];
			
				}
			//idLogin encontrado
			
			//Registrando a saida
			$dataHoraSaidaAcessoLogin = date("Y/m/d H:i");
			$ipAcessoLoginSaida = $_SERVER['REMOTE_ADDR'];
			
			$queryAcessoLoginSaida = "UPDATE `acessologin` SET 
									 dataHoraSaidaAcessoLogin = '".$dataHoraSaidaAcessoLogin."', ipAcessoLoginSaida ='".$ipAcessoLoginSaida."'
			                         WHERE idLogin='".$idLogin."' and dataHoraSaidaAcessoLogin is NULL and ipAcessoLoginSaida is NULL";
			if($this->sql->consulta($queryAcessoLoginSaida))
			{
					return 1; //mensagem de saida registrada
			}
			else
			{
				return $this->sql->consulta($queryAcessoLoginSaida);
			}
			// saida registrada	
		}else{ 
			
			$_SESSION[$loginLogin] = $loginLogin;
			
			// vamo gravar a data de entrada do login 
			// serão gravados somente idLogin, dataHoraEntradaAcessoLogin, ipAcessoLoginEntrada
			$dataHoraEntradaAcessoLogin = date("Y/m/d H:i");
			$ipAcessoLoginEntrada = $_SERVER['REMOTE_ADDR'];
			
			
			
			$queryAcessoLoginEntrada =  "INSERT INTO `acessologin` (`idLogin`, `dataHoraEntradaAcessoLogin`, `ipAcessoLoginEntrada`) 
								 VALUES ('".$idLogin."', '".$dataHoraEntradaAcessoLogin."', '".$ipAcessoLoginEntrada."')";
			
			if ($this->sql->consulta($queryAcessoLoginEntrada))
				{
					
					header("Location: principal.php?idLogin=".$idLogin." ");
					return 3;
				}
			else
				{
					return $this->sql->consulta($queryAcessoLoginEntrada);
				}
			//entrada registrada		
		}
		// 2 FIM 
		
	}
	
	function logoff($loginLogin)
	{
		session_start();
		if(isset($_SESSION) && isset($_SESSION[$loginLogin]) && ($_SESSION[$loginLogin] == $loginLogin))//existe sessão iniciada com o mesmo usuario que logou antes
		{
			$_SESSION[$loginLogin] = NULL;
			
			//vamo gravar a data de saida do login so para fechar o idAcessoLogin
			//precisamos saber se o ultimo login logado foi cadastrada a hora final dele
			
			//descobrindo o idLogin
			$queryEncontrarIdLogin = "SELECT idLogin FROM login WHERE loginLogin = '".$loginLogin."'";
			$this->sql->consulta($queryEncontrarIdLogin);
			$okConsulta = $this->sql->registros();
			
			if($okConsulta == 1)
				{	
					$registrosidLogin = $this->sql->resultadoArray();
					$idLogin = $registrosidLogin["idLogin"];
				}
			//idLogin encontrado
			
			//Registrando a saida
			$dataHoraSaidaAcessoLogin = date("Y/m/d H:i");
			$ipAcessoLoginSaida = $_SERVER['REMOTE_ADDR'];
			$queryAcessoLoginSaida = "UPDATE `acessologin` SET 
									 dataHoraSaidaAcessoLogin = '".$dataHoraSaidaAcessoLogin."', ipAcessoLoginSaida ='".$ipAcessoLoginSaida."'
			                         WHERE idLogin='".$idLogin."' and dataHoraSaidaAcessoLogin is NULL and ipAcessoLoginSaida is NULL";
			if($this->sql->consulta($queryAcessoLoginSaida))
				return 1; //mensagem de saida registrada
			// saida registrada	
		}
	}
	
	public function verificaLogin($idLogin) // ajuda a resolver o problema das multisessoes e principalmente mostra o nome do funcionario logado e verifica.
	{			
		$loginLogin;		
		$selecionaLogin = "SELECT loginLogin, nivelLogin, nomeFuncionarios FROM 
						  (login INNER JOIN funcionarios ON funcionarios.idFuncionarios=login.idFuncionarios) 
						  WHERE idLogin='$idLogin' ";
		$this->sql->consulta($selecionaLogin);
		if($this->sql->registros() == 1)
		{
			$dados = $this->sql->resultadoArray();
			if(isset($_SESSION[$dados["loginLogin"]]) && $_SESSION[$dados["loginLogin"]] == $dados["loginLogin"])//se a sessao ta ok e se tem nela é o esperado
			{
				return $dados["loginLogin"];	
			}
			else
			{
				//erro não veio na sessão ou não existe sessão (loginLogin)
				return 2;
			}
		}
		else
		{
			//erro no comando sql
			return 1;
		}
	}
	
	public function getNivel($idLogin)
	{
		$selecionaNivel = "SELECT nivelLogin FROM login WHERE idLogin = '$idLogin' ";
		$this->sql->consulta($selecionaNivel);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["nivelLogin"];
		}
		else
		{
			return $this->sql->registros();
		}
	}
}

?>