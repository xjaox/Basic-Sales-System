<?php
	include ("../Model/Template.class.php");
	include ("../Model/login.class.php");
	include ("../Model/relatorio.class.php");
	include("../Control/horaAtual.php");
	
	
	
	$vendaRapida = new Template("../View/vendaRapida.html");
	$objLogin = new login();
	$objRelatorio = new relatorio();
	$objHoraAtual = new horaAtual();
	
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
						
						
						if($nomeFuncionarios != "") //nome diferente de vazio - Oooo cara logou no sistema. :)
						{
							//Links na tela
							$vendaRapida->nomeLogin = $nomeFuncionarios;
							//$vendaRapida->idLogin = $idLogin;
							$vendaRapida->horaAtual = $objHoraAtual->horaAgora();
							$vendaRapida->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$vendaRapida->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$vendaRapida->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
							
							if(isset($_POST["nome"],$_POST["codigo"]) && ($_POST["nome"] != "" && $_POST["codigo"] != "") )
							{
								$vendaRapida->corAlerta = "alerta_vermelho";
								$vendaRapida->imagemAlerta = "<img src='../View/template/img/erro.png' />";
								$vendaRapida->menssagemAlerta = "Preencha apenas um campo para a consulta!";
							}
							else
							{
								if(isset($_POST["valorPagamento"]) && $_POST["valorPagamento"] != "")	
								{
									$valor = $_POST["valorPagamento"];
									if(isset($_POST["nome"]) && $_POST["nome"] != "")
									{										
									  $nomeClientes = $_POST["nome"];							  
									  if($objRelatorio->verificaUmCliente($nomeClientes) > 0)//verifica se esse cliente existe
									  {
										  if(!isset($_SESSION))
											  session_start();
										  $_SESSION['nomeCliente'] = $nomeClientes;
										  header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin.'&valor='.$valor);
									  }
									  else
									  {
										  $vendaRapida->corAlerta = "alerta_vermelho";
										  $vendaRapida->imagemAlerta = "<img src='../View/template/img/erro.png' />";
										  $vendaRapida->menssagemAlerta = "Nome incorreto ou Usuário inexistente!";
									  }
									}
									
									if(isset($_POST["codigo"]) && $_POST["codigo"] != "")
									{										
									  $idClientes = $_POST["codigo"];							  
									  if($dados = $objRelatorio->consultaClienteID($idClientes))//verifica se esse cliente existe
									  {
										  if(!isset($_SESSION))
											  session_start();
										  $_SESSION['nomeCliente'] = $dados["nomeClientes"];
										  header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin.'&valor='.$valor);
									  }
									  else
									  {
										  $vendaRapida->corAlerta = "alerta_vermelho";
										  $vendaRapida->imagemAlerta = "<img src='../View/template/img/erro.png' />";
										  $vendaRapida->menssagemAlerta = "Codigo Invalido!";
									  }
									}
								}
								else
								{
									$vendaRapida->corAlerta = "alerta_vermelho";
									$vendaRapida->imagemAlerta = "<img src='../View/template/img/erro.png' />";
									$vendaRapida->menssagemAlerta = "Preencha o valor!";
								}
							}
							
							
							$vendaRapida->show();

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
		header("Looation: ../Control/logar.php");
	}
	
?>
	