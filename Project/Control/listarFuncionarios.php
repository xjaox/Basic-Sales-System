<?php
	
	include ("../Model/Template.class.php");
	include ("../Model/relatorio.class.php");
	include ("../Model/login.class.php");
	include ("../Control/horaAtual.php");
	include ("../Model/funcionarios.class.php");
	
	$totalRegistrosPaginas = 20;//total de registros por pagina
    $contadorRegistro = 0;  //conta a quantidade de resgistro da pagina
	$corBloco = "1"; // serve para alterar as cores dos cadastros

	$listarFuncionarios = new Template("../View/listarFuncionarios.htm");//chama a pagina
	$objRelatorio = new relatorio();
	$objLogin = new login();
	$objFuncionarios = new funcionarios();
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
							$listarFuncionarios->nomeLogin = $nomeFuncionarios;
							$listarFuncionarios->idLogin = $idLogin;
							$listarFuncionarios->horaAtual = $objHoraAtual->horaAgora();
							$listarFuncionarios->configConta = "principal.php?idLogin=".$idLogin."&configConta";
							$listarFuncionarios->suporte = "principal.php?idLogin=".$idLogin."&suporte";
							$listarFuncionarios->sair = "principal.php?idLogin=".$idLogin."&sair";
							//fim links na tela
	
	if(isset($_GET["status"]) && $_GET["status"]=="ok")
	{
		 $listarFuncionarios->corAlerta = "alerta_verde";
		 $listarFuncionarios->imagemAlerta = "<img src='../View/template/img/ok.png'/>";
		 $listarFuncionarios->menssagemAlerta = "Operação efetuada com sucesso!";
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
	$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalListarFuncionarios();//retorna a quantidade de registros
	$listarFuncionarios->quantidadeCadastros = $quantidadeTotalRegistros;//manda para o html a quantidade total de registros
	$objRelatorio->consultaListarFuncionarios($inicio, $totalRegistrosPaginas);//executa a consulta de todos os clientes
	
	if(isset($_GET["Buscar"]) && isset($_GET["pagina"]))// é para o botão buscar
	{
		$listarFuncionarios->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"funcionarios");
		$listarFuncionarios->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"funcionarios",$inicio,$totalRegistrosPaginas);
	}
	elseif(isset($_GET["Buscar"]) && !isset($_GET["pagina"])) 
	{
		$listarFuncionarios->buscar = "&Buscar=".$_GET["Buscar"];
		$quantidadeTotalRegistros = $objRelatorio->quantidadeTotalConsultar($buscar,"funcionarios");
		$listarFuncionarios->quantidadeCadastros = $quantidadeTotalRegistros;
		$objRelatorio->consultar($buscar,"funcionarios",$inicio,$totalRegistrosPaginas);
	}
	// lista os clientes
	
	while(($dados = $objRelatorio->mostraListar()) && ($totalRegistrosPaginas-1>=$contadorRegistro))//lista todos os clientes
	{
		$listarFuncionarios->id = $dados["idFuncionarios"];
		$listarFuncionarios->nome = $dados["nomeFuncionarios"];
		$listarFuncionarios->apelido = $dados["apelidoFuncionarios"];
		$listarFuncionarios->cpf = $dados["cpfFuncionarios"];
		$listarFuncionarios->telefone = $dados["telefoneFuncionarios"];
		
		if ($corBloco == "blocoCinza")
			$corBloco = "blocoBranco";
		else $corBloco = "blocoCinza";
		$listarFuncionarios->corBloco = $corBloco;
		
		$listarFuncionarios->block("BLOCK_FUNCIONARIOS");	
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
			$listarFuncionarios->paginacao = "paginacao";
			$listarFuncionarios->proxima = $i;
			$listarFuncionarios->numerosPaginas = $i;
			$listarFuncionarios->botao = "";
			$listarFuncionarios->block("BLOCK_PAGINACAO");
		}
	}
	$listarFuncionarios->paginacao = "paginacao";
	$listarFuncionarios->proxima = $paginaAtual;
	$listarFuncionarios->numerosPaginas = $paginaAtual;
	$listarFuncionarios->botao = "active";
	$listarFuncionarios->block("BLOCK_PAGINACAO");
	
	
	for ($i = $paginaAtual+1; $i <= $paginaAtual+$totalLinks; $i++)
	{
		if($i > $quantidadesPaginas)
		{
			
		}
		else
		{
			$listarFuncionarios->paginacao = "paginacao";
			$listarFuncionarios->proxima = $i;
			$listarFuncionarios->numerosPaginas = $i;
			$listarFuncionarios->botao = "";
			$listarFuncionarios->block("BLOCK_PAGINACAO");
		}
	}
	
	// - ações botoes
	
	if(isset($_GET["editar"]))
	{
		header("Location: ../Control/cadastrarFuncionarios.php?idLogin=".$idLogin."&editar=".$_GET["editar"]);
	}
	
	if(isset($_GET["excluir"]))
	{
		$idFuncionarios = $_GET["excluir"];
		$resultDel = $objFuncionarios->deletarFuncionarios($idFuncionarios);
		if($resultDel = 1)
		{
			header("Location: ../Control/listarFuncionarios.php?idLogin=".$idLogin."&status=ok");
		}
		else
		{
			header("Location: ../Control/listarFuncionarios.php?idLogin=".$idLogin."&status=");
		}
	}
	// - fim ações botoes
					
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
	$listarFuncionarios->show();//mostra a pagina html com os dados
?>