// JavaScript Document

function formatar(src, mask)
{
  var i = src.value.length;
  var saida = mask.substring(0,1);
  var caract = new RegExp(/^[0-9\b]+$/i);
  var texto = mask.substring(i)
if (texto.substring(0,1) != saida)
  {
        src.value += texto.substring(0,1);
  }

    var kCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    var caract = new RegExp(/^[0-9\b]+$/i);
    var caract = caract.test(String.fromCharCode(kCode));

    if(!caract){
        return false;
    }
}

documentall = document.all;
/*
* função para formatação de valores monetários retirada de
* http://jonasgalvez.com/br/blog/2003-08/egocentrismo
*/

function formatamoney(c) {
    var t = this; if(c == undefined) c = 2;
    var p, d = (t=t.split("."))[1].substr(0, c);
    for(p = (t=t[0]).length; (p-=3) >= 1;) {
	        t = t.substr(0,p) + "." + t.substr(p);
    }
    return t+","+d+Array(c+1-d.length).join(0);
}

String.prototype.formatCurrency=formatamoney

function demaskvalue(valor, currency){
/*
* Se currency é false, retorna o valor sem apenas com os números. Se é true, os dois últimos caracteres são considerados as
* casas decimais
*/
var val2 = '';
var strCheck = '0123456789';
var len = valor.length;
	if (len== 0){
		return 0.00;
	}

	if (currency ==true){
		/* Elimina os zeros à esquerda
		* a variável  <i> passa a ser a localização do primeiro caractere após os zeros e
		* val2 contém os caracteres (descontando os zeros à esquerda)
		*/

		for(var i = 0; i < len; i++)
			if ((valor.charAt(i) != '0') && (valor.charAt(i) != ',')) break;

		for(; i < len; i++){
			if (strCheck.indexOf(valor.charAt(i))!=-1) val2+= valor.charAt(i);
		}

		if(val2.length==0) return "0.00";
		if (val2.length==1)return "0.0" + val2;
		if (val2.length==2)return "0." + val2;

		var parte1 = val2.substring(0,val2.length-2);
		var parte2 = val2.substring(val2.length-2);
		var returnvalue = parte1 + "." + parte2;
		return returnvalue;

	}
	else{
			/* currency é false: retornamos os valores COM os zeros à esquerda,
			* sem considerar os últimos 2 algarismos como casas decimais
			*/
			val3 ="";
			for(var k=0; k < len; k++){
				if (strCheck.indexOf(valor.charAt(k))!=-1) val3+= valor.charAt(k);
			}
	return val3;
	}
}


function reais(obj,event){

var whichCode = (window.Event) ? event.which : event.keyCode;
/*
Executa a formatação após o backspace nos navegadores !document.all
*/
if (whichCode == 8 && !documentall) {
/*
Previne a ação padrão nos navegadores
*/
	if (event.preventDefault){ //standart browsers
			event.preventDefault();
		}else{ // internet explorer
			event.returnValue = false;
	}
	var valor = obj.value;
	var x = valor.substring(0,valor.length-1);
	obj.value= demaskvalue(x,true).formatCurrency();
	return false;
}
/*
Executa o Formata Reais e faz o format currency novamente após o backspace
*/
FormataReais(obj,'.',',',event);
} // end reais


function backspace(obj,event){
/*
Essa função basicamente altera o  backspace nos input com máscara reais para os navegadores IE e opera.
O IE não detecta o keycode 8 no evento keypress, por isso, tratamos no keydown.
Como o opera suporta o infame document.all, tratamos dele na mesma parte do código.
*/

var whichCode = (window.Event) ? event.which : event.keyCode;
if (whichCode == 8 && documentall) {
	var valor = obj.value;
	var x = valor.substring(0,valor.length-1);
	var y = demaskvalue(x,true).formatCurrency();

	obj.value =""; //necessário para o opera
	obj.value += y;

	if (event.preventDefault){ //standart browsers
			event.preventDefault();
		}else{ // internet explorer
			event.returnValue = false;
	}
	return false;

	}// end if
}// end backspace

