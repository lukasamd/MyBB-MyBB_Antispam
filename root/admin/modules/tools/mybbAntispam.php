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

$lang->load("mybbAntispam");
$page->add_breadcrumb_item($lang->mybbAntispamACPLink, "index.php?module=config/mybbAntispam");

// Cleanup old logs
$time_delete = (int) $mybb->settings['mybbAntispamTimeLog'];
$time_delete = TIME_NOW - ($time_delete * 24 * 3600);
$db->delete_query("akismet_register_logs", "time < '{$time_delete}'");

// Top menu
if ($mybb->input['action'] == "add" || $mybb->input['action'] == "edit" || !$mybb->input['action'])
{
    $sub_tabs['mybbAntispam'] = array(
        'title' => $lang->mybbAntispamACPLink,
        'link' => "index.php?module=tools/mybbAntispam",
        'description' => $lang->mybbAntispamACPLinkDesc
    );
}

// Action
$page->add_breadcrumb_item($lang->mybbAntispamACPLink);
$page->output_header($lang->mybbAntispamACPLink);
$page->output_nav_tabs($sub_tabs, 'idoard');

$table = new Table;
$table->construct_header($lang->mybbAntispamACP_Time, array("class" => "align_center"));
$table->construct_header($lang->mybbAntispamACP_Type, array("class" => "align_center"));
$table->construct_header($lang->mybbAntispamACP_Username, array("class" => "align_center"));
$table->construct_header($lang->mybbAntispamACP_IP, array("class" => "align_center"));
$table->construct_header($lang->mybbAntispamACP_UA, array("class" => "align_center"));

$result = $db->simple_select('akismet_register_logs', '*');
if ($db->num_rows($result))
{
    while ($row = $db->fetch_array($result))
    {
        $table->construct_cell(date("jS M Y, G:i", $row['time']));
        $table->construct_cell($row['type']);
        $table->construct_cell($row['username']);
        $table->construct_cell($row['ip']);
        $table->construct_cell($row['useragent']);
        $table->construct_row();
    }
}
else
{
    $table->construct_cell("<b>{$lang->mybbAntispamACP_NoLogs}</b>", array("class" => "align_center", "colspan" => 4));
    $table->construct_row();
}


$table->output($lang->mybbAntispamACPLink);
$page->output_footer();