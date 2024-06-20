<?php

class WDM_Gform_Submission
{
    public function __construct()
    {
        add_action( 'init', array($this,'add_validation_filters') );
        add_action( 'gform_after_submission', array($this,'wdm_custom_submission') ,10,2);
        add_filter( 'gform_validation_message', array($this,'wdm_custom_validation_message'), 10, 2 );

        add_action( 'wp_enqueue_scripts',array($this,'enqueue_scripts_and_styles') );







        
    }


    public function enqueue_scripts_and_styles()
    {
        wp_enqueue_style( 'wdm_login_styles', WDM_ELECTSURE_LEARNSDASH_CUSTOMIZATION_PLUGIN_URL .'public/assets/css/wdm-login.css' );
    }

    public function add_validation_filters()
    {
        $option = get_option( 'wdm_gform_settings');
        add_filter( 'gform_validation_'.$option['login_form'], array($this,'login_custom_validation'),10,1);
        
        add_filter( 'gform_validation_'.$option['registration_form'], array($this,'registration_custom_validation'),10,1);
        
        add_filter( 'gform_validation_'.$option['reset_pass_form'], array($this,'reset_pass_custom_validation'),10,1);
        
        add_filter( 'gform_validation_'.$option['edit_profile_form'], array($this,'edit_profile_custom_validation'),10,1);

        add_filter( 'gform_pre_render_'.$option['edit_profile_form'], array($this,'wdm_populate_user_details'),10,1 );
    }

    public function wdm_populate_user_details($form)
    {     
        if(is_user_logged_in(  ))
        {
            $current_user = wp_get_current_user(  );
            $current_user_id = $current_user->data->ID;
            $current_user_email = $current_user->data->user_email;
            $current_user_first_name = $current_user->data->display_name;
            foreach($form['fields'] as &$field)
                {
                    if ($field->type == 'name' && $field->id == 3) {
                        foreach ($field->inputs as &$input) {
                            if ($input['id'] == 3.3) { // First Name
                                $input['defaultValue'] = $current_user->user_firstname;
                            }
                            if ($input['id'] == 3.6) { // Last Name
                                $input['defaultValue'] = $current_user->user_lastname;
                            }
                        }
                    }
                    // Check for Email field
                    if ($field->type == 'email' && $field->id == 4) {
                        $field->defaultValue = $current_user->user_email;
                    }
                }
                return $form;
        }
    }

    public function registration_custom_validation($validation_result)
    {
        $form = $validation_result['form'];
        foreach($form['fields'] as &$field)
        {
            $field->failed_validation = 0;
        }
        return $validation_result;
    }

    public function login_custom_validation($validation_result)
    {
        error_log("login val");
        $email = '';
        $password = '';

        $form = $validation_result['form'];
        foreach($form['fields'] as &$field)
        {
            if($field['type']==='email')
            {
                $email = rgpost('input_'.$field['id']);
            }
            if($field['type']==='password')
            {
                $password = rgpost('input_'.$field['id']);
            }
            $field->failed_validation = 0;
        }
            
        if($email && filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $user = get_user_by( 'email', $email );
            if(!$user)
            {
                $validation_result['is_valid']= 0;
                $validation_result['form']['fields'][0]['failed_validation'] = 0;
                $validation_result['form']['fields'][0]['validation_message'] = __("This email address is not registered. Please register to continue.",'wdm_electsure_customization');
                return $validation_result;
            }
            else
            {
                if( $password && !wp_check_password( $password, $user->data->user_pass ))
                {
                    $validation_result['is_valid']= 0;
                    $validation_result['form']['fields'][1]['failed_validation'] = 0;
                    $validation_result['form']['fields'][1]['validation_message'] = __("The entered password is incorrect.",'wdm_electsure_customization');
                    return $validation_result;
                }
            }
        }

        return $validation_result;

    } 


    public function reset_pass_custom_validation($validation_result)
    {
        $email = '';
        $form = $validation_result['form'];
        
        foreach($form['fields'] as &$field)
        {
            if($field['type']==='email')
            {
                $email = rgpost('input_'.$field['id']);
            }
            $field->failed_validation = 0;
        }
        
        $user = get_user_by( 'email', $email );
        if($email && filter_var($email,FILTER_VALIDATE_EMAIL))
        {
            $user = get_user_by( 'email', $email );
            if(!$user)
            {
                $validation_result['is_valid']= 0;
                $validation_result['form']['fields'][0]['failed_validation'] = 0;
                $validation_result['form']['fields'][0]['validation_message'] = __("This email address is not registered. Please register to continue.",'wdm_electsure_customization');
                $flag = true;
                return $validation_result;
            }
        }        
        return $validation_result;
    }

