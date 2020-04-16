<?php
	include("../Model/Template.class.php");
	include("../Model/relatorio.class.php");
	include("../Model/vendas.class.php");
	include("../Model/pagamento.class.php");
	include("../Model/login.class.php");
	include("../Model/modificacao.class.php");
	include("../Control/horaAtual.php");
	
	//variaveis e objetos globais
	$objVendasSegundaParte = new  Template("../View/vendasSegundaParte.htm");
	$objRelatorio = new relatorio();
	$objVendas = new vendas();
	$objPagamento = new pagamento();
	$objHoraAtual = new horaAtual();
	$objLogin = new login();
	$objModificacao =  new modificacao();
	
	$nomeCliente = NULL;
	$idClientes;
	$idPagamento;
	$limite;
	$dataVencimento;
	$nivel;
	//fim - variavies e objetos globais
	
	//inverte a data
    function inverterData($data, $separar = "-", $juntar = "/")
	{
		return implode($juntar, array_reverse(explode($separar,$data)));
	}
	//fim inverte a data
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
						$nivel = $objLogin->getNivel($idLogin);
						if($nomeFuncionarios != "") //nome diferente de vazio - Oooo cara logou no sistema. :)
						{
							//Links na tela
							$objVendasSegundaParte->nomeLogin = $nomeFuncionarios;
							$objVendasSegundaParte->horaAtual = $objHoraAtual->horaAgora();
							$objVendasSegundaParte->idLogin = $idLogin;
							$objVendasSegundaParte->configConta = "#";
							$objVendasSegundaParte->suporte = "#";
							$objVendasSegundaParte->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
			
							//preencher nome limites e vencimento 
							if(!isset($_SESSION)) session_start();
							if(isset($_SESSION))
							{
								if(isset($_GET["valor"]) && $_GET["valor"] != "")
								{
									$objVendasSegundaParte->valorCompra = $_GET["valor"];
								}
								
								if(isset($_SESSION["nomeCliente"]))//opção 1 - Cliente Existente
								{
									$nomeCliente = $_SESSION["nomeCliente"];
									$objVendasSegundaParte->nomeCliente = $nomeCliente;//mostra o nome do cliente
								}
								if(isset($_SESSION["nomeClienteNovo"]))//opção 2 - Cliente Novo
								{
									$nomeCliente = $_SESSION["nomeClienteNovo"];
									$objVendasSegundaParte->nomeCliente = $nomeCliente;//mostra o nome do cliente
								}
									$dados = $objRelatorio->vendasSegundaParte($nomeCliente);
									
									if($dados != 1 && $dados != 2)
									{
										$idClientes =  $dados["idClientes"];
										$idPagamento = $dados["idPagamento"];
										$dataVencimento = $dados["dataVencimentoPagamento"];
										$limite = $dados["limitePagamento"];
										
										$objVendasSegundaParte->limitePagamento = $limite;
										$objVendasSegundaParte->dataVencimentoPagamento = inverterData($dados['dataVencimentoPagamento']);
										
										$disponivel = (float) $dados['limitePagamento'] - (float) $dados['valorTotalPagamento']; 
										if($disponivel > 0);
										{
											$objVendasSegundaParte->valorDisponivel = $disponivel;
											$objVendasSegundaParte->corValorDisponivel = "verde";  
										}
										if($disponivel < 0)
										{
											$objVendasSegundaParte->valorDisponivel = $disponivel;
											$objVendasSegundaParte->corValorDisponivel = "vermelho";  
										}
										if($disponivel == 0)
										{
											$objVendasSegundaParte->valorDisponivel = $disponivel;
											$objVendasSegundaParte->corValorDisponivel = "Normal";  
										}
									}
									else
									{
										 echo "Erro: " .$dados; //2 - PAGAMENTO NÃO VINCULADO, 1 - CLIENTE INEXISTENTE
									}	
							}
							// fim - do preencher nome e limites 
							
							//parte do cadastro da venda	
							if(isset($_POST["voltar"]))
								header("Location: vendas.php?idLogin=".$idLogin);
							if(isset($_POST["finalizar"]))
							{	
								if(isset($_POST["valorCompra"],$_POST["pcVendas"]))
								{
									
								
									$valor =  $objRelatorio->converterValor($_POST["valorCompra"]);
									
									$pc =  (int) $_POST["pcVendas"];
									$objVendas->setVenda(1 ,$idClientes,$idPagamento,$valor,date("Y/m/d H:i"),0,$pc);
									
									if ($objVendas->inserirVenda() == 1)
									{
												if($dataVencimento < date("Y-m-d"))//isso é para fechar o pagamento se caso já estiver vencido ai gera outro id de pagamento
												{
													
													$inserirNovoPagamento = $objPagamento->setPagamento($idClientes,NULL,$limite,NULL,NULL,NULL,$objPagamento->alterarData($dataVencimento),date("Y/m/d H:i"),"nao");
													
													if($objPagamento->inserirPagamentoSimples() != 1)//se der erro entra aqui
													{
														$objVendasSegundaParte->corAlerta = "alerta_vermelho";
														$objVendasSegundaParte->imagemAlerta = "template/img/erro.png";
														$objVendasSegundaParte->menssagemAlerta = "Erro DB1: ". $objPagamento->inserirPagamentoSimples();							
													}
													$dados = $objRelatorio->vendasSegundaParte($nomeCliente);
													$idPagamento = $dados["idPagamento"];
													
												}
												
												$valorTotalCadastrado = $objPagamento->cadastrarValorPagamento($idPagamento,$valor);
												if($valorTotalCadastrado == 1)
												{
													$objModificacao->setModificacao(
																 $objRelatorio->retornaIdVen($nomeCliente),$idClientes,
																 $idPagamento,$idLogin,
																 "Pag: vendasSegundaParte.php | Operação: Vender ",date("Y/m/d H:i"));
													$result = $objModificacao->inserirModificao();
													
													unset($_SESSION["nomeCliente"]);
													unset($_SESSION["nomeClienteNovo"]);
													if(!isset($_SESSION)) 
														session_start();
													$_SESSION["vendaOk"] = "quadroVerde";
													header('Location: ../Control/vendas.php?idLogin='.$idLogin);
												}
									}
									else 
									{					
										$objVendasSegundaParte->corAlerta = "alerta_vermelho";
										$objVendasSegundaParte->imagemAlerta = "<img src='../View/template/img/erro.png' />";
										$objVendasSegundaParte->menssagemAlerta = "Erro DB2: ". $objVendas->inserirVenda();
									}
								}
								else
								{
									$objVendasSegundaParte->corAlerta = "alerta_vermelho";
									$objVendasSegundaParte->imagemAlerta = "<img src='../View/template/img/erro.png' />";
									$objVendasSegundaParte->menssagemAlerta = "Preencha o campo Valor da Compra";
								}
							}
							
							//botoes
							
							//fim botoes

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
	$objVendasSegundaParte->show();
	
?>