<?php
class Usuario
{
    private $pdo;
    public $msgErro = " "; // tudo ok
    public function conectar($nome, $host, $usuario, $senha) // para conectar ao sistema
    
    {
            global $pdo;

            try
            {
                $pdo = new PDO("mysql:dbname=".$nome.";host=".$host,$usuario,$senha);
            } catch (PDOException $e){
               $msgErro = $e-> getMessage();
            }   
    }

    public function cadastrar($nome, $dataNasc, $cpf, $rg, $cep, $rua, $bairro, $cidade, $estado, $pais, $telefone, $email, $senha )
    {
            global $pdo;

                    // verificar se já existe o email cadastrado

            $sql = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :e");
            $sql->bindValue(":e", $email);
            $sql->execute();
            if($sql->rowCount() > 0)
            {
                return false; //significa que a pessoa ja esta cadastrada
            }
            else
            {   // caso não possua, cadastrar

                $sql = $pdo-> prepare("INSERT INTO usuarios (nome, dataNasc, cpf, rg, cep, rua, bairro, cidade, estado, pais, telefone, email, senha) VALUES ( :n, :dt, :cp, :r, :ce, :ru, :b, :c, :es, :pa,:t, :e, :s) ");
                $sql->bindValue(":n", $nome);
                $sql->bindValue(":dt", $dataNasc);
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
                $sql->bindValue(":s", md5($senha));
                $sql->execute();
                return true; 
            }
    }

    public function logar($email, $senha)

    {
        global $pdo;

        // verificar se o email e a senha estão cadastrados, se sim
        $sql = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :e AND senha = :s");
        $sql->bindValue(":e", $email);
        $sql->bindValue(":s", md5 ($senha));
        $sql->execute();
        if ($sql->rowCount() > 0)
        {
               // entrar no sistema (sessão)
            $dado = $sql->fetch();
            session_start();
            $_SESSION['id_usuario'] = $dado['id_usuario'];
            return true; //a pessoa esta cadastrada no sistema, logado com sucesso
        }
        else
        {
            session_destroy();
            return false; // não foi possível logar no sistema
        }
        

    }

}


?>

