<?php

class pagamento
{
	
	private $idCliente;
	private $valorTotal;
	private $limite;
	private $juros;
	private $valorPago;
	private $formaPagamento;
	private $dataVencimento;
	private $dataCadastro;
	private $status;
	private $pago;
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
	
	public function inverterData($data, $separar = "/", $juntar = "-")
	{
		return implode($juntar, array_reverse(explode($separar,$data)));
	}
	
	public function setPagamento($idCliente,$valorTotal,$limite,$juros,$formaPagamento,$dataVencimento,$dataCadastro,$pago)
	{
		$this->idCliente = $idCliente;
		$this->valorTotal = $valorTotal;
		$this->limite = $limite;
		$this->juros = $juros;
		$this->formaPagamento = $formaPagamento;
		$this->dataVencimento = $dataVencimento;
		$this->dataCadastro = $dataCadastro;
		$this->pago = $pago;
	}
	
	public function inserirPagamento()
	{
		
		$inserir = "INSERT INTO `pagamento` (`idClientes`, `valorTotalPagamento`, `limitePagamento`, `jurosPagamento`, `statusPagamento`, `formaPagamento`, `dataVencimentoPagamento`, `dataCadastroPagamento`, `pagoPagamento`) VALUES ('".$this->idCliente."', '".$this->valorTotal."', '".$this->limite."', '".$this->juros."', 0 , '".$this->formaPagamento."', '".$this->dataVencimento."', '".$this->dataCadastro."','".$this->pago."')";
		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	public function alterarPagamento($id)
	{
	
		$alterar = sprintf("UPDATE `pagamento` SET idClientes='".$this->idCliente."', valorTotalPagamento='".$this->valorTotal."', limitePagamento='".$this->limite."', jurosPagamento='".$this->juros."', formaPagamento='".$this->formaPagamento."', dataVencimentoPagamento='".$this->dataVencimento."', dataCadastroPagamento='".$this->dataCadastro."',pagoPagamento='".$this->pago."' WHERE idPagamento='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterar com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
	public function deletarPagamento($id)
	{
		
		$deletar = "UPDATE `pagamento` SET status='1' WHERE id='".$id."";
		
		if ($this->sql->consulta($deletar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado deletado com sucesso
		}
			else 
			{
				 return "Erro ao deletar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
	public function getVencimento()
	{
		return $this->dataVencimento;
	}
	
	public function inserirPagamentoSimples() //insere algumas partes do pagamento que é necessario para o controle das vendas
	{
		
		$inserir = "INSERT INTO `pagamento` (`idClientes`, `limitePagamento`,`statusPagamento`,`dataVencimentoPagamento`, `dataCadastroPagamento`, `pagoPagamento`) VALUES ('".$this->idCliente."', '".$this->limite."', 0 ,'".$this->dataVencimento."', '".$this->dataCadastro."','".$this->pago."')";
		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	public function alterarPagamentoSimples($id)
	{
	
		$alterar = sprintf("UPDATE `pagamento` SET idClientes='".$this->idCliente."',
		 				   limitePagamento='".$this->limite."', dataVencimentoPagamento='".$this->dataVencimento."', 
		                   dataCadastroPagamento='".$this->dataCadastro."',pagoPagamento='".$this->pago."' WHERE idClientes='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterar com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		
	}
	
	public function cadastrarValorPagamento($id,$valorVenda)
	{
		//tem qeu calcular o valor total aqui
		$consultaValorTotal = "SELECT valorTotalPagamento FROM pagamento WHERE idPagamento = '$id' ";
		$this->sql->consulta($consultaValorTotal);
		if($this->sql->registros() > 0)
		{
			$dados = $this->sql->resultadoArray();
			$valorTotal = $dados["valorTotalPagamento"];
			$this->valorTotal = $valorTotal + $valorVenda;
		}
		//fim - calculo valor total
		
		//granvando o calculo
		$alterar = sprintf("UPDATE `pagamento` SET valorTotalPagamento='".$this->valorTotal."', pagoPagamento='nao' WHERE idPagamento='".$id."' ");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterar com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
		//fim - gravando o calculo
	}
	
	public function pagarPagamento($id,$valorPagamento,$status)
	{
		$alterar = sprintf("UPDATE `pagamento` SET valorTotalPagamento='".$valorPagamento."', 
				    pagoPagamento='".$status."' WHERE idPagamento='".$id."' ");
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterar com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	public function alterarData($data)//aumenta um mes na data atual - a data tem que estar invertida no modo Inglês
	{
		$ano = substr("$data",0,4);
		$mes = substr("$data",5,2);
		$dia = substr("$data",8,2);
		
		$mes ++;
		
		if($mes<10)
		{
			$mes = "0" . $mes;
		}
		
		if($mes == 13)
		{
			$ano ++;
			$mes = "0". 1;
		}		
		
		return $ano."-".$mes."-".$dia;
	}
	
	
	
	
}

?>