<?php
/**
 * Created by PhpStorm.
 * User: fazlur
 * Date: 6/11/18
 * Time: 1:39 PM
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}



class AdminSettings{

    public function __construct()
    {
        add_action('admin_menu',array($this,'neurogym_customize_setting_menu'));
        add_action( 'admin_init',array( $this,'neurogym_customize_key_settings' ));
    }

    public function neurogym_customize_key_settings() {

       add_option( 'neurogym_customization_menu_title', '');
       register_setting( 'neurogym_customization_key_form_group', 'neurogym_customization_menu_title', '' );


    }


    public function neurogym_customize_setting_menu()
    {
        add_menu_page('NeuroGym Customization ', 'NeuroGym Customization', 'manage_options', 'neurogym_customization_setup', array($this,'neurogym_customization_key_settings'),'','40');
        add_submenu_page( 'neurogym_customization_setup', 'Neurogym Customization Settings', 'Neurogym Customization Settings', 'manage_options', 'neurogym_customization_setup',array($this,'neurogym_customization_key_settings'));


    }



    public  function neurogym_customization_key_settings(){
        ?>
        <div style=" background-color:#fff; margin-right:14px; margin-top:14px; padding-left:14px; border-left:  4px solid #00a0d2; border-top: 1px solid #00a0d2;border-right: 1px solid #00a0d2;border-bottom: 1px solid #00a0d2;">

            <form method="post" action="options.php">
                <?php settings_fields( 'neurogym_customization_key_form_group' ); ?>
                <div>

                    <div >
                        <h3>NeuroGym Customization Settings :</h3>

                    </div>
                </div>


                <table style="margin-left: 8%;">
                    <tr>
                        <th scope="row" style="width: auto%; text-align: left; "><label for="neurogym_customization_menu_title">Custom menu of Groups  : </label></th>
                    </tr>
                    <tr>
                        <th scope="row" style="width: auto%; text-align: left; "><label for="neurogym_customization_menu_title">Video title name : </label></th>
                        <td style="text-align: left; ">
                            <input style="height:35px; margin-left: 60px;" type="text" size="80"  id="neurogym_customization_menu_title" name="neurogym_customization_menu_title" placeholder="Enter Video Title Name" value="<?php echo esc_html( sprintf( __('%s', 'textdomain' ), get_option( 'neurogym_customization_menu_title' ) ) ); ?>" />
                        </td>
                    </tr>


                </table>

                <?php  submit_button(); ?>
            </form>
        </div>
        <?php
    }



}

new AdminSettings();