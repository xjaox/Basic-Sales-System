<?php
	include ("../Model/Template.class.php");
	include ("../Model/login.class.php");
	include ("../Model/relatorio.class.php");
	include("../Control/horaAtual.php");
	
	//objetos 
	$systemCaixa = new Template("../View/systemCaixa.html");
	$objLogin = new login();
	$objRelatorio = new relatorio();
	$objHoraAtual = new horaAtual();
	// - fim objetos
	
	//variaveis
	$idLogin = null;
	$nomeFuncionarios = null;

	
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
							if($nivel == 0)
							{
								//Links na tela
								$systemCaixa->nomeLogin = $nomeFuncionarios;
								$systemCaixa->idLogin = $idLogin;
								$systemCaixa->horaAtual = $objHoraAtual->horaAgora();
								
								$systemCaixa->configConta = "principal.php?idLogin=".$idLogin."&configConta";
								$systemCaixa->suporte = "principal.php?idLogin=".$idLogin."&suporte";
								$systemCaixa->sair = "principal.php?idLogin=".$idLogin."&sair";
								//fim links na tela
							
								$systemCaixa->show();
							}
							else
							{
								//usario não é bom logar nessa pagina pois não tem utilizada para ele
								//ex: administrador e tal
								header("Location: ../Control/principal.php?idLogin=".$idLogin);
							}
						}
						else
						{
							// user não logou
						}
			}
		}
	}
	else
	{
		//user não logou
	}
?>