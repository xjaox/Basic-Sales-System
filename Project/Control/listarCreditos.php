<?php
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	
	$totalRegistrosPaginas = 20;//total de registros por pagina
    $contadorRegistro = 0;  //conta a quantidade de resgistro da pagina
	$corBloco = "1"; // serve para alterar as cores dos cadastros

	$listarCreditos = new Template("../View/listarCreditos.htm");//chama a pagina
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
							$listarCreditos->nomeLogin = $nomeFuncionarios;
							$listarCreditos->idLogin = $idLogin;
							$listarCreditos->horaAtual = $objHoraAtual->horaAgora();
							$listarCreditos->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$listarCreditos->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$listarCreditos->sair = "principal.php?idLogin=".$idLogin."&sair";
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
	$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalListarCreditos();//retorna a quantidade de registros
	$listarCreditos->quantidadeCadastros = $quantidadeTotalRegistros;//manda para o html a quantidade total de registros
	$objRelatorio->consultaListarCreditos($inicio, $totalRegistrosPaginas);//executa a consulta de todos os debitos
	
	if(isset($_GET["Buscar"]) && isset($_GET["pagina"]))// é para o botão buscar
	{
		$listarCreditos->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"creditos");
		$listarCreditos->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"creditos",$inicio,$totalRegistrosPaginas);
	}
	elseif(isset($_GET["Buscar"]) && !isset($_GET["pagina"])) 
	{
		$listarCreditos->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"creditos");
		$listarCreditos->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"creditos",$inicio,$totalRegistrosPaginas);
	}
	// lista os clientes
	
	$numeroLinha = 0;
	
	while(($dados = $objRelatorio->mostraListar()) && ($totalRegistrosPaginas-1>=$contadorRegistro) && ($numeroLinha <= 20))//lista todos os clientes com debitos
	{
		$listarCreditos->id = $dados["idClientes"];
		$listarCreditos->nome = $dados["nomeClientes"];
		$listarCreditos->apelido = $dados["apelidoClientes"];
		$listarCreditos->dataVencimento = $objRelatorio->inverterData($dados["dataVencimentoPagamento"],"-","/");
		$listarCreditos->valorHistPagamento = $dados["total"];
		
		if ($corBloco == "blocoCinza")
			$corBloco = "blocoBranco";
		else $corBloco = "blocoCinza";
		$listarCreditos->corBloco = $corBloco;
		
		$debitos = $objRelatorio->retornaTotalDevendo($dados["idClientes"]);
		$creditos = $objRelatorio->retornaTotalHistPagamento($dados["idClientes"]);
		
		$objRelatorio->status($creditos,$debitos,$listarCreditos);
		
		$listarCreditos->block("BLOCK_CREDITOS");	
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
			$listarCreditos->paginacao = "paginacao";
			$listarCreditos->proxima = $i;
			$listarCreditos->numerosPaginas = $i;
			$listarCreditos->botao = "botao";
			$listarCreditos->block("BLOCK_PAGINACAO");
		}
	}
	
	$listarCreditos->paginacao = "paginacao";
	$listarCreditos->proxima = $paginaAtual;
	$listarCreditos->numerosPaginas = $paginaAtual;
	$listarCreditos->botao = "botaoAtivo";
	$listarCreditos->block("BLOCK_PAGINACAO");
	
	
	for ($i = $paginaAtual+1; $i <= $paginaAtual+$totalLinks; $i++)
	{
		if($i > $quantidadesPaginas)
		{
			
		}
		else
		{
			$listarCreditos->paginacao = "paginacao";
			$listarCreditos->proxima = $i;
			$listarCreditos->numerosPaginas = $i;
			$listarCreditos->botao = "botao";
			$listarCreditos->block("BLOCK_PAGINACAO");
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
	
	$listarCreditos->show();//mostra a pagina html com os dados
	
	
?>