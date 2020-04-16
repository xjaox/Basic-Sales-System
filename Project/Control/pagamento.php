<?php
	
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/clientes.class.php");
	include ("../Model/pagamento.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	
	//variaveis e objetos globais 
	$paginaPagamento = new Template("../View/pagamento.htm");	
	$objRelatorio = new relatorio();
	$objClientes = new clientes();
	$objPagamento = new pagamento();
	$objLogin = new login();
	$objHoraAtual = new horaAtual();
	
	$nomeClientes;
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
							$paginaPagamento->nomeLogin = $nomeFuncionarios;
							$paginaPagamento->idLogin = $idLogin;
							$paginaPagamento->horaAtual = $objHoraAtual->horaAgora();
							$paginaPagamento->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$paginaPagamento->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$paginaPagamento->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
				
	//parte do autocomplete (baseia-se em criar um vetor de todos os dados existentes na tabela cliente selecionando somente os nomes)
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
		$paginaPagamento->vetorCities = $cities; // enviado para o html o vetor que será utilizado pelo javaScript
	}
	
	if(isset($_POST["proximo"])) // se apertou o botão proxima
	{
		if(isset($_POST["nomeCliente"]) && $_POST["nomeCliente"]!="") //testar se não esta vazioo input
		{
			$nomeClientes = $_POST["nomeCliente"];
			if($objRelatorio->verificaUmCliente($nomeClientes)>0) //testa se existe este cliente
			{
				if($objRelatorio->verificaSeClienteTemPagamento($nomeClientes)>0)
				{
					if(!isset($_SESSION)) session_start(); //inicia a sessão
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
				//cliente não existe.
				$paginaPagamento->corAlerta = "alerta_vermelho";
				$paginaPagamento->imagemAlerta = "<img src='../View/template/img/erro.png' > ";
				$paginaPagamento->menssagemAlerta = "Cliente não existe";
			}
		}
		else
		{
			//não preencheu o campo.
			$paginaPagamento->corAlerta = "alerta_amarelo";
			$paginaPagamento->imagemAlerta = "<img src='../View/template/img/aviso.png' > ";
			$paginaPagamento->menssagemAlerta = "Preencha o campo nome";
			//$paginaPagamento->quadroErro = "quadroAmarelo";
			//$paginaPagamento->erro = "Preencha o campo nome";
		}
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
	
	$paginaPagamento->show();
	
		
?>