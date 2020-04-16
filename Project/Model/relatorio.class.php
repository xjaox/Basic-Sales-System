<?php
 
class relatorio
{
	private $sql;
	private $sqlsql;
	
	function relatorio()
	{	
		// Inclui o arquivo com a classe
		require_once("../Persistence/conexao.class.php");
	 
		// Instancia/chama a classe MeuMySQL
		$this->sql = new conexaoMySql();
		$this->sqlsql = new conexaoMySql();
 
		// Conecta-se ao banco de dados usando os valores padrões
		if($this->sql->conecta());
		if($this->sqlsql->conecta());
	}
	
	public function arredondar_dois_decimal($valor)
	{ 
   		$float_arredondar=round($valor * 100) / 100; 
  		return $float_arredondar; 
	} 
	
	public function mesExtenso($mes)
	{
		switch($mes)
		{
			case '01':{return "Jan"; break;}		
			case '02':{return "Fev"; break;}
			case '03':{return "Mar"; break;}
			case '04':{return "Abr"; break;}
			case '05':{return "Mai"; break;}
			case '06':{return "Jun"; break;}
			case '07':{return "Jul"; break;}
			case '08':{return "Ago"; break;}
			case '09':{return "Set"; break;}
			case '10':{return "Out"; break;}
			case '11':{return "Nov"; break;}
			case '12':{return "Dez"; break;}
			default: {return $mes; break;}
		}
	}
	
	public function converterValor($valor) //retorna o valor sem R$ e pronto para ser adcionado no DB
	{
		$trocadoValor = str_replace("R$","",$valor);
		$trocadoValor = str_replace(".","",$trocadoValor);
		$trocadoValor = str_replace(",",".",$trocadoValor);
		return $trocadoValor;	
	}
	
	public function inverterData($data, $separar = "/", $juntar = "-")
	{
		return implode($juntar, array_reverse(explode($separar,$data)));
	}
	
	public function totalVendasData($data)//a data define quantos dias será mostrado as vendas ex: $data = 1 mostra so do dia atual, 
																						      //30 mostra do mes atual
	{
		switch($data)
		{
			case (1)://dia atual
			{
				$dataInicial = date(" Y-m-d 00:00:00");//data hora inicial do dia
				$dataFinal = date(" Y-m-d 23:60:60");//data hora final do dia
				$query = "SELECT apelidoClientes, apelidoFuncionarios, valorCompraVendas, dataVendas FROM 
				 		((vendas INNER JOIN clientes ON clientes.idClientes=vendas.idClientes) 
				 		INNER JOIN funcionarios ON funcionarios.idFuncionarios=vendas.idFuncionarios) 
						WHERE ((dataVendas>='$dataInicial') AND (dataVendas<='$dataFinal'))";
				
				$this->sql->consulta($query);
				break; //faz essa execução e nem procura em outras 
				
			}	
			case (30)://mes atual
			{
				$dataInicial = date(" Y-m-01 00:00:00");//data hora inicial do dia				
				$query = "SELECT apelidoClientes, apelidoFuncionarios, valorCompraVendas, dataVendas FROM 
				 		((vendas INNER JOIN clientes ON clientes.idClientes=vendas.idClientes) 
				 		INNER JOIN funcionarios ON funcionarios.idFuncionarios=vendas.idFuncionarios) 
						WHERE ((dataVendas>='$dataInicial'))";
				
				$this->sql->consulta($query);
				break; //faz essa execução e nem procura em outras opções do case
			}
			default://lista todas as vendas
			{
				$query = "SELECT apelidoClientes, apelidoFuncionarios, valorCompraVendas, dataVendas FROM 
				 		((vendas INNER JOIN clientes ON clientes.idClientes=vendas.idClientes) 
				 		INNER JOIN funcionarios ON funcionarios.idFuncionarios=vendas.idFuncionarios)";
				
				$this->sql->consulta($query);
			}
		}
	}
	
	public function consultaListarClientes($inicio, $quantidade)
	{
		
		$query = "SELECT * FROM clientes
				  WHERE (statusClientes<>1) ORDER BY nomeClientes LIMIT $inicio, $quantidade ";
		return $this->sql->consulta($query);
		
	}
	
