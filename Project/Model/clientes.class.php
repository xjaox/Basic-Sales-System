<?php

class clientes
{
	private $apelido;
	private $nome;
	private $endereco;
	private $numero;
	private $bairro;
	private $cep;
	private $cidade;
	private $estado;
	private $cpf;
	private $rg;
	private $dataNascimento = null;
	private $telefone;
	private $email;
	private $profissao;
	private $dataCadastro;
	private $sql;
	
	function __construct()//abre a conexao
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
	
	function setClientes($apelido,$nome,$endereco,$numero,$bairro,$cep,$cidade,$estado,$cpf,
						 $rg,$dataNascimento,$telefone,$email,$profissao,$dataCadastro,$status)//seta os dados
	{
		$this->apelido = $apelido; //variaveis globais recebendo os valores que estão nas locais da funcao
		$this->nome = $nome;
		$this->endereco = $endereco;
		$this->numero = (int) $numero;
		$this->bairro = $bairro;
		$this->cep = $cep;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->cpf = $cpf;
		$this->rg = $rg;
		$this->dataNascimento = $this->inverterData($dataNascimento);
		$this->telefone = $telefone;
		$this->email = $email;
		$this->profissao = $profissao;
		$this->dataCadastro = $dataCadastro;
		$this->status = $status;
	}
	
	function inserirClientes()//insere clientes no banco
	{
		$inserir = "INSERT INTO 
						 `clientes` (`apelidoClientes`,`nomeClientes`,`enderecoClientes`,`numeroClientes`,`bairroClientes`,
						 `cepClientes`,`cidadeClientes`,	 `estadoClientes`,`cpfClientes`,`rgClientes`,`dataNascimentoClientes`,`telefoneClientes`,`emailClientes`,`profissaoClientes`,`dataCadastroClientes`,`statusClientes`)
						  VALUES ('".$this->apelido."','".$this->nome."','".$this->endereco."','".$this->numero."','".$this->bairro."',
						          '".$this->cep."','".$this->cidade."','".$this->estado."','".$this->cpf."','".$this->rg."','".$this->dataNascimento."',
						          '".$this->telefone."','".$this->email."','".$this->profissao."','".$this->dataCadastro."',0)"; 
								  // query para inserir os dados
								  
	  		
		if ($this->sql->consulta($inserir))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado inserido com sucesso
		}
			else 
			{
				 return "Erro ao inserir os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function alterarClientes($id)
	{
		$alterar = sprintf("UPDATE `clientes` SET apelidoClientes='".$this->apelido."', nomeClientes='".$this->nome."', enderecoClientes='".$this->endereco."', numeroClientes='".$this->numero."', bairroClientes='".$this->bairro."', cepClientes='".$this->cep."', cidadeClientes='".$this->cidade."', estadoClientes='".$this->estado."', cpfClientes='".$this->cpf."', rgClientes='".$this->rg."', dataNascimentoClientes='".$this->dataNascimento."', telefoneClientes='".$this->telefone."', emailClientes='".$this->email."', profissaoClientes='".$this->profissao."' WHERE idClientes='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterado com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function deletarClientes($id)
	{
		$deletar = sprintf("UPDATE `clientes` SET statusClientes=1 WHERE idClientes=".$id."");
		
		if ($this->sql->consulta($deletar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado deletado com sucesso
		}
			else 
			{
				 return "Erro ao deletar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	
	function inserirClientesSimples()//insere clientes cadastro simples no banco
	{
		$inserir = "INSERT INTO 
						 `clientes` (`apelidoClientes`,`nomeClientes`,`statusClientes`)
						  VALUES ('".$this->apelido."','".$this->nome."',0)"; 
								  // query para inserir os dados  		
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