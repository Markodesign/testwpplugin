<?php
/**
 * @since             1.0
 * @package           My Test Wp Plugin
 *
 * @wordpress-plugin
 * Plugin Name: My Test Wp Plugin
 * Description: Тестовый плагин для Wordpress
 * Author URI:  https://www.linkedin.com/in/leonid-maharyta/
 * Author:      Leonyid Maharyta
 * Version:     1.0
 *
 * Text Domain: my-test-wp-plugin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

add_action("admin_menu", "addMenu");
function addMenu() {
    add_menu_page("Test wp plugin menu", "Test wp plugin menu", 4, "test-wp-menu","testWpPlugin");
}

function testWpPlugin() {
    print "<div style='padding-top: 100px;'>For this form please use this code - <strong>do_shortcode('[my-test-wp-form]');</strong></div>";

}

add_shortcode( "my-test-wp-form" , "createTestForm" );

function createTestForm() {
    $succ_add = 0;
    if($_POST['submit_my_test_form']){


        $dataArr = array();
        $dataArr['title'] = htmlspecialchars(trim($_POST['my_test_form_title']));
        $dataArr['text']  = htmlspecialchars(trim($_POST['my_test_form_text']));

        $resCheck = checkMyTestFormElements($dataArr);

        if(empty($resCheck['errors'])) {

            //Create my test post
            $my_post = array(
                'post_title'   => wp_strip_all_tags($dataArr['title']),
                'post_content' => $dataArr['text'],
                'post_status'  => 'draft',
                'post_author'  => 1
            );

            // Insert the post
            if(wp_insert_post($my_post)) {
                $succ_add = 1;
            }
        }
    }

    if($succ_add==1){
        print "<div style='color:green'>Post was successfully created!</div>";
    }else {
        $my_form_template = '<h3>My Test Form</h3>
        <form action="" method="post">
            <input type="hidden" name="submit_my_test_form" value="1"/>
            <div>
                <label>Title</label>
                <input type="text" name="my_test_form_title" value="' . (isset($dataArr['title']) ? $dataArr['title'] : '') . '"/>
            </div>
            <div>
                <label>Text</label>
                <textarea style="width: 400px;height: 200px;" name="my_test_form_text">' . (isset($dataArr['text']) ? $dataArr['text'] : '') . '</textarea>
            </div>
            <div>
                <input type="submit" value="Send form">
            </div>
        </form>';

        //Check errors in form
        if (!empty($resCheck['errors'])) {
            foreach ($resCheck['errors'] as $valueError) {
                print "<div style='color:red'>" . $valueError . "</div>";
            }
        }

        print $my_form_template;
    }

}

function checkMyTestFormElements($dataArr) {
    $error = array();

    if(empty($dataArr['title'])){
        $error['empty_title'] = 'Please enter title';
    }
    if(empty($dataArr['text'])){
        $error['empty_text']  = 'Please enter text';
    }

    //Check post title in DB
    $page = get_page_by_title($dataArr['title'], OBJECT, 'post');
    if(isset($page->ID) and $page->ID>0) {
        $error['title_isset'] = "Post with this title already added";
    }

    return array('errors' => $error);
}