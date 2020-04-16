<?php

class histPagamento
{
	
	private $idPagamento;
	private $valorHistPagamento;
	private $dataCadastroHistPagamento;
	private $statusHistPagamento;
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
	
	public function setHistPagamento($idPag, $valor,$dataCadastro,$status)
	{
		$this->idPagamento = $idPag;
		$this->valorHistPagamento = $valor;
		$this->dataCadastroHistPagamento = $dataCadastro;
		$this->statusHistPagamento = $status;
	}
	
	public function insereHistPagamento()
	{
		
		$inserir = "INSERT INTO `histpagamento` (`idPagamento`, `valorHistPagamento`, `dataCadastroHistPagamento`, `statusHistPagamento`) 
					VALUES ('".$this->idPagamento."', '".$this->valorHistPagamento."', '".$this->dataCadastroHistPagamento."',
					'".$this->statusHistPagamento."')";
		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	public function creditosTotais()
	{
		$creditos = 0;
		$consultaCreditos = "SELECT valorHistPagamento FROM histpagamento";
		$this->sql->consulta($consultaCreditos);
		if($this->sql->registros()>0)
		{
			while($dados = $this->sql->resultadoArray())
			{
				$creditos += $dados["valorHistPagamento"];
			}
			return $creditos;
		}
		else
		{
			return 0;
		}
	}
	
}
?>