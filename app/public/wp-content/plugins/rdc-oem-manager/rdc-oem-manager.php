<?php
/**
 * Plugin Name: RDC Repuestos - Gestión OEM
 * Description: Agrega el campo OEM al catálogo de productos, lo valida y lo expone vía API REST.
 * Version: 1.0.0
 * Author: Equipo de Desarrollo RDC
 * Text Domain: rdc-repuestos
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Seguridad: previene acceso directo
}

// ---------------------------------------------------------
// 1. MEDIR RENDIMIENTO INICIAL
// ---------------------------------------------------------
add_action( 'init', 'rdc_iniciar_medicion_rendimiento' );
function rdc_iniciar_medicion_rendimiento() {
    if ( ! defined( 'RDC_START_TIME' ) ) {
        define( 'RDC_START_TIME', microtime( true ) );
    }
}

add_action( 'shutdown', 'rdc_finalizar_medicion_rendimiento' );
function rdc_finalizar_medicion_rendimiento() {
    if ( defined( 'RDC_START_TIME' ) && WP_DEBUG ) {
        $tiempo_transcurrido = microtime( true ) - RDC_START_TIME;
        // Registra el tiempo en wp-content/debug.log
        error_log( sprintf( "RDC Repuestos [Rendimiento]: Petición procesada en %f segundos.", $tiempo_transcurrido ) );
    }
}

// ---------------------------------------------------------
// 2. AGREGAR EL CAMPO EN LA EDICIÓN DEL PRODUCTO (BACKEND)
// ---------------------------------------------------------
add_action( 'woocommerce_product_options_general_product_data', 'rdc_agregar_campo_oem' );
function rdc_agregar_campo_oem() {
    echo '<div class="options_group">';
    woocommerce_wp_text_input(
        array(
            'id'          => '_numero_parte_oem',
            'label'       => __( 'Número de Parte OEM', 'rdc-repuestos' ),
            'placeholder' => 'Ej: 90915-YZZD2',
            'desc_tip'    => 'true',
            'description' => __( 'Ingrese el número de parte original del fabricante para este repuesto.', 'rdc-repuestos' )
        )
    );
    echo '</div>';
}

// ---------------------------------------------------------
// 3. VALIDAR Y GUARDAR EL CAMPO
// ---------------------------------------------------------
// Reemplaza la lógica de inserción manual ($stmt_app_add) y validación básica
add_action( 'woocommerce_process_product_meta', 'rdc_guardar_campo_oem' );
function rdc_guardar_campo_oem( $post_id ) {
    if ( isset( $_POST['_numero_parte_oem'] ) ) {
        $oem = sanitize_text_field( $_POST['_numero_parte_oem'] );
        update_post_meta( $post_id, '_numero_parte_oem', $oem );
    }
}

// ---------------------------------------------------------
// 4. MOSTRAR EN LA PÁGINA DEL PRODUCTO
// ---------------------------------------------------------
add_action( 'woocommerce_single_product_summary', 'rdc_mostrar_oem_front', 25 );
function rdc_mostrar_oem_front() {
    global $product;
    $oem = $product->get_meta( '_numero_parte_oem' );
    
    if ( ! empty( $oem ) ) {
        echo '<div class="producto-rdc-oem" style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border-left: 4px solid #2563eb; border-radius: 4px;">';
        echo '<strong>' . esc_html__( 'OEM Compatible:', 'rdc-repuestos' ) . '</strong> ' . esc_html( $oem );
        echo '</div>';
    }
}

// ---------------------------------------------------------
// 5. API REST: EXPOSICIÓN (GET) Y ACTUALIZACIÓN (PUT)
// ---------------------------------------------------------
// GET: Incluir el campo OEM en la respuesta de la API de WooCommerce
add_filter( 'woocommerce_rest_prepare_product_object', 'rdc_api_exponer_oem', 10, 3 );
function rdc_api_exponer_oem( $response, $object, $request ) {
    $oem = $object->get_meta( '_numero_parte_oem' );
    $response->data['numero_parte_oem'] = $oem;
    return $response;
}

// PUT/POST: Interceptar la actualización para guardar y validar
add_action( 'woocommerce_rest_insert_product_object', 'rdc_api_actualizar_oem', 10, 3 );
function rdc_api_actualizar_oem( $object, $request, $creating ) {
    if ( isset( $request['numero_parte_oem'] ) ) {
        $oem = sanitize_text_field( $request['numero_parte_oem'] );
        
        // Validación Restricta para la API
        if ( ! empty( $oem ) && strlen( $oem ) < 4 ) {
            // Retorna un HTTP 400 Bad Request si la validación falla
            throw new WC_REST_Exception( 
                'woocommerce_rest_invalid_oem', 
                __( 'El Número de Parte OEM es demasiado corto. Debe tener al menos 4 caracteres.', 'rdc-repuestos' ), 
                400 
            );
        }
        
        $object->update_meta_data( '_numero_parte_oem', $oem );
        $object->save_meta_data();
    }
}