<?php
    sleep(1);
    require_once("../include/initialize.php");
    require_once("../include/classes/sessao.php");
    require_once("../include/classes/tbl_pedido.php");
    require_once("../include/classes/tbl_alteracao_pedido.php");        

    $RETIRADA = 1;
    $DEVOLUCAO = 2;

    $idPedido = ( isset($_POST["idPedido"]) )? (int) $_POST["idPedido"] : null;
    $codigoCartao = ( isset($_POST["codigoSeguranca"]) )? (int) $_POST["codigoSeguranca"] : null;
    $modo = ( isset($_POST["modo"]) && $_POST["modo"] == $RETIRADA )? $RETIRADA : $DEVOLUCAO;
    
    $sessao = new Sessao();
    $idUsuario = (int) $sessao->get("idUsuario");    

    $infoPedido = new \Tabela\Pedido();
    $infoPedido = $infoPedido->buscar("id = {$idPedido}")[0];

    $is_locador = null;
    if( $infoPedido->idUsuarioLocador == $idUsuario ) {
        $is_locador = true;
    } elseif( $infoPedido->idUsuarioLocatario == $idUsuario ) {
        $is_locador = false;
    }
    
    echo "idPedido: " . $idPedido . " modo: " . $modo . " idusuario: " . $idUsuario . " is_locador: " . $is_locador;

    if( $modo == $RETIRADA ) {
        
        if( $is_locador ) {
            $infoPedido->solicitacaoRetiradaLocador = 1;
        } else {
            $infoPedido->solicitacaoRetiradaLocatario = 1;
        }
        
        if( $infoPedido->solicitacaoRetiradaLocador == 1 && $infoPedido->solicitacaoRetiradaLocatario == 1 ) {
            $id_aguardando_confirmacao_local_entrega = 4;
            
            $infoPedido->idStatusPedido = $id_aguardando_confirmacao_local_entrega;
            
            $alteracaoPedido = new \Tabela\AlteracaoPedido();
            $alteracaoPedido->dataOcorrencia = get_data_atual_mysql();
            $alteracaoPedido->idPedido = $idPedido;
            $alteracaoPedido->idStatus = $id_aguardando_confirmacao_local_entrega;
            $alteracaoPedido->inserir();                        
        }
        
    } elseif( $modo == $DEVOLUCAO ) {                
        
        if( $is_locador ) {
            $infoPedido->solicitacaoDevolucaoLocador = 1;
        } else {
            $infoPedido->solicitacaoDevolucaoLocatario = 1;
            $infoPedido->dataEntregaEfetuada = get_data_atual_mysql();
        }                
        
        if( $infoPedido->solicitacaoDevolucaoLocador == 1 && $infoPedido->solicitacaoDevolucaoLocatario == 1 ) {
            $id_aguardando_definicao_pendencias = 6;
            
            $infoPedido->idStatusPedido = $id_aguardando_definicao_pendencias;
            
            $alteracaoPedido = new \Tabela\AlteracaoPedido();
            $alteracaoPedido->dataOcorrencia = get_data_atual_mysql();
            $alteracaoPedido->idPedido = $idPedido;
            $alteracaoPedido->idStatus = $id_aguardando_definicao_pendencias;
            $alteracaoPedido->inserir(); 
        }
        
    }
         
    echo json_encode($infoPedido);
    $infoPedido->atualizar();
?>