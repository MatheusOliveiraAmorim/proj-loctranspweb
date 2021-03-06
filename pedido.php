<?php    
    require_once("include/initialize.php");
    require_once("include/classes/tbl_pedido.php");
    require_once("include/classes/sessao.php");
    require_once("include/classes/tbl_alteracao_pedido.php");

    $idPedido = ( isset( $_GET["id"] ) )? (int) $_GET["id"] : null;
    
    if( empty($idPedido) ) redirecionar_para("index.php");
    
    $sessao = new Sessao();
    $idUsuario = (int) $sessao->get("idUsuario");
        
    $infoPedido = new \Tabela\Pedido();
    $infoPedido = $infoPedido->listarPedidos(null, null, "p.id = {$idPedido}")[0];
    
    $is_locador = $idUsuario == $infoPedido->idUsuarioLocador;
    
    $dataEntregaMarcada = strtotime($infoPedido->dataEntrega);
    $dataEntregaEfetuada = strtotime($infoPedido->dataEntregaEfetuada);
    $diasAtraso = floor( ($dataEntregaEfetuada - $dataEntregaMarcada) / (60 * 60 * 24) );
    $valorAtrasoDiarias = $diasAtraso * $infoPedido->valorDiaria;

    $combustivelRestante = $infoPedido->combustivelRestante / 8;
    $tanqueVeiculo = $infoPedido->tanqueVeiculo;
    $litros_restantes = $combustivelRestante * $tanqueVeiculo;
    $valor_litros = $litros_restantes * $infoPedido->valorCombustivel;

    $quilometragemExcedida = $infoPedido->quilometragemExcedida;
    $valor_quilometragem = $quilometragemExcedida * $infoPedido->valorQuilometragem;  

    $totalPendencias = $valorAtrasoDiarias + $valor_litros + $valor_quilometragem;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Home | City Share</title>
        <meta name="viewport" content="width=device-width" />
        <meta charset="utf-8" />        
        <link rel="stylesheet" href="css/style.css">        
    </head>
    <body>
        <div id="container">
            <?php require_once("layout/header.php"); ?>            
            <div class="main" id="pag-pedido">
                <div id="box-botoes-exibicao">
                    <span id="botao-detalhes" class="preset-botao botao">Detalhes</span>
                    <span id="botao-acoes" class="preset-botao botao">Ações</span>
                    <span id="botao-historico" class="preset-botao botao">Histórico</span>
                </div>
                <div id="box-info">
                    <div id="container-modals">
                        <div class="box-modal-pedido modal js-modal16" id="modal-pagamento-confirmado">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <?php
                                    $titulo = "";
                                    if( $is_locador ) {
                                        $titulo = "Avalie o Locatário";
                                    } else {
                                        $titulo = "Pagamento de Pendências Confirmado!";
                                    }
                                ?>
                                <h1 class="titulo"><?php echo $titulo; ?></h1>                                
                                <p class="conteudo">R$<?php echo str_replace(".", ",", $totalPendencias); ?> foram depositados na conta de <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-avaliacao">
                                    <?php
                                        $label = "";
                                        if( $is_locador ) {
                                            $label = "Avalie o Locatario";
                                        } else {
                                            $label = "Avalie o Locador";
                                        }
                                    ?>
                                    <p class="label"><?php echo $label; ?></p>
                                    <textarea class="txt-comentario-avaliacao js-txt-mensagem-avaliacao" placeholder="Avalie o usuário..."></textarea>
                                    <div class="box-botoes-avaliacao">
                                        <span class="botao-avaliacao"></span>
                                        <span class="botao-avaliacao"></span>
                                        <span class="botao-avaliacao"></span>
                                        <span class="botao-avaliacao"></span>
                                        <span class="botao-avaliacao"></span>
                                    </div>
                                </div>
                                <div class="box-acoes">                                    
                                    <span class="preset-botao botao js-btn-confirmar-avaliacao">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal15" id="modal-pagamento-dinheiro">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Pagamento em Dinheiro</h1>
                                <p class="conteudo">R$<?php echo str_replace(".", ",", $totalPendencias); ?> devem ser pagos à <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar js-modal13">Cancelar</span>
                                    <span class="preset-botao botao js-modal16 js-pagamento-dinheiro">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal14" id="modal-pagamento-cartao">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Pagamento em Cartão de Crédito</h1>
                                <p class="conteudo">Código de Segurança do Cartão:</p>
                                <input class="preset-input-text js-mask js-txt-codigo-seguranca-cartao" type="text" placeholder="Digite o CSC de 3 ou 4 dígitos" data-mask="DDDD"/>
                                <p class="conteudo">A diferença de R$<?php echo str_replace(".", ",", $totalPendencias); ?> será paga à <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar js-modal13">Cancelar</span>
                                    <span class="preset-botao botao js-modal16 js-pagamento-cartao">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal13" id="modal-pagamento-pendencias">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <?php
                                    $titulo = "";
                                    if( $is_locador || ($infoPedido->idStatusPedido == 9) ) {
                                        $titulo = "Pendências Definidas";
                                    } elseif( !$is_locador && ($infoPedido->idStatusPedido == 7 || $infoPedido->idStatusPedido == 8) ) {
                                        $titulo = "Pagamento de Pendências";
                                    }
                                ?>
                                <h1 class="titulo"><?php echo $titulo; ?></h1>
                                <div class="box-label-info">
                                    <p class="label">Atraso:</p>
                                    <p class="info"><?php echo $diasAtraso; ?> dias = R$<?php echo str_replace(".", ",", $valorAtrasoDiarias); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Combustível restante para preenchimento:</p>
                                    <p class="info"><?php echo $litros_restantes; ?> litros = R$<?php echo str_replace(".", ",", $valor_litros); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Distância Excedida:</p>
                                    <p class="info"><?php echo $quilometragemExcedida?> quilômetros = R$<?php echo str_replace(".", ",", $valor_quilometragem); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Total:</p>                                    
                                    <p class="info">R$<?php echo str_replace(".", ",", $totalPendencias); ?></p>
                                </div>                                
                                <?php if( !$is_locador && $infoPedido->idStatusPedido != 9 ) { ?>
                                <div class="box-acoes">
                                    <h1 class="titulo">Forma de Pagamento</h1>
                                    <span class="preset-botao botao btn-avancar js-modal15">Dinheiro</span>
                                    <span class="preset-botao botao btn-avancar js-modal14">Cartão de Crédito</span>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal12" id="modal-confirmacao-pendencias">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Confirmar Pendências</h1>
                                <div class="box-label-info">
                                    <p class="label">Atraso:</p>                                    
                                    <p class="info"><?php echo $diasAtraso; ?> dias = R$<?php echo str_replace(".", ",", $valorAtrasoDiarias); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Combustível restante para preenchimento:</p>                                    
                                    <p class="info"><?php echo $litros_restantes; ?> litros = R$<?php echo str_replace(".", ",", $valor_litros); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Distância Excedida:</p>                                    
                                    <p class="info"><?php echo $quilometragemExcedida?> quilômetros = R$<?php echo str_replace(".", ",", $valor_quilometragem); ?></p>
                                </div>
                                <?php if( !$is_locador ) { ?>
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-btn-pendencias-discordar">Discordo</span>
                                    <span class="preset-botao botao js-modal13 js-btn-pendencias-concordar">Concordo</span>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal11" id="modal-retirada-confirmada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Pendências Enviadas!</h1>
                                <p class="conteudo">Aguarde a confirmação de ~nome do locatário~</p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal10" id="modal-confirmacao-pendencias">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Definir Pendências</h1>
                                <div class="box-label-info">
                                    <p class="label">Atraso:</p>
                                    <p class="info"><?php echo $diasAtraso; ?> dias = R$<?php echo str_replace(".", ",", $valorAtrasoDiarias); ?></p>
                                </div>
                                <p class="conteudo">Combustível restante para preenchimento:</p>
                                <input class="preset-input-text js-mask js-txt-combustivel-restante" type="text" name="txtCombustivelRestante" placeholder="Digite a quantidade restante de combustível" data-mask="D" />
                                <p class="conteudo">Distância Excedida:</p>
                                <input class="preset-input-text js-mask js-txt-distancia-excedida" type="text" name="txtDistanciaExcedida" placeholder="Digite a distância excedida em quilometros" data-mask="DDDDD" />
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-modal11 js-btn-definir-pendencias">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal9" id="modal-retirada-confirmada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Devolução Confirmada!</h1>
                                <p class="conteudo">Prossiga para definir as pendências</p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar js-modal10">Definir Pendências</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal8" id="modal-confirmacao-retirada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Confirmação de Devolução</h1>
                                <p class="conteudo">Após confirmação de devolução, as pendências da locação terão que ser definidas.</p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-modal9 js-btn-confirmar-devolucao">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal7" id="modal-solicitacao-devolucao-enviada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Solicitação de Devolução Enviada!</h1>
                                <p class="conteudo">Após confirmação de devolução, o locador definirá as pendências a serem pagas mediante sua confirmação</p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar js-btn-solicitacao-devolucao">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal6" id="modal-solicitacao-devolucao">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Solicitar Devolução</h1>
                                <div class="box-label-info">
                                    <p class="label">Data de Entrega Marcada:</p>
                                    <p class="info"><?php echo formatar_data(null, $infoPedido->dataEntrega); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Data de Entrega:</p>
                                    <p class="info"><?php echo formatar_data(null, $infoPedido->dataEntregaEfetuada); ?></p>
                                </div>
                                <div class="box-label-info">
                                    <p class="label">Dias atrasados:</p>
                                    <p class="info"><?php echo $diasAtraso; ?></p>
                                </div>
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-modal7 js-btn-solicitar-devolucao">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal5" id="modal-retirada-confirmada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Retirada Confirmada!</h1>
                                <p class="conteudo">R$<?php echo str_replace(".", ",", $infoPedido->diarias * $infoPedido->valorDiaria); ?> serão depositados na conta de <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal4" id="modal-confirmacao-retirada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">                                
                                <h1 class="titulo">Confirmação de Retirada</h1>                                
                                <p class="conteudo">Após confirmação de retirada, o sistema depositará o valor total na conta de <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-modal5 js-btn-confirmar-retirada">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal3" id="modal-solicitacao-retirada-enviada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <h1 class="titulo">Solicitação de Retirada Enviada!</h1>
                                <p class="conteudo">Após confirmação de retirada pelo locador, R$<?php echo str_replace(".", ",", $infoPedido->diarias * $infoPedido->valorDiaria); ?> serão depositados na conta de <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao btn-avancar">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-modal-pedido modal js-modal2" id="modal-solicitacao-retirada">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">                                
                                <h1 class="titulo">Solicitação de Retirada</h1>
                                <p class="conteudo">Digite o código de segurança do cartão cadastrado em sua conta</p>
                                <input class="preset-input-text js-mask js-txt-codigo-seguranca" type="text" name="txtCodigoSeguranca" placeholder="Digite o código de 3 ou 4 digitos" data-mask="DDDD" />
                                <p class="conteudo">Após confirmação de retirada, o sistema depositará o valor total na conta de <?php echo $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador; ?></p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao js-modal3 js-btn-solicitacao-retirada">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <?php if( $infoPedido->idStatusPedido == 2 || $infoPedido->idStatusPedido == 4 ) { ?>
                        <div class="box-modal-pedido modal js-modal1" id="modal-confirmacao-local">
                            <div class="icone"></div>
                            <span class="botao-fechar"></span>
                            <div class="box-info">
                                <?php
                                    $alvo_local = "";
                                    $classe_botao = "";
                                    if( $infoPedido->idStatusPedido == 2 ) {
                                        $alvo_local = "Retirada";
                                        $classe_botao = "js-btn-local-retirada";
                                    } elseif( $infoPedido->idStatusPedido == 4 ) {
                                        $alvo_local = "Entrega";
                                        $classe_botao = "js-btn-local-entrega";
                                    }
                                ?>
                                <h1 class="titulo">Confirmar local de <?php echo $alvo_local; ?></h1>
                                <h2 class="subtitulo">Atenção!</h2>
                                <p class="conteudo">Ao prosseguir, você confirma que o local de <?php echo strtolower($alvo_local); ?> foi negociado com o <?php echo ($is_locador)? "locatário" : "locador" ; ?> após ter contactado o mesmo. A confirmação deve ser feita pelas duas partes, só assim, a <?php echo strtolower($alvo_local); ?> do veículo poderá ser feita.</p>
                                <p class="conteudo">Se você, até o momento, não entrou em contato com o <?php echo ($is_locador)? "locatário" : "locador" ; ?>, NÃO prossiga com a confirmação de definição de local de <?php echo strtolower($alvo_local); ?> do veículo.</p>
                                <div class="box-acoes">
                                    <span class="preset-botao botao <?php echo $classe_botao; ?>">Confirmar</span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div id="container-info-pedido">
                        <div id="box-veiculo-locador">
                            <img id="foto-veiculo" />
                            <div id="info-locador">
                                <p id="modelo-veiculo"><?php echo $infoPedido->veiculo; ?></p>
                                <?php
                                    $nome_completo = "";
                                    if( $is_locador ) {
                                        $nome_completo = $infoPedido->nomeLocatario . " " . $infoPedido->sobrenomeLocatario;
                                    } else {
                                        $nome_completo = $infoPedido->nomeLocador . " " . $infoPedido->sobrenomeLocador;
                                    }
                                ?>
                                <p id="nome-locador"><?php echo $nome_completo; ?></p>
                                <div class="box-avaliacoes">                                    
                                    <div class="container-icone-avaliacoes">
                                        <div class="icone-avaliacao"></div>
                                        <div class="icone-avaliacao"></div>
                                        <div class="icone-avaliacao"></div>
                                        <div class="icone-avaliacao"></div>
                                        <div class="icone-avaliacao"></div>
                                    </div>
                                </div>
                                <div class="box-contato-locador" id="contato-desktop">
                                    <div class="box-contato">
                                       <p class="info-contato">11 9999-9999</p>                                    
                                        <div class="icone-contato telefone"></div>
                                    </div>
                                    <div class="box-contato">
                                       <p class="info-contato">11 9 9999-9999</p>                                    
                                        <div class="icone-contato celular"></div>
                                    </div>
                                    <div class="box-contato">                                    
                                        <p class="info-contato">xxxx@xxxx.com</p>
                                        <div class="icone-contato email"></div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <div class="info-box" id="info-valores">
                            <h1 class="titulo-box">Valores</h1>
                            <div class="box-label-info">
                                <p class="label">Valor da diária:</p>
                                <p class="info">R$<?php echo str_replace(".", ",", $infoPedido->valorDiaria); ?></p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Valor do Combustível (R$/L):</p>
                                <p class="info">R$<?php echo str_replace(".", ",", $infoPedido->valorCombustivel); ?></p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Valor por distância excedida:</p>
                                <p class="info">R$<?php echo str_replace(".", ",", $infoPedido->valorQuilometragem); ?></p>
                            </div>
                        </div>
                        <div class="info-box" id="box-info-diarias">
                            <div class="box-label-info">
                                <p class="label">Data de retirada:</p>
                                <p class="info"><?php echo formatar_data(null, $infoPedido->dataRetirada); ?></p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Data de entrega:</p>
                                <p class="info"><?php echo formatar_data(null, $infoPedido->dataEntrega); ?></p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Valor total de diárias:</p>
                                <p class="info">R$<?php echo str_replace(".", ",", $infoPedido->valorTotal); ?></p>
                            </div>
                            <div class="box-contato-locador" id="contato-mobile">
                                <div class="box-contato">
                                    <?php                                                  
                                        $usuario_alvo = new \Tabela\Usuario();
                                        if( $is_locador ) {                                            
                                            $usuario_alvo = $usuario_alvo->buscar("id = {$infoPedido->idUsuarioLocatario}")[0];
                                        } else {                                            
                                            $usuario_alvo = $usuario_alvo->buscar("id = {$infoPedido->idUsuarioLocador}")[0];
                                        }
                                    ?>
                                    <p class="info-contato"><?php echo $usuario_alvo->telefone; ?></p>                                    
                                    <div class="icone-contato telefone js-botao-contato"></div>
                                </div>
                                <div class="box-contato">
                                    <p class="info-contato"><?php echo $usuario_alvo->celular; ?></p>                                    
                                    <div class="icone-contato celular js-botao-contato"></div>
                                </div>
                                <div class="box-contato">                                    
                                    <p class="info-contato"><?php echo $usuario_alvo->email; ?></p>
                                    <div class="icone-contato email js-botao-contato"></div>
                                </div>
                            </div>
                        </div>
                        <div class="info-box" id="box-info-pendencias">
                            <p class="titulo-box">Pendências</p>
                            <div class="box-label-info">
                                <p class="label">Combustível:</p>
                                <p class="info">R$9,99</p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Quilometragem Excedida:</p>
                                <p class="info">R$9,99</p>
                            </div>
                            <div class="box-label-info">
                                <p class="label">Atraso de entrega:</p>
                                <p class="info">R$9,99</p>
                            </div>
                        </div>
                        <?php if( !$is_locador ) { ?>
                        <div class="info-box" id="box-info-cnh">                            
                            <div class="box-label-info">
                                <p class="label">CNH:</p>
                                <p class="info"><?php echo $infoPedido->numeroCnh; ?></p>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="info-box" id="box-info-status">                            
                            <div class="box-label-info">
                                <p class="label">Status:</p>
                                <p class="info"><?php echo $infoPedido->statusPedido; ?></p>
                            </div>
                        </div>
                    </div>
                    <div id="container-acoes-pedido">
                        <?php
                            $idStatusPedido = $infoPedido->idStatusPedido;                            
                        ?>
                        <?php
                            if( ( $idStatusPedido == 2 && ( $is_locador && $infoPedido->localRetiradaLocador != 1 ) ) ||
                            ( $idStatusPedido == 2 && ( !$is_locador && $infoPedido->localRetiradaLocatario != 1 ) ) ||
                            ( $idStatusPedido == 4 && ( $is_locador && $infoPedido->localDevolucaoLocador != 1 ) ) ||
                            ( $idStatusPedido == 4 && ( !$is_locador && $infoPedido->localDevolucaoLocatario != 1 ) ) ) {
                        ?>
                        <span class="preset-botao botao js-modal1" id="botao-local-pedido">Confirmar Local de <?php echo ($idStatusPedido == 2)? "Retirada" : "Entrega"; ?></span>
                        <?php
                            }
                        ?>
                        <?php
                            if( $idStatusPedido == 3 && !$is_locador && $infoPedido->solicitacaoRetiradaLocatario != 1 ) {                                                            
                        ?>
                        <span class="preset-botao botao js-modal2" id="botao-solicitar-retirada">Solicitar Retirada</span>
                        <?php                            
                            } elseif( $idStatusPedido == 3 && $is_locador && $infoPedido->solicitacaoRetiradaLocatario == 1 ) {
                        ?>
                        <span class="preset-botao botao js-modal4" id="botao-confirmar-retirada">Confirmar Retirada</span>
                        <?php
                            }
                        ?>
                        <?php
                            if( $idStatusPedido == 5 && !$is_locador && $infoPedido->solicitacaoDevolucaoLocatario != 1 ) {
                        ?>
                        <span class="preset-botao botao js-modal6" id="botao-solicitar-devolucao">Solicitar Devolução</span>
                        <?php } elseif( $is_locador && $idStatusPedido == 5 && $infoPedido->solicitacaoDevolucaoLocatario == 1 ) { ?>
                        <span class="preset-botao botao js-modal8" id="botao-confirmar-devolucao">Confirmar Devolução</span>
                        <?php } ?>                        
                        <?php if( $idStatusPedido >= 7 ) { ?>
                        <span class="preset-botao botao <?php echo ( $idStatusPedido == 7 )? "js-modal12" : ""; ?> <?php echo ( $idStatusPedido == 8 || $idStatusPedido == 9 )? "js-modal13" : ""; ?>" id="botao-visualizar-pendencias">Visualizar Pendências</span>
                        <?php } ?>
                        <?php
                            if( $idStatusPedido == 6 && $is_locador ) {
                        ?>
                        <span class="preset-botao botao js-modal10" id="botao-definir-pendencias">Definir Pendências</span>
                        <?php
                            }
                        ?>
                        <?php if( $idStatusPedido == 9 && $is_locador ) { ?>
                        <span class="preset-botao botao js-modal16" id="botao-cancelar-locacao">Avaliar Locatário</span>
                        <?php } ?>
                        <?php
                            if( $idStatusPedido <= 2 ) {                                                            
                        ?>
                        <span class="preset-botao botao js-modal10" id="botao-cancelar-locacao">Cancelar Locação</span>
                        <?php
                            }
                        ?>
                    </div>
                    <div id="container-historico-pedido">
                        <?php
                            $listaHistoricoAlteracao = new \Tabela\AlteracaoPedido();
                            $listaHistoricoAlteracao = $listaHistoricoAlteracao->getAlteracaoPedido("a.idPedido = {$idPedido} ORDER BY a.dataOcorrencia DESC");
                            
                            foreach( $listaHistoricoAlteracao as $alteracao ) {
                        ?>
                        <div class="box-status">                            
                            <p class="info"><?php echo formatar_data(null, $alteracao->dataOcorrencia); ?> - <?php echo $alteracao->tituloStatus?></p>
                        </div>
                        <?php } ?>                        
                    </div>
                </div>
            </div>
            <!-- CONTEUDO PRINCIPAL -->
        </div>
        <?php require_once("layout/footer.php"); ?>
        <script src="js/libs/jquery-3.1.1.min.js"></script>
        <script src="js/classes/JSMask.js"></script>
        <script src="js/script.js"></script>
    </body>
</html>