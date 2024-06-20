<?php

class WDM_Login_Redirect
{
    public function __construct()
    {
        add_action( 'template_redirect', array($this,'wdm_redirect_to_login_page'));
    }

    public function wdm_redirect_to_login_page()
    {  
        $paths = array(
            '/login-register/',
            '/wp-login.php/',
            '/reset-password/',
            '/customer-support/',
            '/frequently-asked-questions/'
        );
        $current_path = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        if(!is_user_logged_in( ))
        {            
            $login_page = home_url('/login-register/');

            if ( !in_array($current_path,$paths)) {
                wp_redirect($login_page);
                exit();
            }
        }
    }
}
$wdm_login_redirect = new WDM_Login_Redirect();