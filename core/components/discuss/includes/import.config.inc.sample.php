<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
/**
 * Sample Import config
 *
 * @var array $config
 */
$config['smf']['dsn'] = 'mysql:dbname=smf;host=127.0.0.1';
$config['smf']['username'] = 'username';
$config['smf']['password'] = 'password';
$config['smf']['tablePrefix'] = 'smf_';

$config['live'] = false;
$config['import_users'] = false;
$config['import_categories'] = false;
$config['import_private_messages'] = false;
$config['import_ignore_boards'] = false;

$config['default_user_group'] = 'Forum Full Member';
$config['usergroup_prefix'] = 'Forum ';
$config['attachments_path'] = false;