function FormataReais(fld, milSep, decSep, e) {
var sep = 0;
var key = '';
var i = j = 0;
var len = len2 = 0;
var strCheck = '0123456789';
var aux = aux2 = '';
var whichCode = (window.Event) ? e.which : e.keyCode;

//if (whichCode == 8 ) return true; //backspace - estamos tratando disso em outra função no keydown
if (whichCode == 0 ) return true;
if (whichCode == 9 ) return true; //tecla tab
if (whichCode == 13) return true; //tecla enter
if (whichCode == 16) return true; //shift internet explorer
if (whichCode == 17) return true; //control no internet explorer
if (whichCode == 27 ) return true; //tecla esc
if (whichCode == 34 ) return true; //tecla end
if (whichCode == 35 ) return true;//tecla end
if (whichCode == 36 ) return true; //tecla home

/*
O trecho abaixo previne a ação padrão nos navegadores. Não estamos inserindo o caractere normalmente, mas via script
*/

if (e.preventDefault){ //standart browsers
		e.preventDefault()
	}else{ // internet explorer
		e.returnValue = false
}

var key = String.fromCharCode(whichCode);  // Valor para o código da Chave
if (strCheck.indexOf(key) == -1) return false;  // Chave inválida

/*
Concatenamos ao value o keycode de key, se esse for um número
*/
fld.value += key;

var len = fld.value.length;
var bodeaux = demaskvalue(fld.value,true).formatCurrency();
fld.value=bodeaux;

/*
Essa parte da função tão somente move o cursor para o final no opera. Atualmente não existe como movê-lo no konqueror.
*/
  if (fld.createTextRange) {
    var range = fld.createTextRange();
    range.collapse(false);
    range.select();
  }
  else if (fld.setSelectionRange) {
    fld.focus();
    var length = fld.value.length;
    fld.setSelectionRange(length, length);
  }
  return false;

}

//--->Função para a formatação dos campos...<---

function Mascara(tipo, campo, teclaPress) {
        if (window.event)
        {
                var tecla = teclaPress.keyCode;
        } else {
                tecla = teclaPress.which;
        }
 
        var s = new String(campo.value);
        // Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
        s = s.replace(/(\.|\(|\)|\/|\-| )+/g,'');
 
        tamanho = s.length + 1;
 
        if ( tecla != 9 && tecla != 8 ) {
                switch (tipo)
                {
                case 'CPF' :
                        if (tamanho > 3 && tamanho < 7)
                                campo.value = s.substr(0,3) + '.' + s.substr(3, tamanho);
                        if (tamanho >= 7 && tamanho < 10)
                                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,tamanho-6);
                        if (tamanho >= 10 && tamanho < 12)
                                campo.value = s.substr(0,3) + '.' + s.substr(3,3) + '.' + s.substr(6,3) + '-' + s.substr(9,tamanho-9);
                break;
 
                case 'TELEFONE' :
                        if (tamanho > 2 && tamanho < 4)
                                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,tamanho);
                        if (tamanho >= 7 && tamanho < 11)
                                campo.value = '(' + s.substr(0,2) + ') ' + s.substr(2,4) + '-' + s.substr(6,tamanho-6);
                break;
 
                case 'DATA' :
                        if (tamanho > 2 && tamanho < 4)
                                campo.value = s.substr(0,2) + '/' + s.substr(2, tamanho);
                        if (tamanho > 4 && tamanho < 11)
                                campo.value = s.substr(0,2) + '/' + s.substr(2,2) + '/' + s.substr(4,tamanho-4);
                break;
                
                case 'CEP' :
                        if (tamanho > 5 && tamanho < 7)
                                campo.value = s.substr(0,5) + '-' + s.substr(5, tamanho);
                break;
				case 'PLACA' :
                        if (tamanho > 3 && tamanho < 7)
                                campo.value = s.substr(0,3) + '-' + s.substr(3, tamanho);
                break;
                }
        }
}

//--->Função para verificar se o valor digitado é número...<---

function digitos(event){
        if (window.event) {
                // IE
                key = event.keyCode;
        } else if ( event.which ) {
                // netscape
                key = event.which;
        }
        if ( key != 8 || key != 13 || key < 48 || key > 57 )
                return ( ( ( key > 47 ) && ( key < 58 ) ) || ( key == 8 ) || ( key == 13 ) );
        return true;
}

//--->Função para verificar forumario Cliente...<---

function formulario(id,iderro){
document.getElementById(id).style.background = '';
document.getElementById(iderro).style.display = 'none';
}

