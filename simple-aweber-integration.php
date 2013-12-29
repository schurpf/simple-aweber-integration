<?php

/*/

Plugin Name: Simple Aweber Integration

Plugin URI: http://www.schurpf.com/simple-aweber-integration/

Description: Add a horizontal Aweber form to your posts or pages given the JavaScript code of your traditional vertical Aweber form. Determine whether the horizontal form is shown at the beginning or end and assign a priority to it.

Version: 101

Author: Michael Schurpf

Author URI: http://schurpf.com

/*/



$simpleaweber_style_options = array('all','nheader','nfooter','nhnf','original');


wp_register_style('simple-aweber-integration', plugins_url('/style.css',__FILE__));

wp_enqueue_style( 'simple-aweber-integration');


// Add settings link on plugin page

function your_plugin_settings_link($links) { 

  $settings_link = '<a href="options-general.php?page=simple-aweber-integration.php">Settings</a>'; 

  array_unshift($links, $settings_link); 

  return $links; 

}

 

$plugin = plugin_basename(__FILE__); 

add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );



function basic_content_replace ($text){

    global $post, $page, $simpleaweber_style_options;

    //load css

    $simpleaweber_style_option = get_option('aweber_style');
    

    switch ($simpleaweber_style_option){

        case $simpleaweber_style_options[0]:

            $aweber_div_id = 'simple-aweber-integration';

        break;

    

        case $simpleaweber_style_options[1]:

            $aweber_div_id = 'simple-aweber-integration-nheader';

        break;

    

        case $simpleaweber_style_options[2]:

            $aweber_div_id = 'simple-aweber-integration-nfooter';

        break;

    

        case $simpleaweber_style_options[3]:

            $aweber_div_id = 'simple-aweber-integration-nh-nf';

        break;

      

        case $simpleaweber_style_options[4]:

            $aweber_div_id = '';

        break;      

    

        default:

            $aweber_div_id = 'simple-aweber-integration-nfooter';

    }

    

$AweberHorizontalJS = html_entity_decode(get_option('aweber_js'));

if ($AweberHorizontalJS and ((is_single() and get_option( 'aweber_post',0)) or (is_page() and get_option( 'aweber_page',0)))) {

    $text .= '<div id="'.$aweber_div_id.'">'.$AweberHorizontalJS.'</div><!--simple-aweber-integration-->';

}



if ($AweberHorizontalJS and ((is_single() and get_option( 'aweber_post_start',0)) or (is_page() and get_option( 'aweber_page_start',0)))) {

    $text = '<div id="'.$aweber_div_id.'">'.$AweberHorizontalJS.'</div><!--simple-aweber-integration-->'.$text;

}

return $text;

}

add_filter('the_content','basic_content_replace', get_option('aweber_priority', 10));



add_action('admin_menu', 'my_first_admin_menu');

function my_first_admin_menu() {

add_options_page('Plugin Admin Options', 'Simple Aweber Integration', 'manage_options','simple-aweber-integration', 'simpleaweber_settings_page');

}



