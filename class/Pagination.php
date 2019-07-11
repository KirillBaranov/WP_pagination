<?php

/**
 * Date: 02.07.2019
 * Author: Kirill Baranov.
 * GitHub: https://github.com/HilkaManilka
 * Last update: 11.07.2019
 * Version: 1.1
 */

$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

/**
 * @var $default - стандартный набор настроек для пагинации.
 */

class Pagination
{
    public function __construct( $post_type = 'post', $args = array(), $keyMetaboxes = array() )
    {
        if ( !empty($args) ) {
            array_replace($this->default, $args);
        }

        if ( !empty($keyMetaboxes) ) {
            $this->metaboxes = $keyMetaboxes;
        }

        if ( !empty($args['offset']) ) {
            $this->offset = $args['offset'];
        }

        $this->post_type = $post_type;
        $this->pages = $this->countPosts();

        if ( $this->pages / $this->numberposts < 1 ) {

            $this->pages = 0;
        }

        else {
            if ( $this->pages / $this->numberposts < 2 )  { // 1.6
                $this->pages = $this->pages / $this->numberposts;
                $this->pages = round($this->pages); // 2
            }

            else {
                $this->pages = round($this->pages);
            }

            for ( $i = 1; $i <= $this->pages; $i++ ) {
                array_push($this->default['paginationButtons'], $i);
            }

        }

        $this->html = $this->generatePagination();
        $this->createPagination();

    }

    /**
     * @var $debug
     * Use debug for wathing results when u use pagination.
     */
    public $debug = array();

    /**
     * @var string
     * This variables is responsible for pagination html;
     */
    public $html;

    /**
     * @var string
     * When u create class be carefull and text correct post_type.
     */
    public $post_type;

    /**
     * @var int
     * How many posts will be showed in the page.
     */
    public $numberposts = 10;

    /**
     * @var array
     * This var used if u want to get especial metaboxes when u doing queryNewData()
     */
    public $metaboxes   = array();

    /**
     * @var mixed
     * How many posts will be offset in the page.
     */
    public $offset;

    /**
     * @var float|int
     * How many pagination buttons will be show in the page.
     */
    public $pages;

    /**
     * This function creating pagination.
     */
    public function createPagination() {
        if ( $this->pages !== 0 ) {
            return $this->html;
        }
    }

    /**
     * @param $offset
     * @return string
     * This function generate pagination using $offset. This params using for updating
     * current page in the default settings.
     */
    public function generatePagination( $offset = 1 ) {

        if ( !empty($offset) ) {
            $this->offset = $offset;

            $this->updatePaginationButtons();
        }

        $buttons     = $this->default['paginationButtons'];
        $buttonsList = '';

        if ($this->default['paginationButtons'][0] - 1 <= 0) {
            $pagePrev = 1;
        }

        else {
            $pagePrev = $this->default['paginationButtons'][1] + 1;
        }


        /**
         * todo: Пофиксить $pagePrev (Изменить на pageNext), добавить алгоритм для высчитывания
         */
        $beforeBtn   = '<li onclick="pagination(this,` '. $this->post_type .' `)"  data-page="' . $pagePrev . '" class="pagination-link">' . $this->default['beforeText'] . '</li>';
        $afterBtn    = '<li onclick="pagination(this,` '. $this->post_type .' `)" data-page=" '. $pagePrev .' " class="pagination-link">' . $this->default['afterText'] . '</li>';

        $prevTag = '<div class="flex justify-center"><ul class="pagination flex">';
        $endTag  = '</ul></div>';

        foreach ( $buttons as $button ) {

            if ( $button == $this->default['currentPage'] ) {
                $paginationBtn = '<li data-page="' . $button . '" onclick="pagination(this,` '. $this->post_type .' `)" class="pagination-link active-pagination">' . $button . '</li>';
            }

            else {
                $paginationBtn = '<li data-page="' . $button . '" onclick="pagination(this,`'. $this->post_type .'`)" class="pagination-link">' . $button . '</li>';
            }

            $buttonsList .= $paginationBtn;
        }

        $html = $prevTag . $beforeBtn . $buttonsList . $afterBtn . $endTag;

        return $html;
    }

    /**
     * @var array
     * This array - default setting pagination
     */
    private $default = array(
        'currentPage'       => 1,
        'beforeText'        => 'Назад',
        'afterText'         => 'Вперед',
        'paginationButtons' => array(),
    );


    /**
     * @return mixed
     * This function return current page
     */
    public function getCurrentPage()
    {
        return $this->default['currentPage'];
    }

    /**
     * This function update pagination buttons.
     * Use her if u want edit current page and generate new pagination.
     */
    public function updatePaginationButtons()
    {
        $i = count($this->default['paginationButtons']);

        $this->default['currentPage'] = $this->offset;

        for ( $j = 0; $j < $i; $j++ ) {
            $this->default['paginationButtons'][$j] = strval(($i[$j]) + $this->default['paginationButtons'][$j]);
        }
    }

    /**
     * @return int
     * This function is responsible for counting posts for correctly generation pagination.
     */
    public function countPosts()
    {
        global $post;
        $new_args = array(
            'post_type'   => $this->post_type,
            'numberposts' => -1
        );
        $posts = get_posts($new_args);
        $i = 0;
        foreach ($posts as $post) {
            setup_postdata($post);

            $i++;

        }
        wp_reset_postdata();

        return $i;
    }


    /**
     * @param array $args
     * @return int[]|WP_Post[]
     * This function - core this class.
     * Use her for getting posts with help args.
     * $args have this params:
     * string - category_name
     * string - orderby
     * string - order
     * string - offset posts. Be carefull with this param.
     */
    public function queryNewData($args = array())
    {
        $this->default['currentPage'] = $args['offset'];

        global $post;
        $arr = array();
        $new_args = array(
            'post_type'         => $this->post_type,
            'numberposts'       => $this->numberposts,
            'category_name'     => $args['category_name'],
            'orderby'           => $args['sort']['orderby'],
            'order'             => $args['sort']['order'],
            'offset'            => $args['offset'],
            'include_children'  => false
        );
        $posts = get_posts($new_args);
        $i = 0;
        foreach ($posts as $post) {
            setup_postdata($post);

            $image_id             = get_post_thumbnail_id();
            $limage_url           = wp_get_attachment_image_src($image_id, 'full');
            $limage_url           = $limage_url[0];

            $arr[$i]['date']      = get_the_date();
            $arr[$i]['link']      = get_permalink();
            $arr[$i]['thumbnail'] = $limage_url;
            $arr[$i]['title']     = get_the_title();

            // Добавить кастомное подтягивание метаданных с постов.
            $arr[$i]['content']   = get_the_content();

            for ( $b = 0; $b < count($this->metaboxes); $b++ ) {
                $arr[$i][$this->metaboxes[$b]] = get_post_meta($post->ID, "$this->metaboxes[$b]", true);

                array_push($this->debug, array(
                    $this->metaboxes[$b] => get_post_meta($post->ID, "$this->metaboxes[$b]", true)
                ));
            }

            $i++;

        }
        wp_reset_postdata();
        //shuffle($arr);
        //Вывод массива
        return $posts;
    }
}