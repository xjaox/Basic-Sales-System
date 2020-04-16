<?php
	include("../Model/Template.class.php");
	include("horaAtual.php");
	include("../Model/login.class.php");
	
	$logar = new Template("../View/logar.htm");
	$objLogin = new login();
	$horaAtual = new horaAtual();
	
	if(isset($_POST["entrar"]))
	{
		$resultadoLogin = $objLogin->logar($_POST["usuario"],$_POST["senha"]);	
		switch($resultadoLogin){
			case(1):{
				$logar->corAlerta = "alerta_amarelo";
				$logar->imagemAlerta = "<img src='../View/template/img/aviso.png' >";
				$logar->menssagemAlerta = "Você estava logado, registramos sua saída. Tente novamente!";
			break;
			}
			case(2):{
				$logar->corAlerta = "alerta_vermelho";
				$logar->imagemAlerta = "<img src='../View/template/img/erro.png' > ";
				$logar->menssagemAlerta = "Login ou Senha Incorreto!";
			break;	
			}
			case(3):{
				$logar->corAlerta = "verde";
				$logar->imagemAlerta = "<img src='../View/template/img/ok.png' >";
				$logar->menssagemAlerta = "Entrada Registrada";
			break;	
			}
			default: $resultadoLogin;
		}
	}
	
	$logar->horaAtual = $horaAtual->horaAgora();
	$logar->show();
?>