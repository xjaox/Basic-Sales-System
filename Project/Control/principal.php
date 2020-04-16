<?php
	include ("../Model/Template.class.php");
	include ("../Model/login.class.php");
	include ("../Model/relatorio.class.php");
	include("../Control/horaAtual.php");
	
	//objetos 
	$principal = new Template("../View/principal.htm");
	$objLogin = new login();
	$objRelatorio = new relatorio();
	$objHoraAtual = new horaAtual();
	// - fim objetos
	
	//variaveis
	$idLogin = null;
	$nomeFuncionarios = null;
	$nivel = null;
	$porcento = 0;
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
						echo "erro ao sair";
						break;
					}
					
			default:{
						//se ok mostra o nome
						$nomeFuncionarios = $objLogin->verificaLogin($idLogin);
						$nivel = $objLogin->getNivel($idLogin);
						if($nivel == 0)
						{
							header("Location: ../Control/systemCaixa.php?idLogin=".$idLogin);
						}
						
						if($nomeFuncionarios != "") //nome diferente de vazio - Oooo cara logou no sistema. :)
						{
							//Links na tela
							$principal->nomeLogin = $nomeFuncionarios;
							$principal->idLogin = $idLogin;
							$principal->horaAtual = $objHoraAtual->horaAgora();
							$principal->versao = "Versão: 1.1";
							$principal->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$principal->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$principal->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
							
							//Preenchimento dos graficos
							$dataAtual = date("Y-m-d");
							$mesAtual = substr($dataAtual,5,2);
							$anoAtual = substr($dataAtual,0,4);
							if($nivel>0){
							for ($i = 0; $i<=8; $i++)
							{
								$mesBusca = $mesAtual - $i;
								$porcCreditos = 98;
								$porcDebitos = 98;
								
								if($mesBusca <= 0) // ano passado 
								{
									$mesBusca +=12;
									$anoBusca = $anoAtual - 1;
									if($mesBusca < 10)//colocar o zero antes para ficar legal a data ex: 9=09
									{
										$mesBusca = "0".$mesBusca;										
										$mesExtenso = $objRelatorio->mesExtenso($mesBusca);									
									}
									else
									{
										$mesExtenso = $objRelatorio->mesExtenso($mesBusca);
									}
									
									$debitos = $objRelatorio->consultarDebidosCreditos("debitos",$mesBusca,$anoBusca);
									$creditos = $objRelatorio->consultarDebidosCreditos("creditos",$mesBusca,$anoBusca);
									
									//calculo dos graficos
									$totalDebitosCreditos = $debitos + $creditos;
									if($totalDebitosCreditos != "")
									{
										$porcDebitos = ($debitos*100)/$totalDebitosCreditos;
										$porcCreditos = ($creditos*100)/$totalDebitosCreditos;
									}
									// fim calculo dos graficos
									
									//colocando as datas no grafico
									$j = $i + 1;
									$data = "data".$j;
									$red = "red".$j;
									$green = "green".$j;
									if($porcCreditos == 100)
										$porcCreditos = 98;
									if($porcDebitos == 100)
										$porcDebitos = 98;
										
									$principal->$red = $porcCreditos;
									$principal->$green = $porcDebitos;
									
									$principal->$data = $mesExtenso ."/".$anoBusca; 
									
								}
								else //ano atual 
								{
									if($mesBusca < 10)//colocar o zero antes para ficar legal a data ex: 9=09
									{
										$mesBusca = "0".$mesBusca;
										$mesExtenso= $objRelatorio->mesExtenso($mesBusca);
									}
									else
									{
										$mesExtenso = $objRelatorio->mesExtenso($mesBusca);
									}
									$debitos = $objRelatorio->consultarDebidosCreditos("debitos",$mesBusca,$anoAtual);
									$creditos = $objRelatorio->consultarDebidosCreditos("creditos",$mesBusca,$anoAtual);
									
									//calculo dos graficos
									$totalDebitosCreditos = $debitos + $creditos;
									if($totalDebitosCreditos != "")
									{
										$porcDebitos = ($debitos*100)/$totalDebitosCreditos;
										$porcCreditos = ($creditos*100)/$totalDebitosCreditos;
									}
									//echo "<br>".$i." - Porc Debitos: ".$porcDebitos." --- Porc Creditos: ".$porcCreditos;
									// fim calculo dos graficos
									
									//colocando as datas no grafico
									$j = $i + 1;
									$data = "data".$j;
									$red = "red".$j;
									$green = "green".$j;
									if($porcCreditos == 100)
										$porcCreditos = 98;
									if($porcDebitos == 100)
										$porcDebitos = 98;
									$principal->$red = $porcCreditos;
									$principal->$green = $porcDebitos;
									
									$principal->$data = $mesExtenso ."/".$anoAtual; 
									
								}
							}}//if nivel > 0
							//fim preenchimentos dos graficos
							
							//preenchendo debitos hoje
							$cinza = "class='cinza'";
							$resultConsulta = $objRelatorio->consultaListarDebitosHoje(0,5);
							$dados = NULL;
							if($resultConsulta >0)
							{
								while($dados = $objRelatorio->mostraListar())
								{
									
									$principal->nomeClientesDH = $dados["nomeClientes"];
									$principal->enderecoClientesDH = $dados["enderecoClientes"];
									if($cinza != "")
										$cinza = "";
									else $cinza = "class='cinza'";
									$principal->estiloBlock = $cinza;
									
									$principal->block("BLOCK_DEBITOSHOJE");
								}
							}
							else
							{
								//não existe debitoshoje então não precisa mostrar nenhuma mensagem.
							}
							//fim debitos hoje
							
							//preenchendo debitos
							$cinza = "class='cinza'";
							$resultConsulta = $objRelatorio->consultaListarDebitos(0,5);
							$dados = NULL;
							if($resultConsulta >0)
							{
								while($dados = $objRelatorio->mostraListar())
								{
									
									$principal->nomeClientesD = $dados["nomeClientes"];
									$principal->enderecoClientesD = $dados["enderecoClientes"];
									if($cinza != "")
										$cinza = "";
									else $cinza = "class='cinza'";
									$principal->estiloBlock = $cinza;
									
									$principal->block("BLOCK_DEBITOS");
								}
							}
							else
							{
								//não existe debitoshoje então não precisa mostrar nenhuma mensagem.
							}
							//fim debitos
							
							//preechendo contas pagas
							$cinza = "class='cinza'";
							$resultConsulta = $objRelatorio->consultaListarCreditos(0,5);
							$dados = NULL;
						
							if($resultConsulta >0)
							{
								while($dados = $objRelatorio->mostraListar())
								{
									
									$principal->nomeClientesCP = $dados["nomeClientes"];
									$principal->enderecoClientesCP = $dados["enderecoClientes"];
									if($cinza != "")
										$cinza = "";
									else $cinza = "class='cinza'";
									$principal->estiloBlock = $cinza;
									
									$principal->block("BLOCK_CONTASPAGAS");
								}
							}
							else
							{
								//não existe debitoshoje então não precisa mostrar nenhuma mensagem.
							}
							//fim contas pagas
							
							//colocando os valores totais de creditos e debitos
							if($nivel>0)
							{
								$principal->valorDebitos = $objRelatorio->arredondar_dois_decimal($objRelatorio->debitosTotais());
								$principal->valorCreditos = $objRelatorio->arredondar_dois_decimal($objRelatorio->creditoTotais());
							}
							//fim
							
							//autoComplete
							$quantidadeTotal = $objRelatorio->autoComplete();
							
							if($quantidadeTotal>0) // se consultou ok
							{	
								for($i=0; $i<$quantidadeTotal; $i++)
								{
									$dados = $objRelatorio->mostraListar();
									$vetor[$i] = $dados["nomeClientes"];
									$vetorr[$i] = $dados["nomeClientes"];
								}
								
								$cities = implode("|",$vetor);
								$citiess = implode("|",$vetorr);
								$principal->vetorCities = $cities; // enviado para o html o vetor que será utilizado pelo javaScript
								$principal->vetorCitiess = $citiess;
							}
							// fim - autoComplete
							
							//acoes dos botoes
							
							//ação do menu principal lateral
							if(isset($_GET["pagina"]))
							{
								$pagina = $_GET["pagina"];
								
								switch($pagina)
								{
									case("vender"):
											{
												header("Location: ../Control/vendas.php?idLogin=".$idLogin);
												break;
											}
									case("cobrar"):
											{
												header("Location: ../Control/pagamento.php?idLogin=".$idLogin);
												break;
											}
									case("clientes"):
											{
												header("Location: ../Control/cadastrarClientes.php?idLogin=".$idLogin);
												break;
											}
									case("funcionarios"):
											{
												if($nivel>0)
												{
													header("Location: ../Control/cadastrarFuncionarios.php?idLogin=".$idLogin);
												}
												else
												{
													header("Location: ../Control/erro.php?idLogin=".$idLogin);
												}
												break;
											}
									case("listarClientes"):
											{
												header("Location: ../Control/listarClientes.php?idLogin=".$idLogin);
												break;
											}
									case("listarFuncionarios"):
											{
												if($nivel>0)
												{
														header("Location: ../Control/listarFuncionarios.php?idLogin=".$idLogin);
												}
												else
												{
													header("Location: ../Control/erro.php?idLogin=".$idLogin);
												}
												break;
											}
									case("listarDebitos"):
											{
												header("Location: ../Control/listarDebitos.php?idLogin=".$idLogin);
												break;
											}
									case("listarDebitosHoje"): 
											{
												header("Location: ../Control/listarDebitosHoje.php?idLogin=".$idLogin);
												break;
											}
									case("listarCreditos"):
											{
												if($nivel>0)
												{
														header("Location: ../Control/listarCreditos.php?idLogin=".$idLogin);
												}
												else
												{
													header("Location: ../Control/erro.php?idLogin=".$idLogin);
												}
												break;
											}
								}
							}
							//fim ação menu principal lateral
							
							//ações dos botoes
							if(isset($_POST["vender"]) && $_POST["vender"] != "")
							{
								$nomeClientes = $_POST["vender"];
								
								if($objRelatorio->verificaUmCliente($nomeClientes) > 0)//verifica se esse cliente existe
								{
									if(!isset($_SESSION))
										session_start();
									$_SESSION['nomeCliente'] = $nomeClientes;
									header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin);
								}
								else
								{
									$paginaVendas->corAlerta = "alerta_vermelho";
									$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
									$paginaVendas->menssagemAlerta = "Usuário não existe!";
								}
								
								
							}
							
							if(isset($_POST["receber"]) && $_POST["receber"] != "")
							{
								$nomeClientes = $_POST["receber"];
								
								if($objRelatorio->verificaUmCliente($nomeClientes) > 0)//verifica se esse cliente existe
								{
									if($objRelatorio->verificaSeClienteTemPagamento($nomeClientes)>0)
									{
										if(!isset($_SESSION)) 
											session_start(); //inicia a sessão
										if(isset($_SESSION["nomeClientes"]))
												unset($_SESSION["nomeClientes"]); //se existir alguma coisa retira 
										
										$_SESSION["nomeClientes"] = $nomeClientes;
										//Tudo ocorreu bem.
										$paginaPagamento->corAlerta = "alerta_verde";
										$paginaPagamento->imagemAlerta = "<img src='../View/template/img/ok.png' > ";
										$paginaPagamento->menssagemAlerta = "Este cliente possui dividas";
										
										header('Location: ../Control/pagamentoSegundaParte.php?idLogin='.$idLogin);
									}
									else
									{
										//cliente não possui pagamento.
										$paginaPagamento->corAlerta = "alerta_amarela";
										$paginaPagamento->imagemAlerta = "<img src='../View/template/img/aviso.png' > ";
										$paginaPagamento->menssagemAlerta = "Este cliente não possui dividas";
									}
								}
								else
								{
									$paginaVendas->corAlerta = "alerta_vermelho";
									$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
									$paginaVendas->menssagemAlerta = "Usuário não existe!";
								}
							}
							
							
							
							if(isset($_GET["sair"])) //clicou no botão sair.
							{
								if($objLogin->logoff($nomeFuncionarios)==1)
								{
									//saida registrada com sucesso
									header("Location: ../Control/logar.php");
								}
								else
								{
									//erro na saida
									echo "erro ao sair";
								}
							}
							//fim acoes dos botoes
							
							
							$principal->show();
						}
						else
						{
							//o login esta incorreto, ou usuario tentando acessar essa pagina indevidamente.
							//o cara não logou no sistema.
							header("Location: ../Control/logar.php");
						}
						
					}
		}
	}
	else
	{
		//o cara não logou no sistema.
		header("Location: ../Control/logar.php");
		
	}
	
	
	
?>