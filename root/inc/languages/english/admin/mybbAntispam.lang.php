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

$l['mybbAntispamName'] = 'MyBB Antispam';
$l['mybbAntispamDesc'] = 'This plugin check user data in Akismet system during registration.';
$l['mybbAntispamGroupDesc'] = 'Settings for "MyBB Antispam" plugin';

$l['mybbAntispamAkismetStatus'] = 'Turn on/off Akismet on register';
$l['mybbAntispamAkismetStatusDesc'] = 'Enable or disable Akismet filter during registration.';
$l['mybbAntispamAkismetKey'] = 'Akismet Key';
$l['mybbAntispamAkismetKeyDesc'] = 'Key to Akismet, <b>it is required to use Akismet</b>.<br />You can get your key on <a href="http://akismet.com/">http://akismet.com/</a>.';

$l['mybbAntispamSblamStatus'] = 'Turn on/off Sblam on register';
$l['mybbAntispamSblamStatusDesc'] = 'Enable or disable Sblam filter during registration.';
$l['mybbAntispamSblamPostStatus'] = 'Turn on/off Sblam on guests posting';
$l['mybbAntispamSblamPostStatusDesc'] = 'Enable or disable Sblam filter during guests posting.';

$l['mybbAntispamSblamMode'] = 'Sblam Extended Protection - Registration';
$l['mybbAntispamSblamModeDesc'] = 'If enabled, Sblam will use strong filters on register validation. This can cause false-positive and it is not recommended.';
$l['mybbAntispamSblamPostMode'] = 'Sblam Extended Protection - Posting';
$l['mybbAntispamSblamPostModeDesc'] = 'If enabled, Sblam will use strong filters on guests posting. This can cause false-positive and it is not recommended.';

$l['mybbAntispamSblamKey'] = 'Sblam Key';
$l['mybbAntispamSblamKeyDesc'] = 'Key to Sblam, <b>it is optional to use Sblam</b>.<br />You can get your key on <a href="http://sblam.com">http://sblam.com</a>.';

$l['mybbAntispamCaptcha'] = 'Improved captcha';
$l['mybbAntispamCaptchaDesc'] = 'If enabled, MyBB will use improved captcha (looks like standard captcha).<br /><b>You must turn off build-in captcha before use this option.</b>';

$l['mybbAntispamLogTime'] = 'Logs expire time';
$l['mybbAntispamLogTimeDesc'] = 'Akismet logs expire time in days. Choose 0 to disable logs.';

$l['mybbAntispamACPLink'] = 'Akismet Logs';
$l['mybbAntispamACPLinkDesc'] = 'This logs contains rejected registarations which Akismet marked as spam.';
$l['mybbAntispamACP_Time'] = 'Register time';
$l['mybbAntispamACP_Type'] = 'Type';
$l['mybbAntispamACP_Username'] = 'Username';
$l['mybbAntispamACP_IP'] = 'IP address';
$l['mybbAntispamACP_UA'] = 'Web browser';
$l['mybbAntispamACP_NoLogs'] = 'No logs to display';
