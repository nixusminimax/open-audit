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
* PHP version 5.3.3
* 
* @category  Model
* @package   Racks
* @author    Mark Unwin <marku@opmantek.com>
* @copyright 2014 Opmantek
* @license   http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @version   GIT: Open-AudIT_3.3.0
* @link      http://www.open-audit.org
*/

/**
* Base Model Racks
*
* @access   public
* @category Model
* @package  Racks
* @author   Mark Unwin <marku@opmantek.com>
* @license  http://www.gnu.org/licenses/agpl-3.0.html aGPL v3
* @link     http://www.open-audit.org
 */
class M_racks extends MY_Model
{
    /**
    * Constructor
    *
    * @access public
    */
    public function __construct()
    {
        parent::__construct();
        $this->log = new stdClass();
        $this->log->status = 'reading data';
        $this->log->type = 'system';
    }

    /**
     * Read an individual item from the database, by ID
     *
     * @param  int $id The ID of the requested item
     * @return array The array of requested items
     */
    public function read($id = 0)
    {
        $id = intval($id);
        $sql = 'SELECT racks.*, orgs.name AS `orgs.name`, `rows`.`name` as `rows.name`, rooms.name as `rooms.name`, floors.name as `floors.name`, buildings.name as `buildings.name`, locations.name as `locations.name`, count(rack_devices.id) as `rack_devices_count`, SUM(rack_devices.height) AS `used`, COALESCE(racks.ru_height, 0) - COALESCE(SUM(rack_devices.height), 0) AS `free` FROM `racks` LEFT JOIN orgs ON (racks.org_id = orgs.id) LEFT JOIN `rows` ON (`rows`.`id` = racks.row_id) LEFT JOIN rooms ON (rooms.id = `rows`.`room_id`) LEFT JOIN floors ON (floors.id = rooms.floor_id) LEFT JOIN buildings ON (buildings.id = floors.building_id) LEFT JOIN locations ON (locations.id = buildings.location_id) LEFT JOIN rack_devices ON (rack_devices.rack_id = racks.id) WHERE racks.id = ?';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'racks');
        return ($result);
    }

    /**
     * Delete an individual item from the database, by ID
     *
     * @param  int $id The ID of the requested item
     * @return bool True = success, False = fail
     */
    public function delete($id = '')
    {
        $id = intval($id);
        $sql = 'DELETE FROM `racks` WHERE `id` = ?';
        $data = array($id);
        $test = $this->run_sql($sql, $data);
        if ( ! empty($test)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read the associated items parents from the DB by ID
     * 
     * @param  int|integer $id [description]
     * @return [type]          [description]
     */
    public function parent($id = '')
    {
        $id = intval($id);
        $sql = 'SELECT `rows`.* FROM `rows`, racks WHERE `rows`.`id` = `racks`.`row_id` AND `racks`.`id` = ?';
        $data = array(intval($id));
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'rows');
        return ($result);
    }

    /**
     * Read the associated items children from the DB by ID
     * 
     * @param  int|integer $id [description]
     * @return [type]          [description]
     */
    public function children($id = '')
    {
        $id = intval($id);
        $sql = 'SELECT rack_devices.*, orgs.name AS `orgs.name`, racks.name as `racks.name`, racks.id as `racks.id`, 0 as `system_count`, rows.name as `rows.name`, rooms.name as `rooms.name`, floors.name as `floors.name`, buildings.name as `buildings.name`, locations.name as `locations.name`, image.filename as `image.filename`, system.name as `system.name`, system.ip as `system.ip`, system.type as `system.type`, system.id as `system.id`, system.icon as `system.icon` FROM `rack_devices` LEFT JOIN orgs ON (orgs.id = rack_devices.org_id) LEFT JOIN racks ON (racks.id = rack_devices.rack_id) LEFT JOIN `rows` ON (`rows`.`id` = racks.row_id) LEFT JOIN rooms ON (rooms.id = `rows`.`room_id`) LEFT JOIN floors ON (floors.id = rooms.floor_id) LEFT JOIN buildings ON (buildings.id = floors.building_id) LEFT JOIN locations ON (locations.id = buildings.location_id) LEFT JOIN image ON (image.system_id = rack_devices.system_id and image.orientation = "front") LEFT JOIN system ON (system.id = rack_devices.system_id) WHERE rack_devices.rack_id = ? GROUP BY rack_devices.id';
        $data = array($id);
        $result = $this->run_sql($sql, $data);
        $result = $this->format_data($result, 'rack_devices');
        return ($result)    ;
    }

    /**
     * Read the collection from the database
     *
     * @param  int $user_id  The ID of the requesting user, no $response->meta->filter used and no $response->data populated
     * @param  int $response A flag to tell us if we need to use $response->meta->filter and populate $response->data
     * @return bool True = success, False = fail
     */
    public function collection($user_id = null, $response = null)
    {
        $CI = & get_instance();
        if ( ! empty($user_id)) {
            $org_list = array_unique(array_merge($CI->user->orgs, $CI->m_orgs->get_user_descendants($user_id)));
            $sql = 'SELECT * FROM racks WHERE org_id IN (' . implode(',', $org_list) . ')';
            $result = $this->run_sql($sql, array());
            $result = $this->format_data($result, 'racks');
            return $result;
        }
        if ( ! empty($response)) {
            $total = $this->collection($CI->user->id);
            $CI->response->meta->total = count($total);
            $sql = 'SELECT ' . $CI->response->meta->internal->properties . ', orgs.id AS `orgs.id`, orgs.name AS `orgs.name`, rows.id AS `rows.id`, rows.name as `rows.name`, rooms.id AS `rooms.id`, rooms.name as `rooms.name`, floors.id AS `floors.id`, floors.name as `floors.name`, buildings.id AS `buildings.id`, buildings.name as `buildings.name`, locations.id AS `locations.id`, locations.name as `locations.name`, count(rack_devices.id) as `rack_devices_count`, SUM(rack_devices.height) AS `used`, COALESCE(racks.ru_height, 0) - COALESCE(SUM(rack_devices.height), 0) AS `free` FROM `racks` LEFT JOIN orgs ON (racks.org_id = orgs.id) LEFT JOIN `rows` ON (rows.id = racks.row_id) LEFT JOIN rooms ON (rooms.id = rows.room_id) LEFT JOIN floors ON (floors.id = rooms.floor_id) LEFT JOIN buildings ON (buildings.id = floors.building_id) LEFT JOIN locations ON (locations.id = buildings.location_id) LEFT JOIN rack_devices ON (rack_devices.rack_id = racks.id) ' .
                $CI->response->meta->internal->filter . ' ' . 
                ' GROUP BY racks.id ' . 
                $CI->response->meta->internal->sort . ' ' . 
                $CI->response->meta->internal->limit;
            $result = $this->run_sql($sql, array());
            $CI->response->data = $this->format_data($result, 'racks');
            $CI->response->meta->filtered = count($CI->response->data);
        }
    }
}
// End of file m_racks.php
// Location: ./models/m_racks.php
