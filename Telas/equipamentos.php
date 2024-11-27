<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1>Equipamentos (EPI's)</h1>
    </div>
<div class="col-sm-6">
    <button onclick="abrirModal()" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionar_equipamento">
        <i class="bi bi-plus"></i> Novo Equipamento
    </button>
</div>
    <hr class="my-4">
 <!-- Abas para Ativos e Inativos -->
<ul class="nav nav-tabs" id="colaboradoresTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="ativos-tab" data-bs-toggle="tab" href="#ativos" role="tab" aria-controls="ativos" aria-selected="true">Ativos</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="inativos-tab" data-bs-toggle="tab" href="#inativos" role="tab" aria-controls="inativos" aria-selected="false">Inativos</a>
    </li>
</ul>
<!-- Conteúdo das Abas -->
<div class="tab-content" id="colaboradoresTabContent">
    <!-- Colaboradores Ativos -->
    <div class="tab-pane fade show active" id="ativos" role="tabpanel" aria-labelledby="ativos-tab">
    <div class="table-responsive">
        <table class="table table-striped table-hover">  
            <thead>
                <tr class="text-center">
                    <th scope="col">ID</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Qtd. em Estoque</th>
                    <th scope="col">Emprestados</th>
                    <th scope="col">Certificado Aprovação</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    try
                    {
                        include_once 'src/class/BancodeDados.php';
                        $banco = new BancodeDados;
                        $sql = 'SELECT equipamentos.id_equipamento
                                     , equipamentos.descricao
                                     , equipamentos.qtd_estoque AS estoque_total
                                     , COALESCE(SUM(CASE WHEN emprestimos.situacao = 1 THEN 1 ELSE 0 END), 0) AS emprestados
                                     , equipamentos.certificado_aprovacao
                                     , equipamentos.imagem_equipamento
                                     FROM equipamentos
                                     LEFT JOIN equipamentos_emprestimo ON equipamentos.id_equipamento = equipamentos_emprestimo.equipamento
                                     LEFT JOIN emprestimos ON equipamentos_emprestimo.emprestimo = emprestimos.id_emprestimo AND emprestimos.situacao = 1
                                     WHERE equipamentos.ativo = 1
                                     GROUP BY equipamentos.id_equipamento, equipamentos.descricao, equipamentos.qtd_estoque';
                        $dados = $banco -> Consultar($sql,[], true);
                        if($dados)
                        {
                            foreach ($dados as $linha)
                            {
                                $caminho_imagem = 'src/equipamentos/upload/' . $linha['imagem_equipamento'];
                                $imagem_existe = file_exists($caminho_imagem);
                                echo
                                "<tr class='text-center'>
                                    <td>{$linha['id_equipamento']}</td>
                                    <td>{$linha['descricao']}</td>
                                    <td>{$linha['estoque_total']}</td>
                                    <td>{$linha['emprestados']}</td>
                                    <td>{$linha['certificado_aprovacao']}</td>
                                    <td>
                                        " . ($imagem_existe ? "<a href='#' onclick='AbrirImagem(\"{$caminho_imagem}\")'><i class='bi bi-image'></i></a>" : "<i class='bi bi-image' style='color: gray;'></i>") . "
                                        <a href='#' onclick='GerarCodigoBarras({$linha['id_equipamento']})'><i class='bi bi-upc-scan'></i></a>
                                        <a href='#' onclick='AlterarEquipamento({$linha['id_equipamento']})'><i class='bi bi-pencil-square'></i></a>
                                        <a href='#' onclick='GetAjustarEstoque({$linha['id_equipamento']})'><i class='bi bi-dropbox'></i></a>  
                                        <a href='#' onclick='ExcluirEquipamento({$linha['id_equipamento']})'><i class='bi bi-trash3-fill'></i></a>
                                    </td>
                                </tr>";
                            }
                        }
                        else
                        {
                            echo 
                            "<tr>
                                <td colspan = '7' class='text-center'>Nenhum equipamento cadastrado</td>
                            </tr>";                   
                        }
                    }
                    catch(PDOException $erro)
                    {
                        $msg = $erro->getMessage();
                        echo
                        "<script>
                            alert(\"$msg\");
                        </script>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    </div>
    <!-- Colaboradores Inativos -->
    <div class="tab-pane fade" id="inativos" role="tabpanel" aria-labelledby="inativos-tab">
        <div class="table-responsive">
        <table class="table table-striped table-hover">  
            <thead>
                <tr class="text-center">
                    <th scope="col">ID</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Qtd. em Estoque</th>
                    <th scope="col">Certificado Aprovação</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    try
                    {
                        include_once 'src/class/BancodeDados.php';
                        $banco = new BancodeDados;
                        $sql = 'SELECT * FROM equipamentos WHERE ativo = 0';
                        $dados = $banco -> Consultar($sql,[], true);
                        if($dados)
                        {
                            foreach ($dados as $linha)
                            {
                                echo
                                "<tr class='text-center'>
                                    <td>{$linha['id_equipamento']}</td>
                                    <td>{$linha['descricao']}</td>
                                    <td>{$linha['qtd_estoque']}</td>
                                    <td>{$linha['certificado_aprovacao']}</td>
                                    <td>
                                        <a href='#' onclick='ReativarEquipamento({$linha['id_equipamento']})'>Reativar</a>
                                    </td>
                                </tr>";
                            }
                        }
                        else
                        {
                            echo 
                            "<tr>
                                <td colspan = '6' class='text-center'>Nenhum equipamento inativado</td>
                            </tr>";                   
                        }
                    }
                    catch(PDOException $erro)
                    {
                        $msg = $erro->getMessage();
                        echo
                        "<script>
                            alert(\"$msg\");
                        </script>";
                    }
                ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<!--Modal Principal-->
<div id="adicionar_equipamento" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_equipamento" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Equipamento</h4>
                    <button onclick="window.location.reload()" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="txt_id" value="NOVO">
                    <div class="form-group">
                        <label for="txt_descricao">Descrição</label>
                        <input type="text" class="form-control" id="txt_descricao" required varchar="255">
                    </div>
                    <div class="form-group">
                        <label for="txt_estoque">Qtd. Estoque</label>
                        <input type="number" class="form-control" id="txt_estoque" required value="0">
                    </div>
                    <div class="form-group">
                        <label for="txt_cert_aprovacao">Certificado Aprovação</label>
                        <input type="text" class="form-control" id="txt_cert_aprovacao" required>
                    </div>  
                    <div class="form-group">
                        <label for="file_imagem">Imagem do Equipamento</label>
                        <input type="file" class="form-control" id="file_imagem" value="S/IMG">
                        <button class="btn btn-danger" id="btnLimparImagem" onclick="LimparImagem()">Limpar Imagem</button>
                    </div>
                    <div class="form-group" id="imagemContainer">
                        <img id="caminho_imagem" class="img-thumbnail" height="300">
                        <button class="btn btn-danger" id="btnDesvincular" onclick="DesvincularImagem()">Desvincular Imagem</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="CadastrarEquipamento()" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Modal Ajuste de Estoque-->
<div id="ajuste_estoque" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_ajuste_equipamento" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalLabel">Ajuste de Estoque</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="txt_id_estoque" id="txt_id_estoque" value="NOVO">
                    <div class="form-group">
                        <label for="txt_descricao_estoque">Descrição</label>
                        <input type="text" class="form-control" name="txt_descricao_estoque" id="txt_descricao_estoque" required varchar="255" readonly>
                    </div>
                    <div class="form-group">
                        <label for="txt_estoque">Qtd. Estoque</label>
                        <input type="number" class="form-control" id="txt_estoque_ajuste" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="txt_new_qtd_estoque">Qtd. Ajuste</label>
                        <input type="number" class="form-control" name="txt_new_qtd_estoque" id="txt_new_qtd_estoque" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="AjustarEstoque()" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal para código de barras -->
<div id="codigo_barras" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> 
                <h4 class="modal-title" id="modalLabel">Código de barras</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <img src="" id="img_barras">
        </div>
    </div>
</div>
<!-- Modal para imagem de equipamento -->
<div id="imagem_equipamento" class="modal fade" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalLabel">Imagem do Equipamento</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> 
                <img id="visualiza_imagem" alt="Imagem do Equipamento" class="img-fluid" style="display:block" height="500px" width="500px">
            </div>
        </div>
    </div>
</div>
<script src="assets/js/equipamentos.js"></script>