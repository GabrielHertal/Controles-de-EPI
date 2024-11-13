<?php
header('Content-Type: application/json');

include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $jsonInput = file_get_contents('php://input');
        $formulario = json_decode($jsonInput, true);

        $mensagem = '';

        include '../class/BancodeDados.php';
        $banco = new BancodeDados;

        $banco->start_transaction();

        if ($formulario['id'] == "NOVO") {
            $idEmprestimo = CadastrarEmprestimo($formulario, $banco);
            if ($idEmprestimo != 0) {
                foreach ($formulario['itens'] as $equipamento) {
                    $quantidade = $equipamento['quantidade'];
                    // VerificarEstoque( $equipamento['id']);
                    CadastrarEquipamentosEmprestimo($idEmprestimo, $equipamento['id'], $banco, $quantidade);
                }
            }
            $banco->commit();
            $mensagem = 'Empréstimo cadastrado com sucesso!';
        } else {
            
            $mensagem = 'Empréstimo atualizado com sucesso!';
        }

        echo json_encode([
            'codigo' => 2,
            'mensagem' => $mensagem
        ]);

    } catch (PDOException $erro) {
        $banco->rollback();
        $msg = $erro->getMessage();
        echo json_encode([
            'codigo' => 0,
            'mensagem' => "Erro ao realizar registro: $msg"
        ]);
    }
}


