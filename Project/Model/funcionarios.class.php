<?php

class funcionarios
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
	private $dataNascimento;
	private $telefone;
	private $email;
	private $profissao;
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
	public function IniciazaValores()
	{
		$this->apelido = NULL;
		$this->nome = NULL;
		$this->endereco = NULL;
		$this->numero = NULL;
		$this->bairro = $bairro;
		$this->cep = $cep;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->cpf = $cpf;
		$this->rg = $rg;
		$this->dataNascimento = $dataNascimento;
		$this->telefone = $telefone;
		$this->email = $email;
		$this->profissao = $profissao;
		$this->dataCadastro = $dataCadastro;
		$this->status = $status;
	}
	
	public function setFuncionarios($apelido,$nome,$endereco,$numero,$bairro,$cep,$cidade,$estado,$cpf,
						     $rg,$dataNascimento,$telefone,$email,$profissao,$dataCadastro,$status)//seta os dados
	{
		$this->apelido = $apelido; //variaveis globais recebendo os valores que estão nas locais da funcao
		$this->nome = $nome;
		$this->endereco = $endereco;
		$this->numero = $numero;
		$this->bairro = $bairro;
		$this->cep = $cep;
		$this->cidade = $cidade;
		$this->estado = $estado;
		$this->cpf = $cpf;
		$this->rg = $rg;
		$this->dataNascimento = $dataNascimento;
		$this->telefone = $telefone;
		$this->email = $email;
		$this->profissao = $profissao;
		$this->dataCadastro = $dataCadastro;
		$this->status = $status;
	}
	
	function inserirFuncionarios()//insere funcionarios no banco
	{
		$inserir = "INSERT INTO 
						 `funcionarios` (`apelidoFuncionarios`,`nomeFuncionarios`,`enderecoFuncionarios`,`numeroFuncionarios`,`bairroFuncionarios`,`cepFuncionarios`,`cidadeFuncionarios`,
							     	 `estadoFuncionarios`,`cpfFuncionarios`,`rgFuncionarios`,`dataNascimentoFuncionarios`,`telefoneFuncionarios`,`emailFuncionarios`,`profissaoFuncionarios`,`dataCadastroFuncionarios`,`statusFuncionarios`)
						  VALUES ('".$this->apelido."','".$this->nome."','".$this->endereco."','".$this->numero."','".$this->bairro."',
						          '".$this->cep."','".$this->cidade."','".$this->estado."','".$this->cpf."','".$this->rg."','".$this->dataNascimento."',
						          '".$this->telefone."','".$this->email."','".$this->profissao."','".$this->dataCadastro."','".$this->status."')"; 
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
	
	function alterarFuncionarios($id)
	{
		$alterar = sprintf("UPDATE `funcionarios` SET apelidoFuncionarios='".$this->apelido."', 
		nomeFuncionarios='".$this->nome."',	enderecoFuncionarios='".$this->endereco."',
		numeroFuncionarios='".$this->numero."', bairroFuncionarios='".$this->bairro."', 
		cepFuncionarios='".$this->cep."', 
		cidadeFuncionarios='".$this->cidade."', 
		estadoFuncionarios='".$this->estado."', 
		cpfFuncionarios='".$this->cpf."', 
		rgFuncionarios='".$this->rg."',	
		dataNascimentoFuncionarios='".$this->dataNascimento."', 
		telefoneFuncionarios='".$this->telefone."', 
		emailFuncionarios='".$this->email."', 
		profissaoFuncionarios='".$this->profissao."' 
		WHERE idFuncionarios='".$id."'");
		
		if ($this->sql->consulta($alterar))//a função consulta da class conexão retorna o resultado da função mysql_query
		{
			return 1; //dado alterado com sucesso
		}
			else 
			{
				 return "Erro ao alterar os dados: " .mysql_error();//mostra a mensagem de erro para o usuário 
			}
	}
	
	function deletarFuncionarios($id)
	{
		$deletar = "UPDATE `funcionarios` SET statusFuncionarios=1 WHERE idFuncionarios=".$id." ";
		
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