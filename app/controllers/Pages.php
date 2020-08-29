<?php

class Pages extends Controller
{
    public function __construct() {
    }

    public function index() {

        if(isLoggedIn()) {
            redirect('posts');
        }

        $data = [
            'title' => 'SharePosts',
            'description' => 'Rede social simples feita utilizando arquitetura MVC com PHP'
        ];


        

        $this->view('pages/index', $data);
    }

    public function about() {
        $data = [
            'title' => 'Sobre nós',
            'description' => 'App para compartilhar posts com outros usuarios'
        ];

        $this->view('pages/about', $data);
    }
}
