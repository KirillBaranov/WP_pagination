<?php

/**
 * Connect your file with Pagination class.
 */
require_once '../class/Pagination.php';

$data = $_POST;
$post_type = $data['post_type'];

/**
 * Create new class for correctly working pagination.
 */
$pagination = new Pagination($post_type);

if (isset($data)) {

    /**
     * Offset - counting how many posts will be offset after query.
     */
    $offset = $data['offset'] * 10 - 10;

    if ($offset < 0 ) {
        $offset = 0;
    }

    /**
     * This args will be used in the query posts.
     */
    $args = array(
        'numberposts' => '10',
        'offset'      => $offset
    );

    /**
     * Updating pagination after ajax.
     */
    $pagination->updatePaginationButtons();

    /**
     * This response will be return for client.
     * 1 - pagination list.
     * 2 - Query
     */
    $response = array(
        $pagination->generatePagination(array( 'offset' => $data['offset'] )), // Не обязательно. Можете генерировать пагинацию на стороне клиента.
        $pagination->queryNewData($args),
    );

    /**
     * Send json data;
     */
    echo json_encode($response);

}


