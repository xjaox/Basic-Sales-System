<?php
	include ("../Model/Template.class.php");
	include ("../Model/login.class.php");
	include ("../Model/relatorio.class.php");
	include("../Control/horaAtual.php");
	
	//objetos 
	$erro = new Template("../View/erro.htm");
	$objLogin = new login();
	$objRelatorio = new relatorio();
	$objHoraAtual = new horaAtual();
	// - fim objetos
	
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
							$erro->nomeLogin = $nomeFuncionarios;
							$erro->idLogin = $idLogin;
							$erro->horaAtual = $objHoraAtual->horaAgora();
							$erro->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$erro->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$erro->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
							$erro->show();
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