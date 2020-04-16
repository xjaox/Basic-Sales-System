<?php
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/histPagamento.class.php");
	include ("../Model/pagamento.class.php");
	include ("../Model/login.class.php");
	include ("../Model/modificacao.class.php");
	include	("../Control/horaAtual.php");
	
	//variaveis e objetos globais 
	$paginaSegundaPartePagamento = new Template("../View/pagamentoSegundaParte.htm");	
	$objRelatorio = new relatorio();
	$objHistPagamento = new histPagamento();
	$objPagamento = new pagamento();
	$objLogin = new login();
	$objModificacao = new modificacao();
	$objHoraAtual = new horaAtual();
	
	$nomeClientes;
	$tabela = null;
	$cont=0;
	$valorTotal = 0;
	$valorPagamento = 0;
	$calculoPagamento = 0;
	$allTotal = 0;
	//fim variaveis e objetos globais
	
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
							$paginaSegundaPartePagamento->nomeLogin = $nomeFuncionarios;
							$paginaSegundaPartePagamento->idLogin = $idLogin;
							$paginaSegundaPartePagamento->horaAtual = $objHoraAtual->horaAgora();
							$paginaSegundaPartePagamento->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$paginaSegundaPartePagamento->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$paginaSegundaPartePagamento->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
				
	
	if(isset($_SESSION["nomeClientes"]))
	{
		$nomeClientes = $_SESSION["nomeClientes"];
		$paginaSegundaPartePagamento->nomeClientes = $nomeClientes;
		if($objRelatorio->listarValorTotalDataVencimento($nomeClientes)>0)
		{
		  while($dados = $objRelatorio->mostraListar())
		  {
			  $valorTotal += $dados["valorTotalPagamento"];
			  $paginaSegundaPartePagamento->valorPagamento = $dados["valorTotalPagamento"];
			  $paginaSegundaPartePagamento->idPagamento = $dados["idPagamento"];
			  $paginaSegundaPartePagamento->dataVencimento = $objRelatorio->inverterData($dados["dataVencimentoPagamento"],"-","/");
			  $paginaSegundaPartePagamento->detalhesPagamento = "../Control/detalhesPagamento.php?idLogin=".$idLogin."&idPagamento=".$dados["idPagamento"];
			  $paginaSegundaPartePagamento->block("BLOCK_PAGAMENTO");
			  $cont++;
		  }
		  
		  $paginaSegundaPartePagamento->totalPagamento = $valorTotal;
		  
		}
		else 
		{
			//ocorreu algum problema na consulta de pagamento ou no nome do cliente
			$paginaSegundaPartePagamento->corAlerta = "alerta_vermelho";
			$paginaSegundaPartePagamento->imagemAlerta = "<img src='../View/template/img/erro.png' > ";
			$paginaSegundaPartePagamento->menssagemAlerta = "Ops!, falha tecnica";
		}
	}
	else
	{
		//o nome do cliente não veio.
		$paginaSegundaPartePagamento->corAlerta = "alerta_vermelho";
		$paginaSegundaPartePagamento->imagemAlerta = "<img src='../View/template/img/erro.png' > ";
		$paginaSegundaPartePagamento->menssagemAlerta = "Ops!, falha tecnica";
	}				
	
	// - - - - - - - - - - - - - avisos logo apos a operação RECEBER
	if(isset($_GET["troco"]) && $_GET["troco"] !="")
	{
		//pagamento efetuado com sucesso
		$paginaSegundaPartePagamento->corAlerta = "alerta_verde";
		$paginaSegundaPartePagamento->imagemAlerta = "<img src='../View/template/img/ok.png' > ";
		$paginaSegundaPartePagamento->menssagemAlerta = "Sobrou troco o valor é: <b> R$ ".$_GET["troco"]." </b>";
	}
	
	if(isset($_GET["pagamento"]) && $_GET["pagamento"] == "ok")
	{
		//pagamento efetuado com sucesso
		$paginaSegundaPartePagamento->corAlerta = "alerta_verde";
		$paginaSegundaPartePagamento->imagemAlerta = "<img src='../View/template/img/ok.png' > ";
		$paginaSegundaPartePagamento->menssagemAlerta = "Pagamento efetuado com sucesso!";
	}
	else
	{
		//erro
	}
	
	
	// - - - - - - - - - - - - - Fim avisos
	if(isset($_POST))//se apertar no botão
	{
		if(isset($_POST["proximo"]) && $_POST["proximo"] = "Receber")	
		{
			if(isset($_POST["valorPagamento"]) && $_POST["valorPagamento"] != "")
			{
				
				$valorPagamento = $objRelatorio->converterValor($_POST["valorPagamento"]);
				$valorDigitado = $objRelatorio->converterValor($_POST["valorPagamento"]);
				$calculoPagamento = $valorTotal - $valorPagamento;
				
			    if($calculoPagamento == 0) //pagou todas as contas gravar 0
				{
					$objRelatorio->listarValorTotalDataVencimento($nomeClientes);
					while($dados = $objRelatorio->mostraListar())//zera as dividas o cliente pagou tudo certinho.
		  			{
						$resultado = $objPagamento->pagarPagamento($dados["idPagamento"],0,'sim');//grava no db 0 no valor total
						//echo $resultado;
						if($dados["valorTotalPagamento"]>0)
						{
							$objHistPagamento->setHistPagamento($dados["idPagamento"],$dados["valorTotalPagamento"],date("Y/m/d H:i"),0);
							$objHistPagamento->insereHistPagamento();
							
							$objModificacao->setModificacao(
														   0,$objRelatorio->retornaIdCli($nomeClientes),
														   $dados["idPagamento"],$idLogin,
														   "Pag: pagamentoSegundaParte.php | Operação: receber ",date("Y/m/d H:i"));
							$result = $objModificacao->inserirModificao();
						}
					}
					
					//--------------- atualizar valores na pagina
					
					header("Location: ../Control/pagamentoSegundaParte.php?idLogin=".$idLogin."&pagamento=ok");
					//------------- fim da atualização
					
					
					
					
				}
	
				if($calculoPagamento < 0) //sobrou troco mostrar 0 e gravar o resultado
				{
					
					$objRelatorio->listarValorTotalDataVencimento($nomeClientes);
					while($dados = $objRelatorio->mostraListar())//zera as dividas o cliente pagou tudo certinho.
		  			{
						$resultado = $objPagamento->pagarPagamento($dados["idPagamento"],0,'sim');//grava no db 0 no valor total
						//echo $resultado;
						
						if($dados["valorTotalPagamento"]>0)
						{
							$objHistPagamento->setHistPagamento($dados["idPagamento"],$dados["valorTotalPagamento"],date("Y/m/d H:i"),0);
							$objHistPagamento->insereHistPagamento();
							
							$objModificacao->setModificacao(
														   0,$objRelatorio->retornaIdCli($nomeClientes),
														   $dados["idPagamento"],$idLogin,
														   "Pag: pagamentoSegundaParte.php | Operação: receber ",date("Y/m/d H:i"));
							$result = $objModificacao->inserirModificao();
						}
					}
					
					
					//--------------- atualizar valores na pagina
					
					 header("Location: ../Control/pagamentoSegundaParte.php?idLogin=".$idLogin."&troco=".abs($calculoPagamento));
					//------------- fim da atualização		
				}
				
				if($calculoPagamento > 0) // não deu pra pagar todas as contas ainda possui dividas
				{
					$ok = 0;
					$objRelatorio->listarValorTotalDataVencimento($nomeClientes);
					while($dados = $objRelatorio->mostraListar())//para atualizar a pagina
		  			{
						if(($dados["valorTotalPagamento"] > 0) && $ok != 1)
			  			{
							//echo "<br> Valor Pagamento 1: ".$valorPagamento;
							$valorPagamento = $dados["valorTotalPagamento"] - $valorPagamento;
							echo "<br><h1> Valor Pagamento 2: " .$valorPagamento."</h1><br>Valor Digitado: ".$valorDigitado;
							
							if($valorPagamento < 0)
							{
								//se entrar aqui o while tem que parar	
								$resultado = $objPagamento->pagarPagamento($dados["idPagamento"],0,'sim');//grava no db 0 no valor total
								//echo $resultado;
								echo "<br> Entrou no if valorPagamento < 1";
								$ok = 1;
								if($dados["valorTotalPagamento"]>0)
								{
									$objHistPagamento->setHistPagamento($dados["idPagamento"],$valorDigitado,date("Y/m/d H:i"),0);
									$objHistPagamento->insereHistPagamento();
									
									$objModificacao->setModificacao(
														   0,$objRelatorio->retornaIdCli($nomeClientes),
														   $dados["idPagamento"],$idLogin,
														   "Pag: pagamentoSegundaParte.php | Operação: receber ",date("Y/m/d H:i"));
									$result = $objModificacao->inserirModificao();
								}
							}
							else 
							{
								$resultado = $objPagamento->pagarPagamento($dados["idPagamento"],$valorPagamento,'nao');
								echo $resultado."<br>";
								echo "<br> Entrou no else valorPagamento < 1 <br>";
								echo "<h1> Valor Pagamento 3: ".$valorPagamento;
								$ok = 1;
								if($dados["valorTotalPagamento"]>0)
								{
									$objHistPagamento->setHistPagamento($dados["idPagamento"],$valorDigitado,date("Y/m/d H:i"),0);
									$objHistPagamento->insereHistPagamento();
									
									$objModificacao->setModificacao(
														   0,$objRelatorio->retornaIdCli($nomeClientes),
														   $dados["idPagamento"],$idLogin,
														   "Pag: pagamentoSegundaParte.php | Operação: receber ",date("Y/m/d H:i"));
									$result = $objModificacao->inserirModificao();
								}
							}
						}
						if($valorPagamento < 0)
						{
							$valorPagamento = abs($valorPagamento);
							$ok = 0;
						}
					}
				
			
					//--------------- atualizar valores na pagina
					
					 header("Location: ../Control/pagamentoSegundaParte.php?idLogin=".$idLogin."&pagamento=ok");
					//------------- fim da atualização
					
				}
			}
			else
			{
				$paginaSegundaPartePagamento->corAlerta = "alerta_amarelo";
				$paginaSegundaPartePagamento->imagemAlerta = "<img src='../View/template/img/aviso.png' > ";
				$paginaSegundaPartePagamento->menssagemAlerta = "Preencha o campo valor";
				
			}
		}
		else
		{
			//cliente não clicou no botão proximo, alternativaaaa impossivelllllll.
		}
	}
	
			}
			else
			{
				header("Location: ../Control/logar.php");
			}
			}
		}
		//colocar a consistencia se ocorrer a alteração do idLogin no get (user fuça)
	}
	else
	{
		header("Location: ../Control/logar.php");
	}
	$paginaSegundaPartePagamento->show();
?>