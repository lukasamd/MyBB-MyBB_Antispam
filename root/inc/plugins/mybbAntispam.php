<?php
/**
 * This file is part of MyBB Antispam plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 
 
/**
 * Disallow direct access to this file for security reasons
 * 
 */
if (!defined("IN_MYBB")) exit;

/**
 * Create plugin object
 * 
 */
$plugins->objects['mybbAntispam'] = new mybbAntispam();

/**
 * Standard MyBB info function
 * 
 */
function mybbAntispam_info()
{
    global $lang;

    $lang->load("mybbAntispam");
    
    $lang->mybbAntispamDesc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="3BTVZBUG6TMFQ">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->mybbAntispamDesc;

    return Array(
        'name' => $lang->mybbAntispamName,
        'description' => $lang->mybbAntispamDesc,
        'website' => 'http://lukasztkacz.com',
        'author' => 'Lukasz Tkacz',
        'authorsite' => 'http://lukasztkacz.com',
        'version' => '1.0',
        'guid' => '',
        'compatibility' => '16*'
    );
}

/**
 * Standard MyBB installation functions 
 * 
 */
function mybbAntispam_install()
{
    require_once('mybbAntispam.settings.php');
    mybbAntispamInstaller::install();

    rebuildsettings();
}

function mybbAntispam_is_installed()
{
    global $mybb;

    return (isset($mybb->settings['mybbAntispamCaptcha']));
}

function mybbAntispam_uninstall()
{
    require_once('mybbAntispam.settings.php');
    mybbAntispamInstaller::uninstall();

    rebuildsettings();
}

/**
 * Standard MyBB activation functions 
 * 
 */
function mybbAntispam_activate()
{
    require_once('mybbAntispam.tpl.php');
    mybbAntispamActivator::activate();
}

function mybbAntispam_deactivate()
{
    require_once('mybbAntispam.tpl.php');
    mybbAntispamActivator::deactivate();
}


/**
 * Plugin Class 
 * 
 */
class mybbAntispam
{ 
    /**
     * Array with captcha data after generate
     */
    private $data = array();
    
    /**
     * Variable to hide captcha if previously was valid
     */
    private $hide = false;