	public function consultaListarFuncionarios($inicio, $quantidade)
	{
		$query = "SELECT idFuncionarios, nomeFuncionarios, apelidoFuncionarios, cpfFuncionarios, telefoneFuncionarios FROM funcionarios
				  WHERE (statusFuncionarios<>1) ORDER BY nomeFuncionarios LIMIT $inicio, $quantidade";
		
		return $this->sql->consulta($query);
		
	}

	public function consultaListarDebitos($inicio, $quantidade)
	{
		$dataAtual = date("Y-m-d");
		
		$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
				  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento < '".$dataAtual."' AND pagoPagamento = 'nao'
				  LIMIT $inicio, $quantidade ";
		
		$this->sql->consulta($query);
		
		return $this->sql->registros();	
	}
	
	public function consultaListarDebitosHoje($inicio, $quantidade)
	{
		
		$dataAtual = date("Y/m/d");
		$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
		  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
		  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento = '".$dataAtual."' AND 
		  pagoPagamento = 'nao' LIMIT $inicio, $quantidade ";
				  
					 
		$this->sql->consulta($query);
		
		return $this->sql->registros();	
	}
	
	public function consultaListarCreditos($inicio, $quantidade)
	{
		$query = "SELECT clientes.idClientes,nomeClientes, enderecoClientes, 
				  apelidoClientes, dataVencimentoPagamento, SUM(valorHistPagamento) AS total
				  FROM clientes
				  INNER JOIN pagamento ON pagamento.idClientes = clientes.idClientes
				  INNER JOIN histpagamento ON pagamento.idPagamento = histpagamento.idPagamento
				  WHERE statusClientes<>1 AND pagoPagamento = 'sim' GROUP BY idClientes
				  LIMIT $inicio, $quantidade ";
				 
		
		$this->sql->consulta($query);
		
		return $this->sql->registros();	
	}

	public function quantidadeTotalListarClientes()
	{
		$query = "SELECT nomeClientes, apelidoClientes, cpfClientes, telefoneClientes FROM clientes
				  WHERE (statusClientes<>1) ORDER BY nomeClientes";
		
		$this->sql->consulta($query);
		
		return $this->sql->registros();
	}
	
	public function quantidadeTotalListarFuncionarios()
	{
		$query = "SELECT nomeFuncionarios, apelidoFuncionarios, cpfFuncionarios, telefoneFuncionarios FROM funcionarios
				  WHERE (statusFuncionarios<>1) ORDER BY nomeFuncionarios";
		
		$this->sql->consulta($query);
		
		return $this->sql->registros();
	}
	
	public function quantidadeTotalListarDebitos()
	{
		$dataAtual = date("Y-m-d");
		
		$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento  
				  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  WHERE statusClientes<>1 AND dataVencimentoPagamento < '".$dataAtual."' AND pagoPagamento = 'nao'";
				  		
		$this->sql->consulta($query);
		
		return $this->sql->registros();	
	}
	
	public function quantidadeTotalListarDebitosHoje()
	{
		$dataAtual = date("Y/m/d");
		$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
		  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
		  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento = '".$dataAtual."' AND 
		  pagoPagamento = 'nao' ";
				
		$this->sql->consulta($query);
			
		return $this->sql->registros();
		break;
	}
	
	public function quantidadeTotalListarCreditos()
	{
		$dataAtual = date("Y-m-d");
		
		$query = "SELECT clientes.idClientes,nomeClientes, apelidoClientes, dataVencimentoPagamento, SUM(valorHistPagamento) AS total
						  FROM clientes
						  INNER JOIN pagamento ON pagamento.idClientes = clientes.idClientes
						  INNER JOIN histpagamento ON pagamento.idPagamento = histpagamento.idPagamento
						  WHERE statusClientes<>1 AND pagoPagamento = 'sim' GROUP BY idClientes
						  ";
		
		$this->sql->consulta($query);
		
		return $this->sql->registros();	
	}
	
