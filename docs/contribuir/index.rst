.. API e-Gens documentation master file, created by
   sphinx-quickstart on Fri Dec  7 13:57:01 2018.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Como começar a desenvolver
==========================

.. toctree::
   :maxdepth: 2
   :caption: Conteúdo:

Criando usuário de testes
-------------------------

O sistema ainda não disponibiliza criação de usuários via API. Para criar seu usuário de testes,
após preparar o ambiente e popular o Banco de Dados com as tabelas utilizando-se das migrations.

Você precisará usar o Tinker, que já está instalado junto com o Laravel.

.. code-block:: guess

    php artisan tinker


Um vez com o Tinker aberto, você precisará criar um novo usuário e em seguida poderá acessar os
métosos PHP diretamente para criar seu novo usuário de testes.

.. code-block:: guess

    $usuario = new User();
    $usuario->create([
        'name'=>'NomeUsuario',
        'email'=>'email@mail.com',
        'password' => Hash::make('123456')
        ]);
