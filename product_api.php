<?php

require_once( 'wp-load.php' );


if($_SERVER["REQUEST_METHOD"]=="POST"){

    if(isset($_POST["product_id"])){

        $product_id = $_POST["product_id"];
        $temp = 0;
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
        );

        $related = new WP_Query( $args );
        if( $related->have_posts() ) :
            while( $related->have_posts() ): $related->the_post();
                
                if(get_the_ID() == $product_id){
                    $temp = 1;
                    $pid = get_the_ID();    
                    $name = get_the_title();

                    $main_array = array(
                        "ID" => $pid,
                        "Name" => $name,
                    );
                    $meta = get_post_meta($pid);

                    $flag = array();
                    foreach($meta as $key=>$values){
                        $flag[$key] = $values[0];
                    }
                    
                    $main_array += [ "Meta" => $flag ];
            
                    
                    $args = array(
                        'public'   => true,
                        '_builtin' => false
                    ); 
                    $output = 'names'; // or objects
                    $operator = 'and'; // 'and' or 'or'
                    $taxonomies = get_taxonomies( $args, $output, $operator ); 
                    

                    $flag = array();
                    $taxonomies_flag = array();
                    foreach ($taxonomies as $key=>$values) {
                        $terms = get_the_terms( $pid , $key );
                        foreach ($terms ?: [] as $value1) {
                            $flag[$key][$value1->name] = $value1;
                        }
                    }
                    $main_array += [ "taxonomies" => $flag ];

                    // echo "<pre>";
                    // print_r($main_array);
                    // echo "</pre>";

                    // echo "<pre>";
                    // print_r($terms);
                    // echo "</pre>";

                    $json = json_encode($main_array);
                    echo $json;
                    // echo "<pre>";
                    // print_r(json_decode($json));
                    // echo "</pre>";

                }

            endwhile;
        endif;
        wp_reset_postdata();
    }

    if($temp == 0){
        echo "we can't find your product";
    }

}
