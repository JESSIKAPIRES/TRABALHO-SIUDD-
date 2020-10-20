<?php
 // require_once 'CLASSES/usuarios.php'; 
 //$u = new Usuario;

// verificar se o usuario clicou no botao cadastrar
if (isset($_POST['nome']))
{
      $nome = addslashes($_POST['nome']);
      $data_nasc = addslashes($_POST['data_nasc']);
      $cpf = addslashes($_POST['cpf']);
      $rg = addslashes($_POST['rg']);
      $cep = addslashes($_POST['cep']);
      $rua = addslashes($_POST['rua']);
      $bairro = addslashes($_POST['bairro']);
      $cidade = addslashes($_POST['cidade']);
      $estado = addslashes($_POST['uf']);
      $pais = addslashes($_POST['pais']);
      $telefone = addslashes($_POST['telefone']);
      $email = addslashes($_POST['email']);
      $senha = addslashes($_POST['senha']);
      $confirmarSenha = addslashes($_POST['confSenha']);

      // verificar se o usuario preencheu todos os campos do cadastro

      function senhaValida($senha) {
         return preg_match('/[a-z]/', $senha)       // tem pelo menos uma letra minúscula
         && preg_match('/[A-Z]/', $senha)           // tem pelo menos uma letra maiúscula
         && preg_match('/[0-9]/', $senha)           // tem pelo menos um número
         && preg_match('/^[\w$@]{6,}$/', $senha);   // tem 6 ou mais caracteres
        }

    if(!empty($nome) && !empty($data_nasc) &&!empty($cpf)&&!empty($rg)&&!empty($cep)&&!empty($rua)&&!empty($bairro)&&!empty($cidade)&&!empty($pais)&&!empty($telefone)&&!empty($email)&&!empty($senha)&&!empty($confirmarSenha))
    {

        function validaCPF($cpf = null){
    
    $cpf = preg_replace("/[^0-9]/", "", $cpf);
	$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
	
	if (strlen($cpf) != 11) {
		return false;
	}
	// Verifica se nenhuma das sequências invalidas abaixo 
	// foi digitada. Caso afirmativo, retorna falso
	else if ($cpf == '00000000000' || 
		$cpf == '11111111111' || 
		$cpf == '22222222222' || 
		$cpf == '33333333333' || 
		$cpf == '44444444444' || 
		$cpf == '55555555555' || 
		$cpf == '66666666666' || 
		$cpf == '77777777777' || 
		$cpf == '88888888888' || 
		$cpf == '99999999999') {
		return false;
	 // Calcula os digitos verificadores para verificar se o
	 // CPF é válido
	 } else {   
		
		for ($t = 9; $t < 11; $t++) {
			
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) {
				return false;
			}
		}
		return true;
    }
}

       /* Conexao com o banco de dados */
 $dsn = 'mysql:dbname=siud_login;host=127.0.0.1';
 $user = 'root';
 $password = '';

    if (validaCPF($cpf) == false){
        echo "O CPF é invalido";
    }
else 
{

    if (senhaValida($senha) == false) {
    echo "A senha é muito fraca";
    }
else 
{

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
     try {
     $pdo = new PDO($dsn, $user, $password);
        if ($senha == $confirmarSenha)
        {
         $sql = $pdo-> prepare("INSERT INTO usuarios (nome, data_nasc, cpf, rg, cep, rua, bairro,
         cidade, estado, pais, telefone, email, senha) 
         VALUES ( :n, :dt, :cp, :r, :ce, :ru, :b, 
         :c, :es, :pa,:t, :e, :s) ");
         $sql->bindValue(":n", $nome);
         $sql->bindValue(":dt", $data_nasc);
         $sql->bindValue(":cp", $cpf);
         $sql->bindValue(":r", $rg);
         $sql->bindValue(":ce", $cep);
         $sql->bindValue(":ru", $rua);
         $sql->bindValue(":b", $bairro);
         $sql->bindValue(":c", $cidade);
         $sql->bindValue(":es", $estado);
         $sql->bindValue(":pa", $pais);
         $sql->bindValue(":t", $telefone);
         $sql->bindValue(":e", $email);
         $sql->bindValue(":s", $senha);
         $sql->execute();

		 echo "<h3 color='red'> Cadastro realizado  com sucesso!</h3>";
         die();  
        }

            else {
            echo "Senha e confirmar Senha não correspodem";
            }
        
    } 
    
        catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        }
     } 
     else {
        echo "Email Invalido!"; 
        }
 }                                                       //fecha o else do verificador de senha
 }                                                       //fecha o else do verificador de CPF
 }                                                       //fecha o verificador de valor nulo
    else {
    echo "Preencha todos os valores";
    }
 
}                                                       
?>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title> ENTRADA </title>
    <link rel="stylesheet" type="text/css" href="CSS/estilo.css" />
