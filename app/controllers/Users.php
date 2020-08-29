<?php

class Users extends Controller
{
    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        //Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Process form

            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            //Init data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            //Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Por favor insira seu email';    
            } else {
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Este email já está em uso';
                }
            }

            //Validate name
            if(empty($data['name'])) {
                $data['name_err'] = 'Por favor inserir nome';    
            }

            //Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Por favor inserir senha';    
            } elseif(strlen($data['password']) < 6) {
                $data['password_err'] = 'Sua senha deve conter pelo menos 6 caracteres'; 
            }

            //Validate confim password
            if(empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Por favor confimar senha';    
            } else {
                if($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Senhas não coincidem';
                }
            }

            //Make sure error are empty
            if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                //Validated
                
                //Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                //Register User
                if($this->userModel->register($data)) {
                    flash('register_success', 'Você está registrado e pode fazer login agora.');
                    redirect('users/login');
                } else {
                    die('Algo deu errado');
                }

            } else {
                //Load view with errors
                $this->view('users/register', $data);
            }

        } else {
            //Init data
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            //Load view
            $this->view('users/register', $data);
        }
    }

    public function login() {
        //Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Process form

            //Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            //Init data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),

                'email_err' => '',
                'password_err' => ''
            ];

            //Validate email
            if(empty($data['email'])) {
                $data['email_err'] = 'Por favor inserir email';    
            }

            //Validate password
            if(empty($data['password'])) {
                $data['password_err'] = 'Por favor inserir senha';    
            }

            //Check for user/email
            if($this->userModel->findUserByEmail($data['email'])) {
                //Validated
                //Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                
                if($loggedInUser) {
                    //Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Senha incorreta';

                    $this->view('users/login', $data);
                }

            } else {
                $data['email_err'] = 'No user found';
            }

            //Make sure error are empty
            if(empty($data['email_err']) && empty($data['password_err'])) {
                //Validated
                die('Success');
            } else {
                //Load view with errors
                $this->view('users/login', $data);
            }


        } else {
            //Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            //Load view
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;

        redirect('posts');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        
        session_destroy();
        redirect('users/login');
    }
}