function simpleaweber_settings_page() {

    global $simpleaweber_style_options;

    //must check that the user has the required capability 

    if (!current_user_can('manage_options'))

    {

      wp_die( __('You do not have sufficient permissions to access this page.') );

    }

    // variables for the field and option names 

    $hidden_field_name = 'mt_submit_hidden';


    $js_input = 'aweber_js';

    $priority_input = 'aweber_priority';

    $post_input = 'aweber_post';

    $page_input = 'aweber_page';

    $post_start_input = 'aweber_post_start';

    $page_start_input = 'aweber_page_start';
 
    $style_input = 'aweber_style';    


    // Read in existing option value from database

    $js_val = get_option( $js_input );

    $priority_val = get_option( $priority_input);

    $post_val = get_option( $post_input,1);

    $page_val = get_option( $page_input,0);

    $post_start_val = get_option( $post_start_input,0);

    $page_start_val = get_option( $page_start_input,0);


    $style_val = get_option( $style_input,$simpleaweber_style_options[2]);


    // See if the user has posted us some information

    // If they did, this hidden field will be set to 'Y'

    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {

        // Read their posted value

        $js_val = htmlentities(stripslashes($_POST[ $js_input ]));

        $priority_val = $_POST[ $priority_input ];

        $post_val = $_POST[ $post_input ];

        $page_val = $_POST[ $page_input ];

        $post_start_val = $_POST[ $post_start_input ];

        $page_start_val = $_POST[ $page_start_input ];     

        $style_val = $_POST[ $style_input ];   

        // Save the posted value in the database

        update_option( $js_input, $js_val );

        update_option( $priority_input, $priority_val );

        update_option( $post_input, $post_val );

        update_option( $page_input, $page_val );

        update_option( $post_start_input, $post_start_val );

        update_option( $page_start_input, $page_start_val );

        update_option( $style_input, $style_val );       

        unset($_POST[ $hidden_field_name ])

        // Put an settings updated message on the screen

?>

<div class="updated"><p><strong><?php _e('settings saved.', 'simple-aweber-integration-menu' ); ?></strong></p></div>

<?php



    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Simple Aweber Integration Settings', 'simple-aweber-integration-menu' ) . "</h2>";

    // settings form    

    ?>

<form name="form1" method="post" action="">

<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Aweber Javascript Code:", 'simple-aweber-integration-menu' ); ?> 

<input type="text" name="<?php echo $js_input; ?>" value="<?php echo $js_val; ?>" size="120">

</p>

<p>

<?php _e("Show at", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php if ($post_val==1){?>

<input type="checkbox" name="<?php echo $post_input; ?>" value="1" checked size="10">

<?php }else{?>

<input type="checkbox" name="<?php echo $post_input; ?>" value="1" size="10">

<?php }?>

<?php _e(" End of post (Default)", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php if ($page_val==1){?>

<input type="checkbox" name="<?php echo $page_input; ?>" value="1" checked size="10">

<?php }else{?>

<input type="checkbox" name="<?php echo $page_input; ?>" value="1" size="10">

<?php }?>

<?php _e(" End of page", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php if ($post_start_val==1){?>

<input type="checkbox" name="<?php echo $post_start_input; ?>" value="1" checked size="10">

<?php }else{?>

<input type="checkbox" name="<?php echo $post_start_input; ?>" value="1" size="10">

<?php }?>

<?php _e(" Beginning of post", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php if ($page_start_val==1){?>

<input type="checkbox" name="<?php echo $page_start_input; ?>" value="1" checked size="10">

<?php }else{?>

<input type="checkbox" name="<?php echo $page_start_input; ?>" value="1" size="10">

<?php }?>

<?php _e(" Beginning of page", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php _e("Form display", 'simple-aweber-integration-menu' ); ?>

</p>

<p>

<?php switch ($style_val){

    case $simpleaweber_style_options[0]:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>" checked> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>"> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>"> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>"> Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" > Original <br> 

    <?php break;

    case $simpleaweber_style_options[1]:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>"> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>" checked> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>"> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>"> Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" > Original <br> 

    <?php break;

    case $simpleaweber_style_options[2]:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>"> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>"> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>" checked> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>"> Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" > Original <br> 

    <?php break;

    case $simpleaweber_style_options[3]:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>"> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>"> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>"> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>" checked> Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" > Original <br> 

    <?php break; 

    case $simpleaweber_style_options[4]:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>"> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>"> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>"> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>" > Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" checked> Original <br>  

    <?php break;    

    default:?>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[0];?>"> Horizontal, all<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[1];?>"> Horizontal, no header<br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[2];?>"> Horizontal, no footer (Default) <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[3];?>"> Horizontal, no header and no footer <br>

        <input type="radio" name="<?php echo $style_input; ?>" value="<?php echo $simpleaweber_style_options[4];?>" > Original <br> 

        <?php   

}?>

</p>    

<p><?php _e("Priority:", 'simple-aweber-integration-menu' ); ?> 

<input type="text" name="<?php echo $priority_input; ?>" value="<?php echo $priority_val; ?>" size="4"> The lower the number the higher the priority.

</p>

<p class="submit">

<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />

</p>

</form>

<hr />

<h2>Documentation</h2>

<p>If you do not have an account at Aweber, please create one <a href="http://www.schurpf.com/go/aweber/" target="_blank">here</a> (affiliate link).

<h3>Attaching form to posts and pages</h3>

<ol>

  <li>Find your JavaScript code at Aweber. Pick the list you want it for. The JavaScript code is under Web Forms -> YOUR_FORM_NAME -> Publish -> I will install my form -> JavaScript snippet. It will look like this: <br /><?php echo htmlentities(stripslashes('<script type="text/javascript" src="http://forms.aweber.com/form/45/230379345.js"></script>'));?></li>

  <li>Pick where to show it. Default option is at end of post.</li>

  <li>Pick your form display option. Default is removing footer and only displaying header and entry form horizontally.</li>

  <li>Set priority if necessary. Should you have several plugins attaching content to post or page content, you can stear which content gets output first.</li>

</ol>

<h3>Using individual shortcodes in posts and pages</h3>

<p>You have the option of posting your form with the shortcode: <?php echo htmlentities(stripslashes('[simpleaweber js="YOUR_AWEBER_JAVASCRIPT" style="YOUR_STYLE"]'));?>. Both settings are optional.</p>

<p>There are two options:</p>

<ol>

  <li>Setting the form JavaScript individually with option <cite>js="YOUR_AWEBER_JAVASCRIPT"</cite>.</li>

  <li>You can change the form display using one of the following stylings:

  <ul>

    <li></li>

    <li><cite>style="all"</cite> : Horizontal, all</li>

    <li><cite>style="nheader"</cite> : Horizontal, no header</li>

    <li><cite>style="nfooter"</cite> : Horizontal, no footer (Default) </li>

    <li><cite>style="nhnf"</cite> : Horizontal, no header and no footer </li>

    <li><cite>style="original"</cite> : Original form styling</li>

  </ul>

  </li>

</ol>

<p>If you dont provide these options, the settings from the plugin settings page are taken.</p>

<p>Example for form with horizontal styling with no header or footer with individual JavaScript code:</p>

<p>[simpleaweber js="<?php echo htmlentities(stripslashes('<script type="text/javascript" src="http://forms.aweber.com/form/45/230379345.js"></script>'));?>" style="nhnf"]</p>

<p>Please note the use of double quote symbols used.</p>

<h3>Using individual shortcodes in your template</h3>

<p>Should you want to add your Aweber form straight to your template, use the shortcode as described above, combined with function <a href="http://codex.wordpress.org/Function_Reference/do_shortcode" target="_blank">do_shortcode</a>.</p>

<p>Example for form with horizontal styling with no header or footer with individual JavaScript code that you can insert into your template:</p>

<p><?php echo htmlentities(stripslashes('<?php'));?> echo do_shortcode('[simpleaweber js="<?php echo htmlentities(stripslashes('<script type="text/javascript" src="http://forms.aweber.com/form/45/230379345.js"></script>'));?>" style="nhnf"]'); <?php echo htmlentities(stripslashes('?>'));?><p>

<p>Please note the use of double quote symbols versus single quote symbols used.</p>

</div>

<?php

}

function simple_aweber_shortcode( $atts ) {

  global $simpleaweber_style_options;

	extract( shortcode_atts( array(

		'js' => get_option('aweber_js'),

		'style' => get_option('aweber_style'),

	), $atts ) );

    switch ($style){

        case $simpleaweber_style_options[0]:

            $aweber_div_id = 'simple-aweber-integration';

        break;

        case $simpleaweber_style_options[1]:

            $aweber_div_id = 'simple-aweber-integration-nheader';

        break;

        case $simpleaweber_style_options[2]:

            $aweber_div_id = 'simple-aweber-integration-nfooter';

        break;

        case $simpleaweber_style_options[3]:

            $aweber_div_id = 'simple-aweber-integration-nh-nf';

        break;

        case $simpleaweber_style_options[4]:

            $aweber_div_id = '';

        break;

        default:

            $aweber_div_id = 'simple-aweber-integration-nfooter';

    }        


	return '<div id="'.$aweber_div_id.'">'.html_entity_decode($js).'</div><!--simple-aweber-integration-->';

}

add_shortcode( 'simpleaweber', 'simple_aweber_shortcode' );

?>

