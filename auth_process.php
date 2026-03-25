<?php

    require_once("globals.php");
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);

    // Verifica o tipo
    $type = filter_input(INPUT_POST, "type");

    //Verificação do tipo de formulario
    if($type === "register") {

        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        //Verificação de dados minimos
        if($name && $lastname && $email && $password) {

            //Verifica se as senhas são iguais
            if($password === $confirmpassword) {

                //Verifica se o email existe
                if($userDao->findByEmail($email) === false){

                    $user = new User();

                    //Criação de token e senha
                    $userToken = $user->generateToken();
                    $finalPassword = password_hash($password, PASSWORD_DEFAULT);

                    $user->name = $name;
                    $user->lastname = $lastname;
                    $user->email = $email;
                    $user->password = $finalPassword;
                    $user->token = $userToken;

                    $auth = true;
                    $userDao->create($user, $auth);

                } else {

                    //Enviar msg de erro
                    $message->setMessage("Usuario já cadastrado.", "error", "back");
                }

            } else {

                //Enviar msg de erro, de senhas diferentes
                $message->setMessage("As senhas não são iguais.", "error", "back");

            }

        } else {

            //Enviar msg de erro
            $message->setMessage("Por Favor, preencha todos os campos.", "error", "back");
        }

    } else if($type === "login") {

        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        //Tenta autenticar 
        if($userDao->authenticateUser($email, $password)) {

           $message->setMessage("Seja bem vindo!", "success", "editprofile.php");
            
            //redireciona o usuario
        } else {
          
            $message->setMessage("Usúario e/ou senha incorretos. ", "error", "back");

        }
    } else {
        $message->setMessage("Informações inválidas.", "error", "index.php");
    }