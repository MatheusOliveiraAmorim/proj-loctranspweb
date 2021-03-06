<?php
    require_once("include/initialize.php");    
    require_once("include/classes/tbl_usuario.php");
    require_once("include/classes/tbl_publicacao.php");
        
    $idUsuario = ( isset($_GET["id"]) )? (int) $_GET["id"] : null;
    
    $detalhes_usuario = new \Tabela\Usuario();
    $detalhes_usuario = $detalhes_usuario->getDetalhesUsuario("u.id = {$idUsuario}")[0];    
?>
<!doctype html>
<html>
    <head>
        <title>Perfil de <?php echo $detalhes_usuario->nome . " " . $detalhes_usuario->sobrenome[0]; ?> |City Share</title>
        <meta name="viewport" content="width=device-width" />
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css">
        <link rel="icon" href="img/icones/logoCityShareIcon.png">
    </head>
    <body>
        <div id="container">            
            <?php require_once("layout/header.php"); ?>
            <div class="main" id="pag-perfil-usuario">
                <div class="box-conteudo">
                    <div id="box-info-usuario">
                        <div id="box-info-pessoal-usuario">
                            <div id="box-foto">
                                <img id="foto-usuario" src="img/link_face.jpg"/>
                            </div>
                            <section id="box-info">
                                <h1 id="nome"><?php echo $detalhes_usuario->nome . " " . $detalhes_usuario->sobrenome; ?></h1>
                                <p class="label-info">Localização: <span class="info"><?php echo $detalhes_usuario->estado . ", " . $detalhes_usuario->cidade; ?></span></p>
                                <p class="label-info">Empréstimos: <span class="info"><?php echo $detalhes_usuario->qtdEmprestimos; ?></span></p>
                                <p class="label-info">Locações: <span class="info"><?php echo $detalhes_usuario->qtdLocacoes; ?></span></p>
                                <div class="container-icone-avaliacoes">
                                    <div class="icone-avaliacao"></div>
                                    <div class="icone-avaliacao"></div>
                                    <div class="icone-avaliacao"></div>
                                    <div class="icone-avaliacao"></div>
                                    <div class="icone-avaliacao"></div>
                                </div>
                            </section>
                        </div>                        
                    </div>
                    <section id="container-publicacoes">
                        <div class="wrapper-publicacoes"></div>
                        <div id="botao-ver-mais" class="js-load-publicacao"></div>
                    </section>
                </div>                
            </div>
            <!-- CONTEUDO PRINCIPAL -->
        </div>
        <?php require_once("layout/footer.php"); ?>
        <script src="js/libs/jquery-3.1.1.min.js"></script>
        <script src="js/script.js"></script>
    </body>
</html>