<?php
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	include ("../Model/clientes.class.php");
	
	$totalRegistrosPaginas = 20;//total de registros por pagina
    $contadorRegistro = 0;  //conta a quantidade de resgistro da pagina
	$corBloco = NULL; // serve para alterar as cores dos cadastros
	

	$listarClientes = new Template("../View/listarClientes.htm");//chama a pagina
	$objRelatorio = new relatorio();
	$objLogin = new login();
	$objClientes = new clientes();
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
							$listarClientes->nomeLogin = $nomeFuncionarios;
							$listarClientes->idLogin = $idLogin;
							$listarClientes->horaAtual = $objHoraAtual->horaAgora();
							$listarClientes->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$listarClientes->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$listarClientes->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
		
	
	if(isset($_GET["status"]) && $_GET["status"]=="ok")
	{
		 $listarClientes->corAlerta = "alerta_verde";
		 $listarClientes->imagemAlerta = "<img src='../View/template/img/ok.png'/>";
		 $listarClientes->menssagemAlerta = "Operação efetuada com sucesso!";
	}
	
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
	$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalListarClientes();//retorna a quantidade de registros
	$listarClientes->quantidadeCadastros = $quantidadeTotalRegistros;//manda para o html a quantidade total de registros
	$objRelatorio->consultaListarClientes($inicio, $totalRegistrosPaginas);//executa a consulta de todos os clientes
	
	if(isset($_GET["Buscar"]) && isset($_GET["pagina"]))// é para o botão buscar
	{
		$listarClientes->buscar = "&Buscar=".$_GET["Buscar"]."&idLogin=1";
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"clientes");
		$listarClientes->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"clientes",$inicio,$totalRegistrosPaginas);
	}
	elseif(isset($_GET["Buscar"]) && !isset($_GET["pagina"])) 
	{
		$listarClientes->buscar = "&Buscar=".$_GET["Buscar"]."&idLogin=1";
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"clientes");
		$listarClientes->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"clientes",$inicio,$totalRegistrosPaginas);
	}
	// lista os clientes
	
	$numeroLinha = 0;
	
	while(($dados = $objRelatorio->mostraListar()) && ($totalRegistrosPaginas-1>=$contadorRegistro) && ($numeroLinha <= 20))//lista todos os clientes
	{
		$listarClientes->id = $dados["idClientes"];
		$listarClientes->nome = $dados["nomeClientes"];
		$listarClientes->apelido = $dados["apelidoClientes"];
		$listarClientes->telefone = $dados["telefoneClientes"];
		$listarClientes->cpf = $dados["cpfClientes"];
		
		
		if ($corBloco == "blocoCinza")
			$corBloco = "blocoBranco";
		else $corBloco = "blocoCinza";
		$listarClientes->corBloco = $corBloco;
		
		$debitos = $objRelatorio->retornaTotalDevendo($dados["idClientes"]);
		$creditos = $objRelatorio->retornaTotalHistPagamento($dados["idClientes"]);
		
		
		
		$objRelatorio->status($creditos,$debitos,$listarClientes);
		$listarClientes->block("BLOCK_CLIENTES");	
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
			$listarClientes->paginacao = "paginacao";
			$listarClientes->proxima = $i;
			$listarClientes->numerosPaginas = $i;
			$listarClientes->botao = "";
			$listarClientes->block("BLOCK_PAGINACAO");
		}
	}
	$listarClientes->paginacao = "paginacao";
	$listarClientes->proxima = $paginaAtual;
	$listarClientes->numerosPaginas = $paginaAtual;
	$listarClientes->botao = "active";
	$listarClientes->block("BLOCK_PAGINACAO");
	
	
	for ($i = $paginaAtual+1; $i <= $paginaAtual+$totalLinks; $i++)
	{
		if($i > $quantidadesPaginas)
		{
			
		}
		else
		{
			$listarClientes->paginacao = "paginacao";
			$listarClientes->proxima = $i;
			$listarClientes->numerosPaginas = $i;
			$listarClientes->botao = "";
			$listarClientes->block("BLOCK_PAGINACAO");
		}
	}
	
	
	
	// fim do algoritmo de paginação	
	
	//acao dos botoes
	if(isset($_GET["vender"]))
	{
		$nomeClientes = $_GET["vender"];
								
		if($objRelatorio->verificaUmCliente($nomeClientes) > 0)//verifica se esse cliente existe
		{
			if(!isset($_SESSION))
				session_start();
			$_SESSION['nomeCliente'] = $nomeClientes;
			header('Location: ../Control/vendasSegundaParte.php?idLogin='.$idLogin);
		}
		else
		{
			$paginaVendas->corAlerta = "alerta_vermelho";
			$paginaVendas->imagemAlerta = "<img src='../View/template/img/erro.png' />";
			$paginaVendas->menssagemAlerta = "Usuário não existe!";
		}
	}
	
	if(isset($_GET["editar"]))
	{
		header("Location: ../Control/cadastrarClientes.php?idLogin=".$idLogin."&editar=".$_GET["editar"]);
	}
	
	if(isset($_GET["excluir"]))
	{
		$idClientes = $_GET["excluir"];
		$resultDel = $objClientes->deletarClientes($idClientes);
		if($resultDel = 1)
		{
			header("Location: ../Control/listarClientes.php?idLogin=".$idLogin."&status=ok");
		}
		else
		{
			header("Location: ../Control/listarClientes.php?idLogin=".$idLogin."&status=");
		}
	}
	// - - fim acao dos botoes
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
	$listarClientes->show();//mostra a pagina html com os dados
	
	
?>