function validarClientes(nform){

	if (nform.nomeCliente.value == ""){
		document.getElementById('nomeCliente').style.background = '#FBE1E1';
		document.getElementById('erroNome').style.display = '';
		return false;
	}

	if (nform.apelidoCliente.value == ""){
		document.getElementById('apelidoCliente').style.background = '#FBE1E1';
		document.getElementById('erroApelido').style.display = '';
		return false;
	}

    /*	
	if (nform.cepCliente.value != ""){
		
		var cep = new String(nform.cepCliente.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			cep = cep.replace(/(\.|\(|\)|\/|\-| )+/g,'');
		
		if (cep.length != 8){
			document.getElementById('cepCliente').style.background = '#FBE1E1';
			document.getElementById('erroCep').style.display = '';
			return false;
		}
	}

	if (nform.telefoneCliente.value != ""){
		
		var telefone = new String(nform.telefoneCliente.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			telefone = telefone.replace(/(\.|\(|\)|\/|\-| )+/g,'');
		
		if (telefone.length != 10){
			document.getElementById('telefoneCliente').style.background = '#FBE1E1';
			document.getElementById('erroTelefone').style.display = '';
			return false;
		}
	}
	
	if (nform.dataNascimentoCliente.value != ""){
	
		var dataNascimento = new String(nform.dataNascimentoCliente.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			dataNascimento = dataNascimento.replace(/(\.|\(|\)|\/|\-| )+/g,'');
			
		if (dataNascimento.length != 8){
			document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
			document.getElementById('erroDataNascimento').style.display = '';
			return false;		
		}
	
		if (dataNascimento.length == 8){
			
			//Aqui inicia a verificação de data
			var bissexto = 0;
			var data = dataNascimento.length; 
			var dia = data.substr(0,2);
			var mes = data.substr(2,-4);
			var ano = data.substr(4,8);
			var pegaAno = new Date();
			var anoAtual = pegaAno.getFullYear();
			var idadeMaxima = anoAtual - 100;
				
			if ((ano > idadeMaxima)||(ano <= anoAtual)){
					
				if ((mes >= 1) || (mes <=12)){
							
					switch (mes) {
						case '01':
						case '03':
						case '05':
						case '07':
						case '08':
						case '10':
						case '12':
								
						if  ((dia >= 1)||(dia <= 31)){
							return true;
						}else{
							document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
							document.getElementById('erroDataNascimento').style.display = '';
							return false;
						}
					break
											
						case '04':              
						case '06':
						case '09':
						case '11':
							
						if  ((dia >= 1)||(dia <= 30)){
							return true;
						}else{
							document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
							document.getElementById('erroDataNascimento').style.display = '';
							return false;
						}
					break
						
						case '02':
							
							if ((ano % 4 == 0) || (ano % 100 == 0) || (ano % 400 == 0)){ 
								bissexto = 1; 
							} 
							
							if ((bissexto == 1) && (dia >= 1) && (dia <= 29)){ 
								return true;                             
							}					
							if ((bissexto != 1) && (dia >= 1) && (dia <= 28)){ 
								return true; 
							}else{
								document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
								document.getElementById('erroDataNascimento').style.display = '';
								return false;
							}                       
						break 
					}
				}else{
					document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
					document.getElementById('erroDataNascimento').style.display = '';
					return false;
				} 
			}else{
				document.getElementById('dataNascimentoCliente').style.background = '#FBE1E1';
				document.getElementById('erroDataNascimento').style.display = '';
				return false;
			} 
		}
	}
	
	if (nform.cpfCliente.value != ""){	
	
		var CPF = new String(nform.cpfCliente.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
		CPF = CPF.replace(/(\.|\(|\)|\/|\-| )+/g,'');
	
		// Aqui começa a checagem do CPF
		var POSICAO, I, SOMA, DV, DV_INFORMADO;
		var DIGITO = new Array(10);
		DV_INFORMADO = CPF.substr(9, 2); // Retira os dois últimos dígitos do número informado
	
		// Desemembra o número do CPF na array DIGITO
		for (I=0; I<=8; I++) {
		  DIGITO[I] = CPF.substr( I, 1);
		}
	
		// Calcula o valor do 10º dígito da verificação
		POSICAO = 10;
		
		SOMA = 0;
		
		   for (I=0; I<=8; I++) {
			  SOMA = SOMA + DIGITO[I] * POSICAO;
			  POSICAO = POSICAO - 1;
		   }
		   
		DIGITO[9] = SOMA % 11;
		   if (DIGITO[9] < 2) {
				DIGITO[9] = 0;
		   }else{
			   DIGITO[9] = 11 - DIGITO[9];
		   }
	
		// Calcula o valor do 11º dígito da verificação
		POSICAO = 11;
		SOMA = 0;
		   for (I=0; I<=9; I++) {
			  SOMA = SOMA + DIGITO[I] * POSICAO;
			  POSICAO = POSICAO - 1;
		   }
		   
		DIGITO[10] = SOMA % 11;
		
		   if (DIGITO[10] < 2) {
				DIGITO[10] = 0;
		   }else {
				DIGITO[10] = 11 - DIGITO[10];
		   }
	
		// Verifica se os valores dos dígitos verificadores conferem
		DV = DIGITO[9] * 10 + DIGITO[10];
		
		if (DV != DV_INFORMADO) {
			document.getElementById('cpfCliente').style.background = '#FBE1E1';
			document.getElementById('erroCpf').style.display = '';
		  return false;
		}
	}

	if (nform.emailCliente.value != ""){

      email = /^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
          
      if(email.exec(nform.emailCliente.value))
                {
                  
                } else {
					document.getElementById('emailCliente').style.background = '#FBE1E1';
					document.getElementById('erroEmail').style.display = '';
                  	return false;
                }
      }
	  */
	  
}