	public function consultar($nome,$tipo, $inicio, $quantidade) //executa consultas em geral nome, vendas e tal, $nome de quem é, $tipo 
														  //se é clientes ou vendas ou funcionario, é utilizado no botão consulta.
	{
		switch($tipo)
		{
			case("clientes"):
			{
				$query = "SELECT idClientes, nomeClientes, apelidoClientes, cpfClientes, telefoneClientes FROM clientes
						 WHERE nomeClientes LIKE '%".$nome."%' LIMIT $inicio, $quantidade";
				
				return $this->sql->consulta($query);
				break;
			}
			
			case("vendas"):
			{
				$query = "SELECT apelidoClientes, apelidoFuncionarios, valorCompraVendas, dataVendas FROM 
				 		((vendas INNER JOIN clientes ON clientes.idClientes=vendas.idClientes) 
				 		INNER JOIN funcionarios ON funcionarios.idFuncionarios=vendas.idFuncionarios) 
						WHERE apelidoClientes LIKE '%".$nome."%' LIMIT $inicio, $quantidade";
				
				$this->sql->consulta($query);
				break;
			}
			
			case("funcionarios"):
			{
				$query = "SELECT idFuncionarios, nomeFuncionarios, apelidoFuncionarios, cpfFuncionarios, telefoneFuncionarios FROM funcionarios
						 WHERE nomeFuncionarios LIKE '%".$nome."%' LIMIT $inicio, $quantidade";
				
				return $this->sql->consulta($query);
				break;
			}
			
			case("debitos"):
			{
				$dataAtual = date("Y/m/d");
				$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
				  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento < '".$dataAtual."' AND 
				  pagoPagamento = 'nao' AND nomeClientes LIKE '%".$nome."%'  LIMIT $inicio, $quantidade ";
				
				return $this->sql->consulta($query);
				break;
			}
			
			case("debitosHoje"):
			{
				$dataAtual = date("Y/m/d");
				$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
				  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento = '".$dataAtual."' AND 
				  pagoPagamento = 'nao' AND nomeClientes LIKE '%".$nome."%'  LIMIT $inicio, $quantidade ";
				
				return $this->sql->consulta($query);
				break;
			}
			
			case("creditos"):
			{
				$query = "SELECT clientes.idClientes,nomeClientes, apelidoClientes, dataVencimentoPagamento, SUM(valorHistPagamento) AS total
						  FROM clientes
						  INNER JOIN pagamento ON pagamento.idClientes = clientes.idClientes
						  INNER JOIN histpagamento ON pagamento.idPagamento = histpagamento.idPagamento
						  WHERE statusClientes<>1 AND pagoPagamento = 'sim' AND nomeClientes LIKE '%".$nome."%' GROUP BY idClientes
						  ";
				
				return $this->sql->consulta($query);
				break;
			}
			
		}
	}
	
