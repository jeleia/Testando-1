<?php

Class CadastroUsuario
{
  public $pdo;

  public function cadastrar($nome, $CPF_Usuario, $materia, $email, $cidade, $endereço, $senha)
  {
    global $pdo;

    //verificar se ja existe o email cadastrado
    $sql = $pdo->prepare("SELECT ID_Usuario FROM usuario_professor WHERE Email = :e");
    $sql->bindValue(":e", $email);
    $sql->execute();
    if($sql->rowCount() > 0){
      return false; //ja esta cadastrada
    }
    else{
      //caso nao exista, verificar o prontuario
      $sql = $pdo->prepare("SELECT Prontuario FROM professor WHERE CPF = :c");
      $sql->bindValue(":c", $CPF_Usuario);
      $sql->execute();
        if($sql->rowCount() > 0){
          //Caso exista, cadastrar com o prontuario
          //verificar qual o id da cidade
          $sql = $pdo->prepare("SELECT Cod FROM cidade WHERE Nome = :c");
          $sql->bindValue(":c", $cidade);
          $sql->execute();
          $idCidade = $sql->fetch(\PDO::FETCH_ASSOC);

          $sql = $pdo->prepare("INSERT INTO usuario_professor (Prontuario_Professor, NickName, CPF_Usuario, Email, Cidade_Cod,Endereço, Senha) VALUES (:p, :n, :c, :e, :cid,:en, :s)");
          $query = $pdo->prepare("SELECT Prontuario FROM professor WHERE CPF = :c");
          $query->bindValue(":c", $CPF_Usuario);
          $query->execute();
          $row = $query->fetch();

          $sql->bindValue(":p", $row["Prontuario"]);
          $sql->bindValue(":n", $nome);
          $sql->bindValue(":c", $CPF_Usuario);
          $sql->bindValue(":e", $email);
          $sql->bindValue(":cid", $idCidade["Cod"]);
          $sql->bindValue(":en", $endereço);
          $sql->bindValue(":s", md5($senha));
          $sql->execute();

          //verificar qual o id da materia
          $sql = $pdo->prepare("SELECT ID_Materia FROM Materia WHERE Nome = :m");
          $sql->bindValue(":m", $materia);
          $sql->execute();
          $idMateria = $sql->fetch(\PDO::FETCH_ASSOC);

          $sql = $pdo->prepare("INSERT INTO usuario_materia (Usuario_Professor_ID_Usuario_, Materia_ID_Materia) VALUES (:u, :m)");
          $sql->bindValue(":u", $idUsuario);
          $sql->bindValue(":m", $idMateria["ID_Materia"]);
          $sql->execute();

          $pdo = null;
          return true;
      }


        else{

          //cadastrar sem prontuario
          $sql = $pdo->prepare("INSERT INTO usuario_professor (NickName, CPF_Usuario, Email, Cidade_Cod, Endereço, Senha) VALUES (:n, :c, :e, :cid :en, :s)");
          $sql->bindValue(":n", $nome);
          $sql->bindValue(":c", $CPF_Usuario);
          $sql->bindValue(":e", $email);
          $sql->bindValue(":cid", $cidade);
          $sql->bindValue(":en", $endereço);
          $sql->bindValue(":s", md5($senha));
          $sql->execute();
          $idUsuario = $pdo->lastInsertId();

          //verificar qual o id da materia
          $sql = $pdo->prepare("SELECT ID_Materia FROM Materia WHERE Nome = :m");
          $sql->bindValue(":m", $materia);
          $sql->execute();
          $idMateria = $sql->fetch(\PDO::FETCH_ASSOC);

          $sql = $pdo->prepare("INSERT INTO usuario_materia (Usuario_Professor_ID_Usuario_, Materia_ID_Materia) VALUES (:u, :m)");
          $sql->bindValue(":u", $idUsuario);
          $sql->bindValue(":m", $idMateria["ID_Materia"]);
          $sql->execute();

          return true;
      }
    }
  }

  public function validprontuario($nome, $prontuario, $CPF){

    global $pdo;

    //verificar se ja existe o cpf cadastrado
    $sql = $pdo->prepare("SELECT Prontuario FROM professor WHERE CPF = :c");
    $sql->bindValue(":c", $CPF);
    $sql->execute();
    if($sql->rowCount() > 0){
      return false; //ja esta cadastrada
    }
    else{
      //caso nao exista, cadastrar
      $sql = $pdo->prepare("INSERT INTO professor (Nome, Prontuario, CPF) VALUES (:n, :p, :c)");
        $sql->bindValue(":n", $nome);
        $sql->bindValue(":p", $prontuario);
        $sql->bindValue(":c", $CPF);
        $sql->execute();

      return true;
    }
  }

  public function verification($CPF, $prontuario){

    global $pdo;

    //verificar se existe um CPF desses na tabela usuarios
    $sql = $pdo->prepare("SELECT ID_Usuario FROM usuario_professor WHERE CPF_Usuario = :c");
    $sql->bindValue(":c", $CPF);
    $sql->execute();
    if($sql->rowCount() > 0){
      $sql = $pdo->prepare("UPDATE usuario_professor SET Prontuario_Professor = :p WHERE CPF_Usuario = :c");
      $sql->bindValue(":c", $CPF);
      $sql->bindValue(":p", $prontuario);
      $sql->execute();

      return true;
    }
    else{
      return false;
    }

  }

}



?>