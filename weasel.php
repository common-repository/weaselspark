<?php
/*
Plugin Name: Weasel plugin
Plugin URI: http://www.weaselspark.com
Description : Plugin de WeaselSpark
Version: 1.0
Author: Elosi
Author URI: http://www.elosi.com
License: GPL2
*/

add_action( 'plugins_loaded', 'weasel_init' );

register_activation_hook( __FILE__,  'weasel_plugin_activation'  );
register_deactivation_hook( __FILE__,  'weasel_plugin_uninstall'  );

register_uninstall_hook(__FILE__, 'weasel_plugin_uninstall');



function weasel_init(){
//die(var_dump());

    if (weasel_traitement_des_donnees()) {
        if(isset($_POST['idWeasel'])){
            saveWeaselData();
        }
    } 
    add_action( 'wp_head', 'weasel_add_to_header' );
    add_action('admin_menu',  'weasel_AdminSetupMenu' );
 
 }

 function weasel_traitement_des_donnees() {

    if(isset($_POST['_wpnonce'])) {
        if(wp_verify_nonce($_POST['_wpnonce'], 'securite_nonce')) {
            // Le formulaire est validé et sécurisé, suite du traitement
            return true;
        } else {
            // le formulaire est refusé
            wp_create_nonce('securite_nonce');
            return false;
        }
    }
}

add_action('weasel_init',  'saveWeaselData' );



function weasel_add_to_header(){
    global $wpdb;
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}weasel");

    if(!empty($row) && isset( $row->id_site)){
     
        
        $idWeasel = $row->id_site;
        $fullScript = $row->fullscript;
        $responsive = $row->responsive;
        $visibility = $row->visibility;
        
        $url =  "https://tracker.weaselspark.com";
        $option = "window.wslParams = window.wslParams||{}; window.wslParams.id='$idWeasel';";
        
        if($fullScript == "notfullScript" || $fullScript == "responsive"){
            $url .= '/weasel-light.js';
        } else {
            $url .= '/weasel-full.js';
            $option .= "window.wslParams.icon = '$visibility'; window.wslParams.resp = '$responsive';";
        
        }
        $scripts .= '<script type="text/javascript">'.$option.'</script>';
        $scripts .= '<script type="text/javascript" src='.$url.'></script>';
       
        echo $scripts;
     
    }
}
       
function weasel_AdminSetupMenu(){
   add_menu_page( 'Weasel', 'Weasel', 'manage_options', 'weasel-plugin',   'displayWeaselMenu'  );
}



function displayWeaselMenu(){
    global $wpdb;
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}weasel");
    

    $idWeasel = !empty($row->id_site)?$row->id_site:'';
    $bugReport = !empty($row->fullscript)?$row->fullscript:'';
    $responsive = !empty($row->responsive)?$row->responsive:'';
    $visibility = !empty($row->visibility)?$row->visibility:'';

?>

<?php

function weasel_scripts() {
    wp_enqueue_style( 'style', plugin_dir_url(__FILE__) .'style.css' );
    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'weasel', plugin_dir_url(__FILE__) . 'weasel.js');
}
add_action( 'wp_enqueue_scripts', 'weasel_scripts' );


?>
    <div class="wrap">

    <h1 class="title">Weasel Spark</h1>
    
    <div class="content">
      <fieldset>
          <legend>
          Définissez le comportement de la pop-in Weasel Spark
          </legend>
           <form action="" method="POST">
               <table>
                    <tr>
                        <td> <label>Id du site</label></td>
                        <td>  <input type="text" name="idWeasel" id="idWeasel" value=<?php echo esc_attr($idWeasel); ?>></td>
                    </tr>
                    <tr>
                        <td><label>Activer la fonctionnalité de report de bug :</label></td>
                        <td> 
                            <input type="radio" name="bugReport" value=<?php echo esc_attr("fullScript");?> <?php echo  $bugReport=="fullScript"?"checked":'' ?> />Oui
                            <input type="radio" name="bugReport" value=<?php echo esc_attr("notfullScript");?> <?php echo $bugReport=="notfullScript"?"checked":'' ?> />Non
                        </td>
                    </tr>
                    <tr>
                        <td><label>Utilisation de l'application sur mobiles et tablettes :</label></td>
                        <td>   
                            <input type="radio" name="reportBugMob" value=<?php echo esc_attr("responsive");?> id="reportBugMobO" <?php echo $responsive=="responsive"?"checked":'' ?> />Oui
                            <input type="radio" name="reportBugMob" value=<?php echo esc_attr("notResponsive");?> id="reportBugMobN" <?php echo $responsive=="notResponsive"?"checked":'' ?> />Non
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><label>Conditions d'affichage de l'icône Weasel Spark:</label></td>
                    </tr>
                    <tr>
                        <td colspan="2"> 
                             <input type="radio" name="displayWeaselIco" value=<?php echo esc_attr("alwaysShown");?> id ="alwaysShown" <?php echo $visibility=="alwaysShown"?"checked":'' ?> />Toujours afficher Weasel Spark <br/>
                             <input type="radio" name="displayWeaselIco" value=<?php echo esc_attr("toggleVisibility");?>  id ="toggleVisibility" <?php echo $visibility=="toggleVisibility"?"checked":'' ?> />cacher Weasel Spark lors d'un clique <br/>
                             <input type="radio" name="displayWeaselIco" value=<?php echo esc_attr("alwaysHidden");?>  id ="alwaysHidden" <?php echo $visibility=="alwaysHidden"?"checked":'' ?> />Toujours cacher Weasel Spark                       
 </td>
                    </tr>
                  
               </table>
                 <?php 
                    echo wp_nonce_field('securite_nonce');
                    submit_button();
                 ?>
               </form>
          </fieldset>
      </div>
      
    </div>
    <?php 
} 

add_action('weasel_init', 'weasel_traitement_des_donnees');

  function weasel_plugin_activation()
{
   global $wpdb;
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}weasel (id_site varchar(20),fullscript varchar(20),responsive varchar(20),visibility varchar(20) ,  UNIQUE KEY `id_site` (`id_site`));");
}


function weasel_plugin_uninstall()
{
    global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}weasel ");

}

function saveWeaselData(){
     global $wpdb;

      if (weasel_traitement_des_donnees()) { 
     $delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}weasel");
     $delete = sanitize_text_field($delete); 
     $idWeasel = sanitize_text_field($_POST['idWeasel']);  
     
     if (isset($_POST['bugReport'])) {
        $bugReport = sanitize_text_field($_POST['bugReport']); 
     }
     else {
        $bugReport = "fullscript";
     } 

     if (isset($_POST['reportBugMob'])) {
        $responsive = sanitize_text_field($_POST['reportBugMob']); 
     }
     else {
        $responsive = "responsive";
     } 

     if (isset($_POST['displayWeaselIco'])) {
        $visibility = sanitize_text_field($_POST['displayWeaselIco']); 
     }
     else {
        $visibility = "alwaysShown";
     } 


     $wpdb->insert("{$wpdb->prefix}weasel", array('id_site' => $idWeasel,'fullscript'=> $bugReport,'responsive'=> $responsive,'visibility'=> $visibility),
        array( 
        '%s', 
        '%s',
        '%s',
        '%s' 
    ) );          
                                    }
}

add_action('saveWeaselData', 'weasel_traitement_des_donnees');