	public function quantidadeTotalConsultar($nome,$tipo)
	{
		switch($tipo)
		{
			case("clientes"):
			{
				$query = "SELECT nomeClientes, apelidoClientes, cpfClientes, telefoneClientes FROM clientes
						 WHERE nomeClientes LIKE '%".$nome."%' ";
				
				$this->sql->consulta($query);
				return $this->sql->registros();
				
				break;
			}
			
			case("vendas"):
			{
				$query = "SELECT apelidoClientes, apelidoFuncionarios, valorCompraVendas, dataVendas FROM 
				 		((vendas INNER JOIN clientes ON clientes.idClientes=vendas.idClientes) 
				 		INNER JOIN funcionarios ON funcionarios.idFuncionarios=vendas.idFuncionarios) 
						WHERE apelidoClientes LIKE '%".$nome."%'";
				
				$this->sql->consulta($query);
				
				return $this->sql->registros();				
				break;
			}
			
			case("funcionarios"):
			{
				$query = "SELECT nomeFuncionarios, apelidoFuncionarios, cpfFuncionarios, telefoneFuncionarios FROM funcionarios
						 WHERE nomeFuncionarios LIKE '%".$nome."%' ";
				
				$this->sql->consulta($query);
				
				return $this->sql->registros();
				break;
			}
			
			case("debitos"):
			{
				$dataAtual = date("Y/m/d");
				$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
				  		FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  		WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento < '".$dataAtual."' AND 
				  		pagoPagamento = 'nao' AND nomeClientes LIKE '%".$nome."%' ";
				
				$this->sql->consulta($query);
				
				return $this->sql->registros();
				break;
			}
			
			case("debitosHoje"):
			{
				$dataAtual = date("Y/m/d");
				$query = "SELECT clientes.idClientes,nomeClientes,enderecoClientes,apelidoClientes, valorTotalPagamento, dataVencimentoPagamento 
				  FROM (pagamento INNER JOIN clientes ON clientes.idClientes = pagamento.idClientes)
				  WHERE statusClientes<>1 AND pagamento.dataVencimentoPagamento = '".$dataAtual."' AND 
				  pagoPagamento = 'nao' AND nomeClientes LIKE '%".$nome."%' ";
				
				$this->sql->consulta($query);
				
				return $this->sql->registros();
				break;
			}
			
			case("creditos"):
			{
				$query = "SELECT clientes.idClientes,nomeClientes, apelidoClientes, dataVencimentoPagamento, SUM(valorHistPagamento) AS total
						  FROM clientes
						  INNER JOIN pagamento ON pagamento.idClientes = clientes.idClientes
						  INNER JOIN histpagamento ON pagamento.idPagamento = histpagamento.idPagamento
						  WHERE statusClientes<>1 AND pagoPagamento = 'sim' AND nomeClientes LIKE '%".$nome."%' GROUP BY idClientes
						  ";
				
				$this->sql->consulta($query);
				
				return $this->sql->registros();
				break;
			}
			
		}
	}
	
	public function mostraListar()
	{
		return $this->sql->resultadoArray();
	}
	
	public function autoComplete()
	{
		$consultaTodosClientes = "SELECT nomeClientes, apelidoClientes FROM clientes";
		$this->sql->consulta($consultaTodosClientes);
		return $this->sql->registros();
	}
	
	public function verificaUmCliente($nomeCliente)
	{
		$consultaUmCliente = "SELECT * FROM clientes WHERE nomeClientes = '$nomeCliente' ";
		$this->sql->consulta($consultaUmCliente);
		return $this->sql->registros();
	}
	
	public function vendasSegundaParte($nomeCliente) // devolve o limite a data de  vencimento
	{
		$retornaIdcliente = "SELECT idClientes FROM clientes WHERE nomeClientes = '$nomeCliente' ";
		$this->sql->consulta($retornaIdcliente);
		
		if($this->sql->registros() >0)
		{
			$dados = $this->sql->resultadoArray();
			$idClientes = $dados["idClientes"];
			
			$consultaLimVen = "SELECT idPagamento, idClientes, limitePagamento, dataVencimentoPagamento, valorTotalPagamento FROM pagamento 
							   WHERE idClientes = '$idClientes' ORDER BY idPagamento desc";
			$this->sql->consulta($consultaLimVen);
			
			if($this->sql->registros() > 0)
			{
				return $this->sql->resultadoArray(); // retona limitePagamento e dataVencimento
			}
			else
			{
				return 2; //pagamento não vinculado ao cliente
			}
		}
		else 
		{
			return 1; //cliente não existe
		}
	}
	