    public function edit_profile_custom_validation($validation_result)
    {
        $email = '';
        $form = $validation_result['form'];
        foreach($form['fields'] as $field)
        {
            if($field['type']==='email')
            {
                $email = rgpost('input_'.$field['id']);
            }
        }
        if(empty($email))
        {
            $validation_result['is_valid']= 0;
            $validation_result['form']['fields'][1]['failed_validation'] = 0;
            $validation_result['form']['fields'][1]['validation_message'] = __("This field is required.",'wdm_electsure_customization');
            return $validation_result;
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $validation_result['is_valid']= 0;
            $validation_result['form']['fields'][1]['failed_validation'] = 0;
            $validation_result['form']['fields'][1]['validation_message'] = __("The email address entered is invalid, please check the formatting (e.g. email@domain.com).",'wdm_electsure_customization');
            return $validation_result;
        }
        return $validation_result;
    }



    /**
     * This function create custom validation message to display
     * 
     * @param string $message the default validation message
     * @param object $form the submitted form object
     * 
     * @return string $final_message
     */
    public function wdm_custom_validation_message($message,$form)
    {
        $final_message = "";
        foreach($form['fields'] as &$field)
        {
            $message = $field->validation_message;
            $type =  ucfirst($field->type);
            
            if($message)
            {
                $final_message .= "<h2 class='gform_submission_error' style='margin-bottom:0px'><span class='gform-icon gform-icon--close'></span>$type : $message</h2>";
            }
        }
        return $final_message;
    }

    public function wdm_custom_submission($entry,$form)
    {
        $option = get_option( 'wdm_gform_settings');
       // error_log(print_r($entry,true));
        switch($form['id'])
        {
            case $option['login_form']:   
                $this->login($entry,$form);
            break;
            
            case $option['registration_form']:
                //$this->regsiter($entry,$form);

            break;
                
            case $option['reset_pass_form']:            
                $this->reset_password_email($entry,$form);

            break;            
                    
            case $option['edit_profile_form']:
                $this->update_profile($entry,$form);

            break;
        }
    }

    public function login($entry,$form)
    {
       
        $email = '';
        $pass = '';
        $remember_me ='';

        foreach($form['fields'] as $field){
            $field_value = rgar($entry,$field['id']);
            if($field['type']==='email')
            {
                $email = $field_value;
            }
            if($field['type']==='password')
            {
                $pass = $field_value;
            }
            if($field['type']==='checkbox')
            {
                foreach($field['choices'] as $choice)
                {
                    if($choice['isSelected'] === 1)
                    {
                        $remember_me = $choice['value'];
                    }
                }
            }
        }

        $creds = array(
            'user_login' => $email,
            'user_password' => $pass,
            'remember' => $remember_me ? true : false
        );
           
        $user = wp_signon($creds, false);
       // error_log(print_r($user,true));
        wp_set_current_user($user->ID);
        wp_redirect( home_url());
    }

    public function reset_password_email($entry, $form)
    {

        $email = '';
        foreach($form['fields'] as $field){
            $field_value = rgar($entry,$field['id']);
            if($field['type']==='email')
            {
                $email = $field_value;
            }
        }
        //$email = rgar($entry, '1'); 

        $user_data = get_user_by( 'email',$email );
        $user_login = $user_data->user_login;
        $key = get_password_reset_key($user_data);

        $reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

        $message = "Someone requested a password reset for the following account:\n\n";
        $message .= site_url() . "\n\n";
        $message .= "Username: $user_login\n\n";
        $message .= "If this was a mistake, just ignore this email and nothing will happen.\n\n";
        $message .= "To reset your password, visit the following address:\n\n";
        $message .= "<a href='$reset_link'>$reset_link</a>\n";

        wp_mail($user_email, 'Password Reset Request', $message);   
    }


    public function update_profile($entry,$form)
    {
        if(is_user_logged_in(  ))
        {
            $current_user = wp_get_current_user();
            $first_name='';
            $middle_name = '';
            $email='';

            foreach($form['fields'] as $field)
            {
                $field_value = rgar($entry,$field['id']);
                if($field['type']==='email')
                {
                    $email = $field_value;
                    error_log($email);
                }
                if($field['type']==='name')
                {
                    $first_name = rgar($entry,$field['inputs'][1]['id']);
                    error_log($first_name);
                    $last_name = rgar($entry,$field['inputs'][3]['id']);
                    error_log($last_name);
                }
            }
            
            // Update user data
            wp_update_user([
                'ID' => $current_user->ID,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'user_email' => $email,
            ]);
        }
    }
}

$wdm_gform_submission = new WDM_Gform_Submission();