<?php
	include ("../Model/Template.class.php");
	include ("../Model/funcionarios.class.php");
	include ("../Model/login.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/modificacao.class.php");
	include ("../Control/horaAtual.php");
	
	$cadastrarFuncionarios = new Template("../View/cadastrarFuncionarios.htm");
	$objFuncionarios = new funcionarios();
	$objLogin = new login();
	$objModificacao = new modificacao();
	$objRelatorio = new relatorio();
	$objHoraAtual = new horaAtual();
	
	if(!isset($_SESSION)) 
		session_start(); //inicia a sessão
	
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
							$cadastrarFuncionarios->nomeLogin = $nomeFuncionarios;
							$cadastrarFuncionarios->idLogin = $idLogin;
							$cadastrarFuncionarios->horaAtual = $objHoraAtual->horaAgora();
							$cadastrarFuncionarios->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$cadastrarFuncionarios->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$cadastrarFuncionarios->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
		
	if(isset($_GET["status"]) && $_GET["status"]==="ok")
	{
		 $cadastrarFuncionarios->corAlerta = "alerta_verde";
		 $cadastrarFuncionarios->imagemAlerta = "<img src='../View/template/img/ok.png'/>";
		 $cadastrarFuncionarios->menssagemAlerta = "Operação Efetuada com Sucesso";
  	}
	
	if(isset($_GET["editar"]))
	{
		$idFuncionarios = $_GET["editar"];
		
		//dados cadastro clientes
		$dados = $objRelatorio->consultaFuncionarioID($idFuncionarios);
		
		$cadastrarFuncionarios->nomeFuncionarios = $dados["nomeFuncionarios"];
		$cadastrarFuncionarios->apelidoFuncionarios = $dados["apelidoFuncionarios"];
		$cadastrarFuncionarios->enderecoFuncionarios = $dados["enderecoFuncionarios"];
		$cadastrarFuncionarios->numeroFuncionarios = $dados["numeroFuncionarios"];
		$cadastrarFuncionarios->bairroFuncionarios = $dados["bairroFuncionarios"];
		$cadastrarFuncionarios->cepFuncionarios = $dados["cepFuncionarios"];
		$cadastrarFuncionarios->cidadeFuncionarios = $dados["cidadeFuncionarios"];
		$cadastrarFuncionarios->estadoFuncionarios = $dados["estadoFuncionarios"];
		$cadastrarFuncionarios->cpfFuncionarios = $dados["cpfFuncionarios"];
		$cadastrarFuncionarios->rgFuncionarios = $dados["rgFuncionarios"];
		$cadastrarFuncionarios->dataNascimentoFuncionarios = $objRelatorio->inverterData($dados["dataNascimentoFuncionarios"],"-","/");
		$cadastrarFuncionarios->telefoneFuncionarios = $dados["telefoneFuncionarios"];
		$cadastrarFuncionarios->emailFuncionarios = $dados["emailFuncionarios"];
		$cadastrarFuncionarios->profissaoFuncionarios = $dados["profissaoFuncionarios"];
		
	}
	
	if(isset($_POST["cadastrar"])){
		$dataCadastroFuncionarios = date("Y/m/d H:i");
		$statusFuncionarios = 0;
		$dataNascimentoFuncionarios = $objRelatorio->inverterData($_POST["dataNascimentoFuncionario"],"/","-");
		$objFuncionarios->setFuncionarios($_POST["apelidoFuncionario"],$_POST["nomeFuncionario"],$_POST["enderecoFuncionario"],
								  (int) $_POST["numeroFuncionario"], $_POST["bairroFuncionario"],$_POST["cepFuncionario"],
								  $_POST["cidadeFuncionario"],$_POST["estadoFuncionario"],$_POST["cpfFuncionario"],
								  $_POST["rgFuncionario"],$dataCadastroFuncionarios,$_POST["telefoneFuncionario"],
								  $_POST["emailFuncionario"],$_POST["profissaoFuncionario"],$dataCadastroFuncionarios,
								  $statusFuncionarios);
		if(isset($_GET["editar"]) && $_GET["editar"] != "")
		{
			//faz atualização
			$resultadoInsercao = $objFuncionarios->alterarFuncionarios($idFuncionarios);
			
		}
		else
		{
			//faz novo cadastro
			$resultadoInsercao = $objFuncionarios->inserirFuncionarios();
		}
		
		if ($resultadoInsercao == 1)
		{
			$objModificacao->setModificacao(
										   0,0,
										   0,$idLogin,
										   "Pag: cadastrarFuncionarios.php | Operação: Cadastrar Funcionarios ",date("Y/m/d H:i"));
			$result = $objModificacao->inserirModificao();
			
			 header("Location: ../Control/cadastrarFuncionarios.php?idLogin=".$idLogin."&status=ok");
		}else{
			$cadastrarFuncioanrios->corAlerta = "alerta_vermelho";
			$cadastrarFuncionarios->imagemAlerta = "<img src='../View/template/img/erro.png' />";
			$cadastrarFuncionarios->menssagemAlerta = "OPS, algo deu errado! ".$resultadoInsercao;
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
	$cadastrarFuncionarios->show();
	
?>