	public function verificaSeClienteTemPagamento($nomeClientes)
	{
		$consultaClientes = "SELECT idClientes FROM clientes WHERE nomeClientes = '$nomeClientes' ";
		$this->sql->consulta($consultaClientes);
		$dados = $this->sql->resultadoArray();
		$idClientes = $dados["idClientes"];
		
		$consultaClientePagamento = "SELECT * FROM pagamento WHERE idClientes='$idClientes' AND statusPagamento=0 AND pagoPagamento='nao' ORDER BY idPagamento desc ";
		$result = $this->sql->consulta($consultaClientePagamento);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			if($dados["valorTotalPagamento"]>0)
			{
				return 1;
			}
			else 
			{
				//cliente não tem valor no pagamento
				return 0;
				
			}
			
		}
		else
		{
			return 0;
			
			//cliente não tem pagamento
		}
	}
	
	public function listarValorTotalDataVencimento($nomeClientes)
	{
		$consultaIdCli = "SELECT idClientes FROM clientes WHERE nomeClientes = '$nomeClientes' ";
		$this->sql->consulta($consultaIdCli);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			$idClientes = $dados["idClientes"];
			$consultaValorTotalDataVencimento = "SELECT idPagamento, valorTotalPagamento, dataVencimentoPagamento FROM pagamento WHERE idClientes = '$idClientes' ";
			$this->sql->consulta($consultaValorTotalDataVencimento);
			if($this->sql->registros() > 0)
			{
				return 1;
			}
			else
			{
				//cliente não tem pagamento
				return 0;
			}
		}
		else
		{
			//cliente não existe
			return 0;
		}
	}
	
	public function detalhesVendas($idClientes,$idPagamento)
	{
		$consultaDetalhesVendas = "SELECT valorCompraVendas, dataVendas,nomeClientes,telefoneClientes,clientes.idClientes,idVendas 
								   FROM vendas INNER JOIN clientes ON vendas.idClientes=clientes.idClientes
								   WHERE idPagamento = '$idPagamento' AND clientes.idClientes = '$idClientes' ";
		$this->sql->consulta($consultaDetalhesVendas);
		return $this->sql->registros();
	}
	
	public function status($valorTotalPago,$valorTotalDevendo,&$listarDebitos)
	{
		//Funcao para calcular status do cliente
		$total = $valorTotalPago + $valorTotalDevendo;
		if($total != 0)
		{
		  if($valorTotalPago >= $valorTotalDevendo)
		  {
			  $resultado = ($valorTotalPago * 100) / $total;
		  }
		  else
		  {
			  $resultado = 0; 
		  }
		  
		}
		else
		{
			$resultado = 0;
		}
		
		if($resultado >= 0 && $resultado <= 10)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaMeio.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 10 && $resultado <= 20)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 20 && $resultado <= 30)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaMeio.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 30 && $resultado <= 40)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 40 && $resultado <= 50)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaMeio.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 50 && $resultado <= 60)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 60 && $resultado <= 70)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaMeio.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 70 && $resultado <= 80)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
		elseif($resultado > 80 && $resultado <= 90)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaMeio.png'>";
		}
		elseif($resultado>90 && $resultado<=100)
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaAmarela.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaAmarela.png'>";
		}
		else
		{
			$listarDebitos->estrela01 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela02 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela03 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela04 = "<img src='../View/template/img/estrelaBranco.png'>";
			$listarDebitos->estrela05 = "<img src='../View/template/img/estrelaBranco.png'>";
		}
	}
	
	public function consultarDebidosCreditos($tipo,$mes,$ano)//preenche o grafico da pagina inicial
	{
		$dataIncial = $ano."-".$mes."-01";
		$dataFinal = $ano."-".$mes."-31";
		switch($tipo)
		{
			case ("debitos"):
							{
								 
								$query = "SELECT SUM(valorCompraVendas) AS total 
										  FROM (vendas INNER JOIN pagamento ON vendas.idPagamento=pagamento.idPagamento)
										  WHERE dataCadastroPagamento >= '".$dataIncial."' AND dataCadastroPagamento <= '".$dataFinal."'
										  AND pagoPagamento='nao'";
								break;
							}
							
			case ("creditos"):
							{
								$query = "SELECT SUM(valorCompraVendas) AS total 
										  FROM (vendas INNER JOIN pagamento ON vendas.idPagamento=pagamento.idPagamento)
										  WHERE dataCadastroPagamento >= '".$dataIncial."' AND dataCadastroPagamento <= '".$dataFinal."'
										  AND pagoPagamento='sim'";
								break;
							}
		}
		
		$result = $this->sql->consulta($query);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["total"];
		}
		else
		{
			return "Error: ".$result;
		}	
	}
	
	public function debitosTotais()
	{
		$consultaDebitos = "SELECT SUM(valorTotalPagamento) as total FROM pagamento 
							WHERE pagoPagamento='nao'";
		$debitosTotais = null;
		$this->sql->consulta($consultaDebitos);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["total"];
		}
		else
		{
			//não existe pagamentos atrazados
			return 0;
		}
	}
	
	public function creditoTotais()
	{
		$consultaDebitos = "SELECT SUM(valorHistPagamento) as total FROM histpagamento 
							WHERE statusHistPagamento <> 1";
		$debitosTotais = null;
		$this->sql->consulta($consultaDebitos);
		if($this->sql->registros()>0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["total"];
		}
		else
		{
			//não existe pagamentos atrazados
			return 0;
		}	
	}
	
	public function retornaIdCli($nomeClientes)
	{
		$query = "SELECT * FROM clientes WHERE nomeClientes = '".$nomeClientes."'";
		$this->sql->consulta($query);
		if($this->sql->registros() > 0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["idClientes"];
		}
		else
		{
			//não existe cliente com este nome
		}
	}
	
	public function retornaIdVen($nomeClientes)
	{
		$query = "SELECT idVendas 
				  FROM clientes INNER JOIN vendas ON clientes.idClientes = vendas.idClientes 
		          WHERE nomeClientes = '".$nomeClientes."' ORDER BY idVendas DESC";
		$this->sql->consulta($query);
		if($this->sql->registros() > 0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["idVendas"];
		}
		else
		{
			//não existe cliente com este nome
		}
	}
	
	public function retornaIdPag($nomeClientes)
	{
		$query = "SELECT idPagamento 
				  FROM clientes INNER JOIN pagamento ON clientes.idClientes = pagamento.idClientes 
		          WHERE nomeClientes = '".$nomeClientes."' ORDER BY idPagamento DESC";
		$this->sql->consulta($query);
		if($this->sql->registros() > 0)
		{
			$dados = $this->sql->resultadoArray();
			return $dados["idPagamento"];
		}
		else
		{
			//não existe cliente com este nome
		}
	}
	
	public function retornaTotalHistPagamento($idClientes)
	{
		$query = "SELECT SUM(valorHistPagamento) AS totalHistPagamento FROM
				  (clientes INNER JOIN pagamento 
				  ON clientes.idClientes=pagamento.idClientes) 
				  INNER JOIN histpagamento 
				  ON pagamento.idPagamento=histpagamento.idPagamento
				  WHERE pagoPagamento='sim' AND clientes.idClientes='".$idClientes."' ";
		
		$this->sqlsql->consulta($query);
	
		if($this->sqlsql->registros()>0)
		{
			$dados = $this->sqlsql->resultadoArray();
			return $dados["totalHistPagamento"];
		}
		else
			return 0;
	}
	
	public function retornaTotalDevendo($idClientes)
	{
		$query = "SELECT SUM(valorTotalPagamento) AS totalPagamento FROM
				  (clientes INNER JOIN pagamento 
				  ON clientes.idClientes=pagamento.idClientes) 
				  WHERE pagoPagamento='nao' AND clientes.idClientes='".$idClientes."'";
		
		$this->sqlsql->consulta($query);
		
		if($this->sqlsql->registros()>0)
		{
			$dados = $this->sqlsql->resultadoArray();
			return $dados["totalPagamento"];
		}
		else
			return 0;
	}
	
	public function consultaClienteID($idClientes)
	{
		$query = "SELECT * FROM clientes WHERE idClientes = '".$idClientes."' ";
		$this->sql->consulta($query);
		return $this->sql->resultadoArray();
	}
	
	public function consultaClientePagamentoID($idClientes)
	{
		$query = "SELECT * FROM pagamento WHERE idClientes= '".$idClientes."' ORDER BY idPagamento DESC";
		$this->sql->consulta($query);
		return $this->sql->resultadoArray();
	}
	
	public function consultaFuncionarioID($idFuncionarios)
	{
		$query = "SELECT * FROM funcionarios WHERE idFuncionarios = '".$idFuncionarios."' ";
		$this->sql->consulta($query);
		return $this->sql->resultadoArray();
	}
}



?>