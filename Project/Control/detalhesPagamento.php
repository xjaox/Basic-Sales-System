<?php
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/vendas.class.php");
	include ("../Model/clientes.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	
	
	//objetos 
	$detalhesVendas = new Template("../View/detalhesPagamento.htm");
	$objVendas = new vendas();
	$objRelatorio = new relatorio();
	$objClientes = new clientes();
	$objLogin = new login();
	$objHoraAtual = new horaAtual();
	
	// - fim objetos
	
	//variaveis
	$nomeClientes = null;
	$idPagamento = 0;
	$idClientes = 0;
	$tabelaDetalhes = null;
	$totalCompras = 0;
	$corBloco = 1;
	// - fim variaveis
	
	if(!isset($_SESSION))
		session_start();
	
	if(isset($_GET["idLogin"]))
	{
		$idLogin = $_GET["idLogin"];
		switch($objLogin->verificaLogin($idLogin))
		{
			case 1 :{
						//erro no comando sql
						header("Location: ../Control/logar.php");
						break;
					}
					
			case 2: {
						//erro na sessão
						header("Location: ../Control/logar.php");
						break;
					}
					
			default:{
						//se ok mostra o nome
						$nomeFuncionarios = $objLogin->verificaLogin($idLogin);
						
						if($nomeFuncionarios != "") //nome diferente de vazio - Oooo cara logou no sistema. :)
						{
							//Links na tela
							$detalhesVendas->nomeLogin = $nomeFuncionarios;
							$detalhesVendas->idLogin = $idLogin;
							$detalhesVendas->horaAtual = $objHoraAtual->horaAgora();
							$detalhesVendas->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$detalhesVendas->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$detalhesVendas->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
			
	if(isset($_SESSION))
	{
		if(isset($_SESSION["nomeClientes"]))
		{
			$nomeClientes = $_SESSION["nomeClientes"]; 
			$detalhesVendas->nomeClientes = $nomeClientes;
			if(isset($_GET["idPagamento"]))
			{
				$idPagamento = $_GET["idPagamento"];
				if($objRelatorio->verificaUmCliente($nomeClientes) > 0)
				{
					if($dados = $objRelatorio->mostraListar())
					{
						$idClientes = $dados["idClientes"];
						if($objRelatorio->detalhesVendas($idClientes,$idPagamento) >0)
						{
							
							while ($dados = $objRelatorio->mostraListar())
							{
								$detalhesVendas->idVendas = $dados["idVendas"];
								$detalhesVendas->valorCompra = $dados["valorCompraVendas"];
								$detalhesVendas->dataCompra = $objRelatorio->inverterData($dados["dataVendas"],"-","/"); 
								$totalCompras += $dados["valorCompraVendas"];
								$idClientes = $dados["idClientes"];
								$nomeClientes = $dados["nomeClientes"];
								$telefoneClientes = $dados["telefoneClientes"];
								
								if ($corBloco == "blocoCinza")
									$corBloco = "blocoBranco";
								else $corBloco = "blocoCinza";
								$detalhesVendas->corBloco = $corBloco;
								
								$detalhesVendas->block("BLOCK_DETAILSPAG");
								
							}
							
							
							$detalhesVendas->idClientes = $idClientes;
							$detalhesVendas->nome = $nomeClientes;
							$detalhesVendas->telefone = $telefoneClientes;
							$detalhesVendas->totalPagamento = $totalCompras;
						}
						{
							//não existe vendas para este cliente
						}
					}
					else
					{
						//algum erro na consulta de clientes (camando sql)
					}
				}
				else
				{
					//esse cliente não existe
				}
			}
			else
			{
				//não veio o numero de paginas
				
			}
		}
		else
		{
			//nome do cliente não existe na função
		}
	}
	else
	{
		//sessão não inciada
	}
				}
				else
				{
					header("Location: ../Control/logar.php");
				}
			}
		}
	}
	else
	{
		header("Location: ../Control/logar.php");
	}
	
	$detalhesVendas->show();
?>