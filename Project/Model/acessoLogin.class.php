<?php

class acessoLogin
{
	private $idLogin;
	private $dataHoraEntrada;	
	private $ip;
	private $dataHoraSaida;
	private $sql;
	
	function __construct()//abre a conexao
	{
		// Inclui o arquivo com a classe
		include("conexao.class.php");
	 
		// Instancia/chama a classe MeuMySQL
		$this->sql = new conexaoMySql();
 
		// Conecta-se ao banco de dados usando os valores padrões
		$this->sql->conecta();
	}
	
	function inserirAcessoLogin()
	{
		$inserir = "INSERT INTO `acessoLogin` (`idLogin`, `dataHoraEntrada`, `ip`, `dataHoraSaida`) VALUES ('".$this->idLogin."', '".$this->dataHoraEntrada."', '".$this->ip."', '".$this->dataHoraSaida."')";
		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	
}

?>