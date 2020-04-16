<?php
 
/**
* conexaoMySql
* Classe personalizada de uso do MySQL
*/
class conexaoMySql {
	// Propriedades padrões
    private $servidor = 'localhost'; // Endereço
    private $usuario = 'fixasolu_ghitu'; // Usuário
    private $senha = '246513748'; // Senha
    private $banco = 'fixasolu_projetocabecao'; // Banco de dados
    // Outras variáveis para uso interno
    private $conexao = null;
    private $query = null;
 
    /**
    * Função para fazer a conexão com o MySQL
    */
    public function conecta() {
        $this->conexao = mysql_connect($this->servidor, $this->usuario, $this->senha);
        $status = mysql_select_db($this->banco, $this->conexao);
        return $status;
    }
 
    /**
    * Função para fazer uma consulta no MySQL
    */
    public function consulta($query) {
		mysql_escape_string($query);
        $this->query = mysql_query($query);
        return $this->query;
    }
 
    /**
    * Função para retornar cada resultado da consulta
    */
    public function resultadoAssoc() {
        return mysql_fetch_assoc($this->query);
    }
	 
	/**
    * Função para retornar cada resultado da consulta em um array com indices inteiros
    */ 
	public function resultadoArray() {
        return mysql_fetch_array($this->query);
    }
    /**
    * Função que retorna o total de resultados encontrados
    */
    public function registros() {
        return mysql_num_rows($this->query);
    }
	
	public function verificaSessao($nivel)
	{
		if(!isset($_SESSION)) session_start();
		
		if(!isset($_SESSION['usuarioID']) or ($_SESSION['usuarioNivel']<$nivel))
		{
			session_destroy();
			
			return 1; // quando ocorre algo de errado
			exit;
		}	
	} 
}
 
?>