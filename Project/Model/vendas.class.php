<?php

class vendas
{
	private $idFuncionario;
	private $idCliente;
	private $idPagamento;
	private $valorCompra;
	private $dataVenda;
	private $status;
	private $pc;
	private $sql;
	
	public function __construct()
	{
		// Inclui o arquivo com a classe
		require_once("../Persistence/conexao.class.php");
	 
		// Instancia/chama a classe MeuMySQL
		$this->sql = new conexaoMySql();
 
		// Conecta-se ao banco de dados usando os valores padrões
		if($this->sql->conecta());	
	}
	
	public function setVenda($idFuncionario, $idCliente, $idPagamento, $valorCompra, $dataVenda, $status, $pc)
	{
		$this->idFuncionario = $idFuncionario;
		$this->idCliente = $idCliente;
		$this->idPagamento = $idPagamento;
		$this->valorCompra = $valorCompra;
		$this->dataVenda = $dataVenda;
		$this->status = $status;
		$this->pc = $pc;
	}
	
	public function inserirVenda()
	{
		
		$inserir = "INSERT INTO `vendas` (`idFuncionarios`, `idClientes`, `idPagamento`, `valorCompraVendas`, `dataVendas`,`statusVendas`, `pcVendas`) VALUES ('".$this->idFuncionario."', '".$this->idCliente."', '".$this->idPagamento."', '".$this->valorCompra."', '".$this->dataVenda."', '".$this->status."', '".$this->pc."')";
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
	public function alterarVenda($id)
	{
	
		$alterar = sprintf("UPDATE `vendas` SET idFuncionario='".$this->idFuncionario."', idCliente='".$this->idCliente."', idPagamento='".$this->idPagamento."', valorCompra='".$this->valorCompra."', dataVenda='".$this->dataVenda."' WHERE id='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterado com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
	public function deletarVenda($id)
	{
		
		$deletar = sprintf("UPDATE `vendas` SET status='1' WHERE id='".$id."'");
		
		if ($this->sql->consulta($deletar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado deletado com sucesso
		}
			else 
			{
				 return "Erro ao deletar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
}

?>