<?php
/**
#  Copyright 2003-2015 Opmantek Limited (www.opmantek.com)
#
#  ALL CODE MODIFICATIONS MUST BE SENT TO CODE@OPMANTEK.COM
#
#  This file is part of Open-AudIT.
#
#  Open-AudIT is free software: you can redistribute it and/or modify
#  it under the terms of the GNU Affero General Public License as published
#  by the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  Open-AudIT is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Affero General Public License for more details.
#
#  You should have received a copy of the GNU Affero General Public License
#  along with Open-AudIT (most likely in a file named LICENSE).
#  If not, see <http://www.gnu.org/licenses/>
#
#  For further information on Open-AudIT or for a license other than AGPL please see
#  www.opmantek.com or email contact@opmantek.com
#
# *****************************************************************************
*
**/

/*
ALTER TABLE `discovery_scan_options` CHANGE `ssh_ports` `ssh_ports` TEXT NOT NULL AFTER `exclude_ip`;

UPDATE `roles` SET `permissions` = '{\"applications\":\"crud\",\"attributes\":\"crud\",\"baselines\":\"crud\",\"buildings\":\"crud\",\"charts\":\"crud\",\"clouds\":\"crud\",\"conditions\":\"crud\",\"connections\":\"crud\",\"credentials\":\"crud\",\"dashboards\":\"crud\",\"errors\":\"r\",\"floors\":\"crud\",\"queue\":\"cr\",\"summaries\":\"crud\",\"devices\":\"crud\",\"discoveries\":\"crud\",\"discovery_scan_options\":\"crud\",\"fields\":\"crud\",\"files\":\"crud\",\"graph\":\"crud\",\"groups\":\"crud\",\"integrations\":\"crud\",\"invoice\":\"crud\",\"licenses\":\"crud\",\"locations\":\"crud\",\"networks\":\"crud\",\"orgs\":\"crud\",\"queue\":\"cr\",\"queries\":\"crud\",\"racks\":\"crud\",\"rack_devices\":\"crud\",\"reports\":\"r\",\"rooms\":\"crud\",\"rows\":\"crud\",\"scripts\":\"crud\",\"search\":\"crud\",\"sessions\":\"crud\",\"tasks\":\"crud\",\"users\":\"crud\",\"widgets\":\"crud\"}' WHERE `name` = 'org_admin';

UPDATE `configuration` SET `value` = '20190810' WHERE `name` = 'internal_version';

UPDATE `configuration` SET `value` = '3.2.0' WHERE `name` = 'display_version';
*/

$this->log_db('Upgrade database to 3.2.0 commenced');

$this->alter_table('discovery_scan_options', 'ssh_ports', "`ssh_ports` TEXT NOT NULL AFTER exclude_ip");

$this->alter_table('system', 'manufacturer_code', "ADD `manufacturer_code` varchar(200) NOT NULL DEFAULT '' AFTER manufacturer", 'add');

$this->alter_table('system', 'snmp_enterprise_id', "ADD `snmp_enterprise_id` int(10) unsigned NOT NULL DEFAULT '0' AFTER snmp_version", 'add');

$this->alter_table('system', 'snmp_enterprise_name', "ADD `snmp_enterprise_name` varchar(255) NOT NULL DEFAULT '' AFTER snmp_enterprise_id", 'add');

$sql = "DROP TABLE IF EXISTS conditions";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "CREATE TABLE `conditions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `org_id` int(10) unsigned NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `weight` int(10) unsigned NOT NULL DEFAULT '100',
  `inputs` text NOT NULL,
  `outputs` text NOT NULL,
  `edited_by` varchar(200) NOT NULL DEFAULT '',
  `edited_date` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$this->db->query($sql);
$this->log_db($this->db->last_query());

if (php_uname('s') != 'Windows NT') {
	$command = 'mysql -h ' . $this->db->hostname . ' -u ' . $this->db->username . ' -p' . $this->db->password . ' ' . $this->db->database . ' conditions < /usr/local/open-audit/other/assets/conditions.sql';
} else {
	$command = 'c:\\xampp\\mysql\\bin\\mysql.exe -h ' . $this->db->hostname . ' -u ' . $this->db->username . ' -p' . $this->db->password . ' ' . $this->db->database . ' conditions < c:\\xampp\\open-audit\\other\\assets\\conditions.sql';
}

$this->log_db($command);
exec($command);

# set our versions
$sql = "UPDATE `configuration` SET `value` = '20190810' WHERE `name` = 'internal_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$sql = "UPDATE `configuration` SET `value` = '3.2.0' WHERE `name` = 'display_version'";
$this->db->query($sql);
$this->log_db($this->db->last_query());

$this->log_db("Upgrade database to 3.2.0 completed");
$this->config->config['internal_version'] = '20190810';
$this->config->config['display_version'] = '3.2.0';