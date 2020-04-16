<?php
	include ("../Model/Template.class.php");
	include ("../Model/clientes.class.php");
	include ("../Model/login.class.php");
	include ("../Model/pagamento.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/modificacao.class.php");
	include ("../Control/horaAtual.php");
	
	$cadastrarClientes = new Template("../View/cadastrarClientes.htm");
	$objClientes = new clientes();
	$objPagamento = new pagamento();
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
							$cadastrarClientes->nomeLogin = $nomeFuncionarios;
							$cadastrarClientes->idLogin = $idLogin;
							$cadastrarClientes->horaAtual = $objHoraAtual->horaAgora();
							$cadastrarClientes->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$cadastrarClientes->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$cadastrarClientes->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
	
	if(isset($_GET["status"]) && $_GET["status"]==="ok")
	{
		 $cadastrarClientes->corAlerta = "alerta_verde";
		 $cadastrarClientes->imagemAlerta = "<img src='../View/template/img/ok.png'/>";
		 $cadastrarClientes->menssagemAlerta = "<a href=../Control/vendasSegundaParte.php?idLogin=".$idLogin.">
												 Operação efetuada com Sucesso! Deseja vender para este cliente? </a>";
  	}
	if(isset($_GET["editar"]))
	{
		$idClientes = $_GET["editar"];
		
		
		//dados cadastro clientes
		$dados = $objRelatorio->consultaClienteID($idClientes);
		
		$cadastrarClientes->nomeCliente = $dados["nomeClientes"];
		$cadastrarClientes->apelidoCliente = $dados["apelidoClientes"];
		$cadastrarClientes->enderecoCliente = $dados["enderecoClientes"];
		$cadastrarClientes->numeroCliente = $dados["numeroClientes"];
		$cadastrarClientes->bairroCliente = $dados["bairroClientes"];
		$cadastrarClientes->cepCliente = $dados["cepClientes"];
		$cadastrarClientes->cidadeCliente = $dados["cidadeClientes"];
		$cadastrarClientes->estadoCliente = $dados["estadoClientes"];
		$cadastrarClientes->cpfCliente = $dados["cpfClientes"];
		$cadastrarClientes->rgCliente = $dados["rgClientes"];
		$cadastrarClientes->dataNascimentoCliente = $objRelatorio->inverterData($dados["dataNascimentoClientes"],"-","/");
		$cadastrarClientes->telefoneCliente = $dados["telefoneClientes"];
		$cadastrarClientes->emailCliente = $dados["emailClientes"];
		$cadastrarClientes->profissaoCliente = $dados["profissaoClientes"];
		
		//dados pagamento clientes
		$dados = $objRelatorio->consultaClientePagamentoID($idClientes);
		
		$cadastrarClientes->dataVencimento = $objRelatorio->inverterData($dados["dataVencimentoPagamento"],"-","/");
		$cadastrarClientes->limite = $dados["limitePagamento"];
		
	}
	
	if(isset($_POST["cadastrar"]))
	{
	if(isset($_POST["nomeCliente"]) && $_POST["nomeCliente"]!="")
	{
		$dataCadastroCliente = date("Y/m/d H:i");
		$statusCliente = 0;
		if($_POST["dataNascimentoCliente"] == "")
		{
			$_POST["dataNascimentoCliente"] = "00/00/0000";
		}
		
		$objClientes->setClientes($_POST["apelidoCliente"],$_POST["nomeCliente"],$_POST["enderecoCliente"],
								  (int) $_POST["numeroCliente"], $_POST["bairroCliente"],$_POST["cepCliente"],
								  $_POST["cidadeCliente"],$_POST["estadoCliente"],$_POST["cpfCliente"],
								  $_POST["rgCliente"],$_POST["dataNascimentoCliente"],$_POST["telefoneCliente"],
								  $_POST["emailCliente"],$_POST["profissaoCliente"],$dataCadastroCliente,		$statusCliente);
		
		if(isset($_GET["editar"]) && $_GET["editar"] != "")
		{
			//faz atualização
			$resultadoInsercao = $objClientes->alterarClientes($idClientes);
		}
		else
		{
			//faz inserção
			$resultadoInsercao = $objClientes->inserirClientes();
		}
		
		
		if ($resultadoInsercao == 1)
		{
			//inseriu o cliente ok
			if(isset($_POST["dataVencimento"],$_POST["nomeCliente"],$_POST["limite"]) && 
				$_POST["dataVencimento"] != "" && $_POST["nomeCliente"] != "" && $_POST["limite"] != "" )
			{
			  $vencimentoPagamento = $objPagamento->inverterData($_POST["dataVencimento"],"/","-");
		  
			  $objPagamento->setPagamento($objRelatorio->retornaIdCli($_POST["nomeCliente"]),NULL,$objRelatorio->converterValor($_POST["limite"]),
										  NULL,NULL,$vencimentoPagamento,date("Y/m/d H:i"),"nao");
	
			  if(isset($_GET["editar"]) && $_GET["editar"] != "")
			  {
					//faz atualização
				  $resultadoPagamento = $objPagamento->alterarPagamentoSimples($idClientes);	
			  }
			  else
			  {
				  //faz inserção
				  $resultadoPagamento = $objPagamento->inserirPagamentoSimples();			
			  }
			  
			  if($resultadoPagamento == 1)
			  {
				  if(!isset($_SESSION))
				  	session_start();
				  $_SESSION['nomeCliente'] = $_POST["nomeCliente"];
			  
			  	  $objModificacao->setModificacao(
												 0,$objRelatorio->retornaIdCli($_POST["nomeCliente"]),
												 $objRelatorio->retornaIdPag($_POST["nomeCliente"]),$idLogin,
												 "Pag: cadastraClientes.php | Operação: cadastrar clientes ",date("Y/m/d H:i"));
				  $result = $objModificacao->inserirModificao();
				  header("Location: ../Control/cadastrarClientes.php?idLogin=".$idLogin."&status=ok");
			  }
			  else
			  {
				  //erro ao inserir pagamento sim0ples
				  $cadastrarClientes->corAlerta = "alerta_vermelho";
				  $cadastrarClientes->imagemAlerta = "<img src='../View/template/img/erro.png'/>";
				  $cadastrarClientes->menssagemAlerta = "Erro: Inserir pagamento simples: !".$resultadoPagamento	;
			  }
			}
			else
			{
				//não preencheu os dados corretamente
				$cadastrarClientes->corAlerta = "alerta_vermelho";
				$cadastrarClientes->imagemAlerta = "<img src='../View/template/img/erro.png'/>";
				$cadastrarClientes->menssagemAlerta = "Preencha os dados obrigatorios";
			}
			
		}else{
			$cadastrarClientes->corAlerta = "alerta_vermelho";
			$cadastrarClientes->imagemAlerta = "<img src='../View/template/img/erro.png'/>";
			$cadastrarClientes->menssagemAlerta = "Ops aconteceu algo errado!".$resultadoInsercao;
		}
	}
	else
	{
		//não preencheu os dados obrigatorios
		$cadastrarClientes->corAlerta = "alerta_vermelho";
		$cadastrarClientes->imagemAlerta = "<img src='../View/template/img/erro.png'/>";
		$cadastrarClientes->menssagemAlerta = "Preencha os dados obrigatorios";
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
	
	$cadastrarClientes->show();
	
	
?>