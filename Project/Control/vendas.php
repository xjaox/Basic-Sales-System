<?php
	
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/clientes.class.php");
	include ("../Model/pagamento.class.php");
	include ("../Model/login.class.php");
	include ("../Model/modificacao.class.php");
	include ("../Control/horaAtual.php");
	
	
	
	//variaveis e objetos globais 
	$paginaVendas = new Template("../View/vendas.htm");	
	$objRelatorio = new relatorio();
	$objClientes = new clientes();
	$objPagamento = new pagamento();
	$objLogin = new login();
	$objModificacao = new modificacao();
	$objHoraAtual = new horaAtual();
	$nomeCliente = NULL;
	//fim variaveis e objetos globais
	
	
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
						echo "erro no comando sql";
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
							$paginaVendas->nomeLogin = $nomeFuncionarios;
							$paginaVendas->horaAtual = $objHoraAtual->horaAgora();
							$paginaVendas->idLogin = $idLogin;
							$paginaVendas->configConta = "#";
							$paginaVendas->suporte = "#";
							$paginaVendas->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
		
	
							if(isset($_SESSION["nomeCliente"]))
								unset($_SESSION["nomeCliente"]);
							if(isset($_SESSION["nomeClienteNovo"]))
								unset($_SESSION["nomeClienteNovo"]);
								
							if(isset($_SESSION["vendaOk"]))
							{	
								$paginaVendas->corAlerta = "alerta_verde";
								$paginaVendas->imagemAlerta = "<img src='../View/template/img/ok.png' />";
								$paginaVendas->menssagemAlerta = "Venda Efetuada com Sucesso!";
								unset($_SESSION["vendaOk"]);	
							}
							
							//parte do autocomplete (baseia-se em criar um 
							//vetor de todos os dados existentes na tabela cliente selecionando somente os nomes)
							$cont = 0;
							$quantidadeTotal = $objRelatorio->autoComplete();
							
							if($quantidadeTotal>0) // se consultou ok
							{	
								for($i=0; $i<$quantidadeTotal; $i++)
								{
									$dados = $objRelatorio->mostraListar();
									$vetor[$i] = $dados["nomeClientes"];
								}
								$cities = implode("|",$vetor);
								$paginaVendas->vetorCities = $cities; // enviado para o html o vetor que será utilizado pelo javaScript
							}
							
							//fim do autocomplete
							
							//verificando qual opção o cliente selecionou
							if(isset($_POST["opcao"]))
							{
								if($_POST["opcao"] == "opcao1")// cliente já existente
								{
									if(isset($_POST["nomeCliente"]) && $_POST["nomeCliente"] != "")//verifica se o input text não está vazio
									{
										$nomeCliente = $_POST["nomeCliente"];
										if($objRelatorio->verificaUmCliente($nomeCliente) > 0)//verifica se esse cliente existe
										{
											session_start();
											$_SESSION['nomeCliente'] = $nomeCliente;
											header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin);
										}
										else
										{
											$paginaVendas->corAlerta = "alerta_vermelho";
											$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
											$paginaVendas->menssagemAlerta = "Usuário não existe!";
										}
									}
									else 
									{
										$paginaVendas->corAlerta = "alerta_amarelo";
										$paginaVendas->imagemAlerta = "<img src='template/img/aviso.png' />";
										$paginaVendas->menssagemAlerta = "Preencha o campo!";
									}
								}
								if($_POST["opcao"] == "opcao2")
								{
									if(isset($_POST["nomeClienteNovo"]) && $_POST["nomeClienteNovo"] != "" &&
									   isset($_POST["apelidoCliente"]) && $_POST["apelidoCliente"] != "" &&
									   isset($_POST["limitePagamento"]) && $_POST["limitePagamento"] != "" &&
									   isset($_POST["vencimentoPagamento"]) && $_POST["vencimentoPagamento"] != "")//verifica se os input text não estão vazios
									{
									
										$objClientes->setClientes($_POST["apelidoCliente"],$_POST["nomeClienteNovo"],null,null,null,null,null,null,
															  null,null,null,null,null,null,null,0);
										if($objClientes->inserirClientesSimples() == 1 )
										{	
											
											$vencimentoPagamento = $objPagamento->inverterData($_POST["vencimentoPagamento"],"/","-");
											
											$objPagamento->setPagamento($objRelatorio->retornaIdCli($_POST["nomeClienteNovo"]),NULL,$_POST["limitePagamento"],
																		NULL,NULL,$vencimentoPagamento,date("Y/m/d H:i"),"nao");
											//echo $objPagamento->getVencimento();							
											if($objPagamento->inserirPagamentoSimples() == 1)
											{
												$objModificacao->setModificacao(
																 0,$objRelatorio->retornaIdCli($_POST["nomeClienteNovo"]),
																 $objRelatorio->retornaIdPag($_POST["nomeClienteNovo"]),$idLogin,
																 "Pag: vendas.php | Operação: Cliente Novo",date("Y/m/d H:i"));
												$result = $objModificacao->inserirModificao();
											
												if(!isset($_SESSION))
													session_start();
												$_SESSION['nomeClienteNovo'] = $_POST["nomeClienteNovo"];
												header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin);
											}
											else
											{
												$paginaVendas->corAlerta = "alerta_vermelho";
												$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
												$paginaVendas->menssagemAlerta = "Ocorreu um erro nos dados, Limite e Vencimento: ".$objPagamento->inserirPagamentoSimples();
											}
										}
										else 
										{
											$paginaVendas->corAlerta = "alerta_vermelho";
											$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
											$paginaVendas->menssagemAlerta = "Ocorreu um erro nos dados, Apelido e Nome : ". $objClientes->inserirClientes();
										}			
									}
									else 
									{
										$cadastrarClientes->corAlerta = "alerta_amarelo";
										$cadastrarClientes->imagemAlerta = "<img src='template/img/aviso.png' />";
										$cadastrarClientes->menssagemAlerta = "Preencha os campos";
									}
								}
							}
						}
						else//fecha o if = condição nomeFuncionarios != ""
						{
							header("Location: ../Control/logar.php");
						}
						
					}//fecha o default
		}//fecha o switch 
	}//fecha o primeiro if = condição isset($_GET["idLogin"]		
	else
	{
		header("Location: ../Control/logar.php");
	}
	$paginaVendas->show();
?>