    /**
     * Constructor - add plugin hooks
     */
    public function __construct()
    {
        global $plugins;

        $plugins->hooks["admin_tools_menu_logs"][10]["mybbAntispam_adminLink"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'mybbAntispam\']->adminLink($arg);'));
        $plugins->hooks["admin_tools_action_handler"][10]["mybbAntispam_adminHandler"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'mybbAntispam\']->adminHandler($arg);'));
    
        $plugins->hooks["datahandler_user_validate"][10]["mybbAntispam_captchaCheckRegister"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->captchaCheckRegister();'));
        $plugins->hooks["datahandler_user_validate"][10]["mybbAntispam_registerAkismet"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->registerAkismet();'));
        $plugins->hooks["datahandler_user_validate"][10]["mybbAntispam_registerSblam"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->registerSblam();'));

        $plugins->hooks["datahandler_post_insert_post"][10]["mybbAntispam_validatePostSblam"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'mybbAntispam\']->validatePostSblam($arg);'));
        $plugins->hooks["datahandler_post_insert_thread_post"][10]["mybbAntispam_validatePostSblam"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'mybbAntispam\']->validatePostSblam($arg);'));

        $plugins->hooks["member_register_start"][10]["mybbAntispam_captchaGenerate"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->captchaGenerate("Register");'));
        $plugins->hooks["member_register_end"][10]["mybbAntispam_captchaXMLHttpCheck"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->captchaXMLHttpCheck();'));
        
        $plugins->hooks["xmlhttp"][10]["mybbAntispam_captchaXMLHttpRefresh"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'mybbAntispam\']->captchaXMLHttpRefresh();'));
    }
    
    /**
     * Add link to ACP
     */
    public function adminLink(&$sub_menu)
    {
        global $lang;
        
        $lang->load("mybbAntispam");
        $sub_menu[] = array("title" => $lang->mybbAntispamACPLink, "link" => "index.php?module=tools/mybbAntispam");
    }

    /**
     * Add action to ACP
     */
    public function adminHandler(&$actions)
    {
        $actions['mybbAntispam'] = array('file' => 'mybbAntispam.php');
    }

    /**
     * Check user by Akismet
     */
    public function registerAkismet()
    {
        global $db, $lang, $mybb, $session, $user, $userhandler;

        $akismet_status = (int) $this->getConfig('AkismetStatus');
        $akismet_key = trim($this->getConfig('AkismetKey'));
        
        if (!$akismet_status || $akismet_key == '' || THIS_SCRIPT != 'member.php')
        {
            return;
        }
        
        // Is rest valid?
        if (count($userhandler->get_errors()) > 0)
        {
            return;
        }

        // Include class
        require_once(MYBB_ROOT.'inc/plugins/mybbAntispam/Akismet.php');

        $akismet = new mybbAntispam_Akismet($mybb->settings['bburl'], $akismet_key);
        $akismet->setCommentAuthor($user['username']);
        $akismet->setCommentAuthorEmail($user['email']);
        $akismet->setCommentAuthorURL('');
        $akismet->setCommentContent('');
        $akismet->setPermalink($mybb->settings['bburl']);
        
        if ($akismet->isCommentSpam())
        {
            $lang->load("mybbAntispam");
            $userhandler->set_error('mybbAntispam_Error');
            
            // Insert log
            $log_enable = (int) $this->getConfig('LogTime');
            if ($log_enable)
            {
                $sql_array = array(
                    'username'  => $db->escape_string($user['username']),
                    'email'     => $db->escape_string($user['email']),
                    'useragent' => $db->escape_string($session->useragent),
                    'ip'        => $db->escape_string($session->ipaddress),
                    'time'      => TIME_NOW,
                    'type'      => 'Akismet-User',
                );
                $db->insert_query("mybb_antispam_logs", $sql_array);  
            }
        }   
    }
    
    /**
     * Check user by Sblam
     */
    public function registerSblam()
    {
        global $db, $lang, $mybb, $session, $user, $userhandler;

        $sblam_status = (int) $this->getConfig('SblamStatus');
        $sblam_mode = ((int) $this->getConfig('SblamMode')) ? 1 : 2;
        $sblam_key = trim($this->getConfig('SblamKey'));
        
        if (!$sblam_status || THIS_SCRIPT != 'member.php')
        {
            return;
        }
        
        // Is rest valid?
        if (count($userhandler->get_errors()) > 0)
        {
            return;
        }

        // Include class
        require_once(MYBB_ROOT.'inc/plugins/mybbAntispam/Sblam.php');
        if (sblamtestpost() == $sblam_mode)
        {
            $lang->load("mybbAntispam");
            $userhandler->set_error('mybbAntispam_Error');
            
            // Insert log
            $log_enable = (int) $this->getConfig('LogTime');
            if ($log_enable)
            {
                $sql_array = array(
                    'username'  => $db->escape_string($user['username']),
                    'email'     => $db->escape_string($user['email']),
                    'useragent' => $db->escape_string($session->useragent),
                    'ip'        => $db->escape_string($session->ipaddress),
                    'time'      => TIME_NOW,
                    'type'      => 'Sblam-User',
                );
                $db->insert_query("mybb_antispam_logs", $sql_array);  
            }
        }   
    }
        
    /**
     * Sblam action on new post
     */
    public function validatePostSblam(&$post)
    {
        global $db, $mybb, $session;
        
        $sblam_status = (int) $this->getConfig('SblamPostStatus');
        $sblam_mode = ((int) $this->getConfig('SblamMode')) ? 1 : 2;
        $sblam_key = trim($this->getConfig('SblamKey'));
        
        if (!$sblam_status || $mybb->user['uid'] > 0)
        {
            return;
        }
    
        // Include class
        require_once(MYBB_ROOT.'inc/plugins/mybbAntispam/Sblam.php');
        if (sblamtestpost() == $sblam_mode)
        {
            $lang->load("mybbAntispam");
            
            if (!empty($post->post_update_data))
            {
                $post->post_update_data['visible'] = 0;
            }
            elseif (!empty($post->post_insert_data))
            {
                $post->post_insert_data['visible'] = 0;
            }
            
            // Insert log
            $log_enable = (int) $this->getConfig('LogTime');
            if ($log_enable)
            {
                $sql_array = array(
                    'username'  => $this->post_insert_data['username'],
                    'email'     => '',
                    'useragent' => $db->escape_string($session->useragent),
                    'ip'        => $db->escape_string($session->ipaddress),
                    'time'      => TIME_NOW,
                    'type'      => 'Sblam-Post',
                );
                $db->insert_query("mybb_antispam_logs", $sql_array);  
            }
        }
    }         

    /**
     * Add javascript validator in register view to auto-validate field
     */
    public function captchaXMLHttpCheck()
    {
        global $lang, $validator_extra;
        if (!$this->getConfig('Captcha')) return;
        
        $validator_extra .= "\tregValidator.register('{$this->data['inputname']}', 'ajax', {url:'xmlhttp.php?action=validate_captcha', extra_body: 'imagehash', loading_message:'{$lang->js_validator_captcha_valid}', failure_message:'{$lang->js_validator_no_image_text}'});\n";
    }

    /**
     * Refresh simple captcha using ajax
     */
    public function captchaXMLHttpRefresh()
    {
        global $db, $lang, $mybb;
        if (!$this->getConfig('Captcha')) return;

        if ($mybb->input['action'] != "refreshMyBBAntispam")
        {
            return;
        }

        $lang->load("member");

        if (!$this->captchaValidateCaptchaXMLHttp())
        {
            xmlhttp_error($lang->captcha_not_exists);
        }
        $this->captchaGenerate();

        if (!in_array('Content-type', headers_list()))
        {
            header("Content-type: text/plain; charset={$lang->settings['charset']}");
        }
        
        echo $this->data['saltname'] . '|' . $this->data['inputname'] . '|' . $this->data['inputsize'] . '|' . $this->data['imageshash'] .
        "|regValidator.register('" . $this->data['inputname'] . "', 'ajax', {url:'xmlhttp.php?action=validate_captcha', extra_body: 'imagehash', loading_message:'{$lang->js_validator_captcha_valid}', failure_message:'{$lang->js_validator_no_image_text}'});";
        die();
    }

    /**
     * Validate captcha on register
     */
    public function captchaCheckRegister()
    {
        global $db, $mybb, $templates, $mybb_antispam, $theme, $lang, $errors;
        if (!$this->getConfig('Captcha')) return;

        if (THIS_SCRIPT != 'member.php')
        {
            return;
        }

        if (!$this->captchaValidateCaptcha())
        {
            $errors[] = $lang->error_regimageinvalid;
        }
    }

    /**
     * Validate captcha on new post or thread
     */
    public function captchaCheckNewPost()
    {
        global $mybb, $lang, $post_errors;
        if (!$this->getConfig('Captcha')) return;
        
        if ($mybb->user['uid'] > 0)
        {
            return;
        }

        $ajax_mode = ($mybb->input['ajax']) ? true : false;

        if (!$this->captchaValidateCaptcha($ajax_mode))
        {
            $post_errors[] = $lang->invalid_captcha;  
        }
        else
        {
            $this->hide = true;
        }
        
        if ($ajax_mode)
        {
            $this->captchaGenerateXMLHttp();
        }
        
        return;
    }
    
    /**
     * Generate captcha for ajax request in new post
     */
    private function captchaGenerateXMLHttp()
    {
        if (!$this->getConfig('Captcha')) return;
        
        $this->captchaGenerate();
        if (!in_array('Content-type', headers_list()))
        {
            global $lang;
            header("Content-type: text/html; charset={$lang->settings['charset']}");
        }

        echo '<captcha>';
        echo "{$this->data['saltname']}|{$this->data['inputname']}|{$this->data['inputsize']}|{$this->data['imageshash']}|";
        echo ($this->hide) ? $this->data['imagestring'] : '0';
        echo '</captcha>';
    }

    /**
     * Generate captcha on new post or thread
     */
    public function captchaGenerateNewPost()
    {
        if (!$this->getConfig('Captcha')) return;
    
        if (!$this->hide)
        {
            $this->captchaGenerate('Post');
        }
        else
        {
            $this->captchaGenerate('Hidden');
        }
    }

    /**
     * Validate captcha on member login
     */
    public function captchaCheckLogin()
    {
        global $lang, $errors, $do_captcha;
        if (!$this->getConfig('Captcha')) return;

        if ($do_captcha && !$this->captchaValidateCaptcha())
        {
            $errors[] = $lang->error_regimageinvalid;
        }
    }

    /**
     * Generate captcha on member login
     */
    public function captchaGenerateLogin()
    {
        global $do_captcha;
        if (!$this->getConfig('Captcha')) return;

        if ($do_captcha == true)
        {
            $this->captchaGenerate('Post');
        }
    }

    /**
     * Validate captcha main function
     * 
     * @param bool $ajax Ajax mode, if yes, don't delete captcha from db
     * @return int Valid on invalid    
     */
    private function captchaValidateCaptcha($ajax = false)
    {
        global $db, $mybb;
        if (!$this->getConfig('Captcha')) return;
        
        if ($mybb->user['uid'] > 0)
        {
            return 1;
        }

        $imagehash = $db->escape_string($mybb->input['imagehash']);
        $fieldHash = sha1($mybb->settings['adminemail'] . date('d'));
        $inputHash = trim($mybb->input[$fieldHash]);
        $inputCode = trim($mybb->input[$inputHash]);

        if (!$inputCode)
        {
            return false;
        }

        $result = $db->simple_select("captcha", "*", "imagehash='{$imagehash}' AND imagestring='{$inputCode}'");
        $num_rows = (int) $db->num_rows($result);

        if (!$ajax || ($ajax && $num_rows))
        {
            $db->delete_query("captcha", "imagehash='{$imagehash}'");
        }

        if ($num_rows)
        {
            $this->hide = true;
        }

        return $num_rows;
    }

    /**
     * Validate captcha for Ajax - only for hidden captcha
     * 
     * @return int Valid on invalid    
     */
    private function captchaValidateCaptchaXMLHttp()
    {
        global $mybb, $db;
        if (!$this->getConfig('Captcha')) return;

        $imagehash = $db->escape_string($mybb->input['imagehash']);
        $result = $db->simple_select("captcha", "*", "imagehash='$imagehash'");
        return (int) $db->num_rows($result);
    }

    /**
     * Generate captcha - main function
     * 
     * @param string $tpl Template name for eval if needed 
     */
    public function captchaGenerate($tpl = '')
    {
        global $mybb_antispam, $db, $lang, $mybb, $templates;
        if (!$this->getConfig('Captcha')) return;

        if ($mybb->user['uid'] > 0)
        {
            return;
        }

        $this->data = array(
            'imageshash' => md5(random_str(12)),
            'inputname' => sha1(time() . rand()),
            'inputsize' => rand(6, 12),
            'saltname' => sha1($mybb->settings['adminemail'] . date('d')),
        );

        $sql_array = array(
            "imagehash" => $this->data['imageshash'],
            "imagestring" => random_str(5),
            "dateline" => TIME_NOW
        );
        $db->insert_query("captcha", $sql_array);
        $this->data = array_merge($this->data, $sql_array);

        if ($tpl != '')
        {
            $mybbAntispamData = $this->data;
            eval("\$mybb_antispam = \"" . $templates->get("mybbAntispam{$tpl}") . "\";");
        }
    }

    /**
     * Helper function to get variable from config
     * 
     * @param string $name Name of config to get
     * @return string Data config from MyBB Settings
     */
    private function getConfig($name)
    {
        global $mybb;

        return $mybb->settings["mybbAntispam{$name}"];
    }

}
