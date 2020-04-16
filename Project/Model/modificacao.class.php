<?php

class modificacao
{
	
	private $idVenda;
	private $idCliente;
	private $idPagamento;
	private $idFuncionario;
	private $tipoModificacao;
	private $dataModificacao;
	private $sql; 
	
	public function __construct()
	{
		// Inclui o arquivo com a classe
		require_once("../Persistence/conexao.class.php");
	 
		// Instancia/chama a classe MeuMySQL
		$this->sql = new conexaoMySql();
 
		// Conecta-se ao banco de dados usando os valores padrões
		$this->sql->conecta();
	}
	
	public function setModificacao($idVenda, $idCliente, $idPagamento, $idFuncionario, $tipoModificacao, $dataModificacao)
	{
		$this->idVenda = $idVenda;
		$this->idCliente = $idCliente;
		$this->idPagamento = $idPagamento;
		$this->idFuncionario = $idFuncionario;
		$this->tipoModificacao = $tipoModificacao;
		$this->dataModificacao = $dataModificacao;
	}
	
	public function inserirModificao()
	{
		
		$inserir = "INSERT INTO `modificacao` (`idVendas`, `idClientes`, `idPagamento`, `idFuncionarios`, `tipoModificacao`, `dataModificacao`) VALUES ('".$this->idVenda."', '".$this->idCliente."', '".$this->idPagamento."', '".$this->idFuncionario."', '".$this->tipoModificacao."', '".$this->dataModificacao."')";
		
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