</head>

  <!-- Auto Preenchimento do Endereço por CEP -->
 <script>
    
    function limpa_formulário_cep() {
            //Limpa valores do formulário de cep.
            document.getElementById('rua').value=("");
            document.getElementById('bairro').value=("");
            document.getElementById('cidade').value=("");
            document.getElementById('uf').value=("");
    }

    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            //Atualiza os campos com os valores.
            document.getElementById('rua').value=(conteudo.logradouro);
            document.getElementById('bairro').value=(conteudo.bairro);
            document.getElementById('cidade').value=(conteudo.localidade);
            document.getElementById('uf').value=(conteudo.uf);

        } //end if.
        else {
            //CEP não Encontrado.
            limpa_formulário_cep();
            alert("CEP não encontrado.");
        }
    }
        
    function pesquisacep(valor) {

        //Nova variável "cep" somente com dígitos.
        var cep = valor.replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if(validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                document.getElementById('rua').value="...";
                document.getElementById('bairro').value="...";
                document.getElementById('cidade').value="...";
                document.getElementById('uf').value="...";

                //Cria um elemento javascript.
                var script = document.createElement('script');

                //Sincroniza com o callback.
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';

                //Insere script no documento e carrega o conteúdo.
                document.body.appendChild(script);

            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    }
    function fMasc(objeto,mascara) {
				obj=objeto
				masc=mascara
				setTimeout("fMascEx()",1)
			}
			function fMascEx() {
				obj.value=masc(obj.value)
			}
			function mTel(tel) {
				tel=tel.replace(/\D/g,"")
				tel=tel.replace(/^(\d)/,"($1")
				tel=tel.replace(/(.{3})(\d)/,"$1)$2")
				if(tel.length == 9) {
					tel=tel.replace(/(.{1})$/,"-$1")
				} else if (tel.length == 10) {
					tel=tel.replace(/(.{2})$/,"-$1")
				} else if (tel.length == 11) {
					tel=tel.replace(/(.{3})$/,"-$1")
				} else if (tel.length == 12) {
					tel=tel.replace(/(.{4})$/,"-$1")
				} else if (tel.length > 12) {
					tel=tel.replace(/(.{4})$/,"-$1")
				}
				return tel;
			}

			function mCPF(cpf){
				cpf=cpf.replace(/\D/g,"")
				cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")          //A cada 3 casas coloca um . 
				cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
				cpf=cpf.replace(/(\d{3})(\d{2,3})$/,"$1-$2")    //A cada 3 casa coloca um . e dps de 3 casas coloca um - dps de 2 casas
				return cpf
			}
			function mCEP(cep){
				cep=cep.replace(/\D/g,"")
				cep=cep.replace(/^(\d{2})(\d)/,"$1.$2")
				cep=cep.replace(/\.(\d{3})(\d)/,".$1-$2")
				return cep
			}
			function mRG(rg){
                rg=rg.replace(/\D/g,"");
                rg=rg.replace(/(\d{2})(\d)/,"$1.$2")
                rg=rg.replace(/(\d{3})(\d)/,"$1.$2")
                rg=rg.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
				return rg
			}
		</script>

<body back-color=" #FFCC99">

    <div id="corpo-form-Cad">

        <h1> CADASTRAR </h1>

        <form method="post">

            <input type = "hidden" name="acao" value= "cadastro"/>
            <input type="text" name="nome" placeholder=" Nome Completo " maxlenggth="40" />
            <input type="date" name="data_nasc" placeholder=" Data de Nascimento " maxlenggth="8" />
            <input type="text" name="cpf" placeholder=" CPF " maxlenggth="16" onkeydown = "javascript: fMasc (this, mCPF);"/>
            <input type="text" name="rg" placeholder=" RG " maxlenggth="12" onkeydown = "javascript: fMasc (this, mRG);" />
            <input type="text" id ="cep" value = "" name="cep" placeholder=" CEP " maxlenggth="10" 
            onkeydown = "javascript: fMasc (this, mCEP)"; onblur="pesquisacep(this.value);"/>

            <input type="text" name="rua" id="rua" placeholder=" Rua e numero" maxlenggth="40" />
            <input type="text" name="bairro" id="bairro" placeholder=" Bairro" maxlenggth="40" />
            <input type="text" name="cidade" id = "cidade" placeholder=" Cidade " maxlenggth="40" />
            <input type="text" name="uf" id = "uf" placeholder=" Estado " maxlenggth="40" />
            <input type="text" name="pais" placeholder=" País " maxlenggth="40" />
            <input type="text" name="telefone" placeholder="Telefone" maxlenggth="30" onkeydown = "javascript: fMasc (this, mTel);"/>
            <input type="email" name="email" placeholder="Usuário" maxlenggth="40" />

            <input type="password" name="senha" placeholder="Senha" maxlenggth="15">
            <input type="password" name="confSenha" placeholder="Confirmar Senha" maxlenggth="15" />

            <input value="CADASTRAR" type="submit" />
        </form>
    </div>
</body>
</html>