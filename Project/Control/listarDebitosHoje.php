<?php
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	
	$totalRegistrosPaginas = 20;//total de registros por pagina
    $contadorRegistro = 0;  //conta a quantidade de resgistro da pagina
	$corBloco = "1"; // serve para alterar as cores dos cadastros

	$listarDebitosHoje = new Template("../View/listarDebitosHoje.htm");//chama a pagina
	$objRelatorio = new relatorio();
	$objLogin = new login();
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
							$listarDebitosHoje->nomeLogin = $nomeFuncionarios;
							$listarDebitosHoje->idLogin = $idLogin;
							$listarDebitosHoje->horaAtual = $objHoraAtual->horaAgora();
							$listarDebitosHoje->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$listarDebitosHoje->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$listarDebitosHoje->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
				
	
	
	if(isset($_GET["pagina"])){
		$paginaAtual = $_GET["pagina"];
	}
	else 
	{
		$paginaAtual = 1;
	}
	
	if (isset($_GET["Buscar"]))
	{
		$buscar = $_GET["Buscar"];	
	}
	
	
	
	$inicio = ($paginaAtual * $totalRegistrosPaginas) - $totalRegistrosPaginas;
	
	//isso pode ocorrer se voce não pesquisar e se pesquisar vai entrar em algum if mais abaixo5
	$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalListarDebitosHoje();//retorna a quantidade de registros
	$listarDebitosHoje->quantidadeCadastros = $quantidadeTotalRegistros;//manda para o html a quantidade total de registros
	$objRelatorio->consultaListarDebitosHoje($inicio, $totalRegistrosPaginas);//executa a consulta de todos os debitos
	
	if(isset($_GET["Buscar"]) && isset($_GET["pagina"]))// é para o botão buscar
	{
		$listarDebitosHoje->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"debitosHoje");
		$listarDebitosHoje->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"debitosHoje",$inicio,$totalRegistrosPaginas);
	}
	elseif(isset($_GET["Buscar"]) && !isset($_GET["pagina"])) 
	{
		$listarDebitosHoje->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"debitosHoje");
		$listarDebitosHoje->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"debitosHoje",$inicio,$totalRegistrosPaginas);
	}
	// lista os clientes
	
	$numeroLinha = 0;
	
	while(($dados = $objRelatorio->mostraListar()) && ($totalRegistrosPaginas-1>=$contadorRegistro) && ($numeroLinha <= 20))//lista todos os clientes com debitos
	{
		$listarDebitosHoje->id = $dados["idClientes"];
		$listarDebitosHoje->nome = $dados["nomeClientes"];
		$listarDebitosHoje->apelido = $dados["apelidoClientes"];
		$listarDebitosHoje->dataVencimento = $objRelatorio->inverterData($dados["dataVencimentoPagamento"],"-","/");
		$listarDebitosHoje->totalVencimento = $dados["valorTotalPagamento"];
		
		
		if ($corBloco == "blocoCinza")
			$corBloco = "blocoBranco";
		else $corBloco = "blocoCinza";
		$listarDebitosHoje->corBloco = $corBloco;
		
		$debitos = $objRelatorio->retornaTotalDevendo($dados["idClientes"]);
		$creditos = $objRelatorio->retornaTotalHistPagamento($dados["idClientes"]);
		
		$objRelatorio->status($creditos,$debitos,$listarDebitosHoje);
		
		$listarDebitosHoje->block("BLOCK_DEBITOS");	
		$contadorRegistro++;
	}
	
	
	//fim da lista clientes
	
	// algoritmo para fazer a paginação
	$quantidadesPaginas = ceil($quantidadeTotalRegistros / $totalRegistrosPaginas);
	
	$totalLinks = 5;
	
	for ($i = $paginaAtual-$quantidadesPaginas; $i <= $paginaAtual-1; $i++)
	{
		if($i <= 0)
		{
			
		}
		else
		{
			$listarDebitosHoje->paginacao = "paginacao";
			$listarDebitosHoje->proxima = $i;
			$listarDebitosHoje->numerosPaginas = $i;
			$listarDebitosHoje->botao = "botao";
			$listarDebitosHoje->block("BLOCK_PAGINACAO");
		}
	}
	
	$listarDebitosHoje->paginacao = "paginacao";
	$listarDebitosHoje->proxima = $paginaAtual;
	$listarDebitosHoje->numerosPaginas = $paginaAtual;
	$listarDebitosHoje->botao = "botaoAtivo";
	$listarDebitosHoje->block("BLOCK_PAGINACAO");
	
	
	for ($i = $paginaAtual+1; $i <= $paginaAtual+$totalLinks; $i++)
	{
		if($i > $quantidadesPaginas)
		{
			
		}
		else
		{
			$listarDebitosHoje->paginacao = "paginacao";
			$listarDebitosHoje->proxima = $i;
			$listarDebitosHoje->numerosPaginas = $i;
			$listarDebitosHoje->botao = "botao";
			$listarDebitosHoje->block("BLOCK_PAGINACAO");
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
	// fim do algoritmo de paginação	
	
	$listarDebitosHoje->show();//mostra a pagina html com os dados
	
	
?>