//--->Função para verificar forumario Funcionarios...<---

function validarFuncionarios(nform){

	if (nform.nomeFuncionario.value == ""){
		document.getElementById('nomeFuncionario').style.background = '#FBE1E1';
		document.getElementById('erroNome').style.display = '';
		return false;
	}

	if (nform.apelidoFuncionario.value == ""){
		document.getElementById('apelidoFuncionario').style.background = '#FBE1E1';
		document.getElementById('erroApelido').style.display = '';
		return false;
	}
	
	/*
	if (nform.cepFuncionario.value != ""){
		
		var cep = new String(nform.cepFuncionario.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			cep = cep.replace(/(\.|\(|\)|\/|\-| )+/g,'');
		
		if (cep.length != 8){
			document.getElementById('cepFuncionario').style.background = '#FBE1E1';
			document.getElementById('erroCep').style.display = '';
			return false;
		}
	}

	if (nform.telefoneFuncionario.value != ""){
		
		var telefone = new String(nform.telefoneFuncionario.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			telefone = telefone.replace(/(\.|\(|\)|\/|\-| )+/g,'');
		
		if (telefone.length != 10){
			document.getElementById('telefoneFuncionario').style.background = '#FBE1E1';
			document.getElementById('erroTelefone').style.display = '';
			return false;
		}
	}
	
	if (nform.dataNascimentoFuncionario.value != ""){
	
		var dataNascimento = new String(nform.dataNascimentoFuncionario.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
			dataNascimento = dataNascimento.replace(/(\.|\(|\)|\/|\-| )+/g,'');
			
		if (dataNascimento.length != 8){
			ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
			document.getElementById('erroDataNascimento').style.display = '';
			return false;		
		}
	
		if (dataNascimento.length == 8){
			
			//Aqui inicia a verificação de data
			var bissexto = 0;
			var data = dataNascimento.length; 
			var dia = data.substr(0,2);
			var mes = data.substr(2,-4);
			var ano = data.substr(4,8);
			var pegaAno = new Date();
			var anoAtual = pegaAno.getFullYear();
			var idadeMaxima = anoAtual - 100;
				
			if ((ano > idadeMaxima)||(ano <= anoAtual)){
					
				if ((mes >= 1) || (mes <=12)){
							
					switch (mes) {
						case '01':
						case '03':
						case '05':
						case '07':
						case '08':
						case '10':
						case '12':
								
						if  ((dia >= 1)||(dia <= 31)){
							return true;
						}else{
							ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
							document.getElementById('erroDataNascimento').style.display = '';
							return false;
						}
					break
											
						case '04':              
						case '06':
						case '09':
						case '11':
							
						if  ((dia >= 1)||(dia <= 30)){
							return true;
						}else{
							ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
							document.getElementById('erroDataNascimento').style.display = '';
							return false;
						}
					break
						
						case '02':
							
							if ((ano % 4 == 0) || (ano % 100 == 0) || (ano % 400 == 0)){ 
								bissexto = 1; 
							} 
							
							if ((bissexto == 1) && (dia >= 1) && (dia <= 29)){ 
								return true;                             
							}					
							if ((bissexto != 1) && (dia >= 1) && (dia <= 28)){ 
								return true; 
							}else{
								ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
								document.getElementById('erroDataNascimento').style.display = '';
								return false;
							}                       
						break 
					}
				}else{
					ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
					document.getElementById('erroDataNascimento').style.display = '';
					return false;
				} 
			}else{
				ocument.getElementById('dataNascimentoFuncionario').style.background = '#FBE1E1';
				document.getElementById('erroDataNascimento').style.display = '';
				return false;
			} 
		}
	}
	
	if (nform.cpfFuncionario.value != ""){	
	
		var CPF = new String(nform.cpfFuncionario.value);
			// Remove todos os caracteres à seguir: ( ) / - . e espaço, para tratar a string denovo.
		CPF = CPF.replace(/(\.|\(|\)|\/|\-| )+/g,'');
	
		// Aqui começa a checagem do CPF
		var POSICAO, I, SOMA, DV, DV_INFORMADO;
		var DIGITO = new Array(10);
		DV_INFORMADO = CPF.substr(9, 2); // Retira os dois últimos dígitos do número informado
	
		// Desemembra o número do CPF na array DIGITO
		for (I=0; I<=8; I++) {
		  DIGITO[I] = CPF.substr( I, 1);
		}
	
		// Calcula o valor do 10º dígito da verificação
		POSICAO = 10;
		
		SOMA = 0;
		
		   for (I=0; I<=8; I++) {
			  SOMA = SOMA + DIGITO[I] * POSICAO;
			  POSICAO = POSICAO - 1;
		   }
		   
		DIGITO[9] = SOMA % 11;
		   if (DIGITO[9] < 2) {
				DIGITO[9] = 0;
		   }else{
			   DIGITO[9] = 11 - DIGITO[9];
		   }
	
		// Calcula o valor do 11º dígito da verificação
		POSICAO = 11;
		SOMA = 0;
		   for (I=0; I<=9; I++) {
			  SOMA = SOMA + DIGITO[I] * POSICAO;
			  POSICAO = POSICAO - 1;
		   }
		   
		DIGITO[10] = SOMA % 11;
		
		   if (DIGITO[10] < 2) {
				DIGITO[10] = 0;
		   }else {
				DIGITO[10] = 11 - DIGITO[10];
		   }
	
		// Verifica se os valores dos dígitos verificadores conferem
		DV = DIGITO[9] * 10 + DIGITO[10];
		
		if (DV != DV_INFORMADO) {
			document.getElementById('cpfFuncionario').style.background = '#FBE1E1';
			document.getElementById('erroCpf').style.display = '';
		  return false;
		}
	}

	if (nform.emailFuncionario.value != ""){

      email = /^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
          
      if(email.exec(nform.emailFuncionario.value))
                {
                  
                } else {
					document.getElementById('emailFuncionario').style.background = '#FBE1E1';
					document.getElementById('erroEmail').style.display = '';
                  	return false;
                }
      }
	  */
}

//--->Função para verificar forumario Login...<---

function validarLogin(nform){

	if (nform.usuario.value == ""){
		document.getElementById('usuario').style.background = '#FBE1E1';
		document.getElementById('erroUsuario').style.display = '';
		return false;
	}

	if (nform.senha.value == ""){
		document.getElementById('senha').style.background = '#FBE1E1';
		document.getElementById('erroSenha').style.display = '';
		return false;
	}
}

//--->Função para verificar forumario Vendas Primeira Parte...<---

function validarVendasSegundaParte(nform){

	if (nform.valorCompra.value == ""){
		document.getElementById('valorCompra').style.background = '#FBE1E1';
		document.getElementById('erroValorCompra').style.display = '';
		return false;
	}

	if (nform.dataVencimento.value == ""){
		document.getElementById('dataVencimento').style.background = '#FBE1E1';
		document.getElementById('erroDataVencimento').style.display = '';
		return false;
	}
}
