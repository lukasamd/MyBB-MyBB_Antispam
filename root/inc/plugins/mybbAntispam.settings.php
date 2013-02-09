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
 * Plugin Installator Class
 * 
 */
class mybbAntispamInstaller
{

    public static function install()
    {
        global $db, $lang, $mybb;
        self::uninstall();

        $result = $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder');
        $max_disporder = $db->fetch_field($result, 'max_disporder');
        $disporder = 1;

        $settings_group = array(
            'gid' => 'NULL',
            'name' => 'mybbAntispam',
            'title' => $db->escape_string($lang->mybbAntispamName),
            'description' => $db->escape_string($lang->mybbAntispamGroupDesc),
            'disporder' => $max_disporder + 1,
            'isdefault' => '0'
        );
        $db->insert_query('settinggroups', $settings_group);
        $gid = (int) $db->insert_id();
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamAkismetStatus',
            'title' => $db->escape_string($lang->mybbAntispamAkismetStatus),
            'description' => $db->escape_string($lang->mybbAntispamAkismetStatusDesc),
            'optionscode' => 'onoff',
            'value' => '0',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamAkismetKey',
            'title' => $db->escape_string($lang->mybbAntispamAkismetKey),
            'description' => $db->escape_string($lang->mybbAntispamAkismetKeyDesc),
            'optionscode' => 'text',
            'value' => '',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamSblamStatus',
            'title' => $db->escape_string($lang->mybbAntispamSblamStatus),
            'description' => $db->escape_string($lang->mybbAntispamSblamStatusDesc),
            'optionscode' => 'onoff',
            'value' => '0',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamSblamMode',
            'title' => $db->escape_string($lang->mybbAntispamSblamMode),
            'description' => $db->escape_string($lang->mybbAntispamSblamModeDesc),
            'optionscode' => 'onoff',
            'value' => '0',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamSblamPostStatus',
            'title' => $db->escape_string($lang->mybbAntispamSblamPostStatus),
            'description' => $db->escape_string($lang->mybbAntispamSblamPostStatusDesc),
            'optionscode' => 'onoff',
            'value' => '0',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamSblamPostMode',
            'title' => $db->escape_string($lang->mybbAntispamSblamPostMode),
            'description' => $db->escape_string($lang->mybbAntispamSblamPostModeDesc),
            'optionscode' => 'onoff',
            'value' => '0',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamSblamKey',
            'title' => $db->escape_string($lang->mybbAntispamSblamKey),
            'description' => $db->escape_string($lang->mybbAntispamSblamKeyDesc),
            'optionscode' => 'text',
            'value' => '',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamCaptcha',
            'title' => $db->escape_string($lang->mybbAntispamCaptcha),
            'description' => $db->escape_string($lang->mybbAntispamCaptchaDesc),
            'optionscode' => 'onoff',
            'value' => '1',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $setting = array(
            'sid' => 'NULL',
            'name' => 'mybbAntispamLogTime',
            'title' => $db->escape_string($lang->mybbAntispamLogTime),
            'description' => $db->escape_string($lang->mybbAntispamLogTimeDesc),
            'optionscode' => 'text',
            'value' => '7',
            'disporder' => $disporder++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);
        
        $sql = "CREATE TABLE IF NOT EXISTS " . TABLE_PREFIX . "mybb_antispam_logs (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                username VARCHAR(255) NOT NULL DEFAULT '',
                email VARCHAR(255) NOT NULL DEFAULT '',
                useragent VARCHAR(255) NOT NULL DEFAULT '',
                ip VARCHAR(255) NOT NULL DEFAULT '',
                time INT UNSIGNED NOT NULL,
                type VARCHAR(255) NOT NULL DEFAULT '',
                PRIMARY KEY (id)
                ) DEFAULT CHARSET=utf8;";
        $db->query($sql);
    }

    public static function uninstall()
    {
        global $db;
        
        // Delete settings
        $result = $db->simple_select('settinggroups', 'gid', "name = 'mybbAntispam'");
        $gid = (int) $db->fetch_field($result, "gid");
        
        if ($gid > 0)
        {
            $db->delete_query('settings', "gid = '{$gid}'");
        }
        $db->delete_query('settinggroups', "gid = '{$gid}'");
        
        $db->drop_table('mybb_antispam_logs');
    }

}
