<?php

/**
 * CalDAV driver for the Calendar plugin
 *
 * @version @package_version@
 * @author Jean-Louis Dupond <jean-louis@dupond.be>
 *
 * Copyright (C) 2012, Jean-Louis Dupond <jean-louis@dupond.be>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */


class caldav_driver extends calendar_driver
{
	private $cal;
	private $rc;
	private $caldav;
	private $calendars;
	
	public function __construct($cal)
	{
		$this->cal = $cal;
		$this->rc = $cal->rc;
		
		// load library classes
		require_once($this->cal->home . '/lib/caldav-client-v2.php');
		
		// Open CalDAV connection
		$this->caldav = new CalDAVClient("http://calendar.dupie.be/caldav.php/info@dupondje.be/home", $this->rc->user->ID, $this->rc->decrypt($_SESSION['password']));
	}
	
	
	/**
	 * Get a list of available calendars from this source
	 */
	public function list_calendars()
	{
		if (!$this->calendars)
		{
			$this->calendars = array();
			$calendars = $this->_caldav->FindCalendars();
			foreach ($calendars as $val)
			{
				$folder = array();
				$fpath = explode("/", $val->url, -1);
				if (is_array($fpath))
				{
					$id = array_pop($fpath);
					$this->calendars[$id] = $val;
				}
			}
		}
		return $this->calendars;
	}
	
	/**
	 * Create a new calendar assigned to the current user
	 *
	 * @param array Hash array with calendar properties
	 *        name: Calendar name
	 *       color: The color of the calendar
	 *  showalarms: True if alarms are enabled
	 * @return mixed ID of the calendar on success, False on error
	 */
	public function create_calendar($prop)
	{
		//Not allowed ATM
		return false;
	}
	
	/**
	 * Update properties of an existing calendar
	 *
	 * @param array Hash array with calendar properties
	 *          id: Calendar Identifier
	 *        name: Calendar name
	 *       color: The color of the calendar
	 *  showalarms: True if alarms are enabled (if supported)
	 * @return boolean True on success, Fales on failure
	 */
	public function edit_calendar($prop)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Set active/subscribed state of a calendar
	 *
	 * @param array Hash array with calendar properties
	 *          id: Calendar Identifier
	 *      active: True if calendar is active, false if not
	 * @return boolean True on success, Fales on failure
	 */
	public function subscribe_calendar($prop)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Delete the given calendar with all its contents
	 *
	 * @param array Hash array with calendar properties
	 *      id: Calendar Identifier
	 * @return boolean True on success, Fales on failure
	 */
	public function remove_calendar($prop)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Add a single event to the database
	 *
	 * @param array Hash array with event properties (see header of this file)
	 * @return mixed New event ID on success, False on error
	 */
	public function new_event($event)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Update an event entry with the given data
	 *
	 * @param array Hash array with event properties (see header of this file)
	 * @return boolean True on success, False on error
	 */
	public function edit_event($event)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Move a single event
	 *
	 * @param array Hash array with event properties:
	 *      id: Event identifier
	 *   start: Event start date/time as DateTime object
	 *     end: Event end date/time as DateTime object
	 *  allday: Boolean flag if this is an all-day event
	 * @return boolean True on success, False on error
	 */
	public function move_event($event)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Resize a single event
	 *
	 * @param array Hash array with event properties:
	 *      id: Event identifier
	 *   start: Event start date/time as DateTime object with timezone
	 *     end: Event end date/time as DateTime object with timezone
	 * @return boolean True on success, False on error
	 */
	public function resize_event($event)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Remove a single event from the database
	 *
	 * @param array   Hash array with event properties:
	 *      id: Event identifier
	 * @param boolean Remove event irreversible (mark as deleted otherwise,
	 *                if supported by the backend)
	 *
	 * @return boolean True on success, False on error
	 */
	public function remove_event($event, $force = true)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Return data of a single event
	 *
	 * @param mixed  UID string or hash array with event properties:
	 *        id: Event identifier
	 *  calendar: Calendar identifier (optional)
	 * @param boolean If true, only writeable calendars shall be searched
	 * @return array Event object as hash array
	 */
	public function get_event($event, $writeable = null)
	{
		// Not implemented
		return false;
	}
	
	/**
	 * Get events from source.
	 *
	 * @param  integer Event's new start (unix timestamp)
	 * @param  integer Event's new end (unix timestamp)
	 * @param  string  Search query (optional)
	 * @param  mixed   List of calendar IDs to load events from (either as array or comma-separated string)
	 * @return array A list of event objects (see header of this file for struct of an event)
	 */
	public function load_events($start, $end, $query = null, $calendars = null)
	{
		// Not implemented
		return null;
	}
	
	/**
	 * Get a list of pending alarms to be displayed to the user
	 *
	 * @param  integer Current time (unix timestamp)
	 * @param  mixed   List of calendar IDs to show alarms for (either as array or comma-separated string)
	 	* @return array A list of alarms, each encoded as hash array:
	 *         id: Event identifier
	 *        uid: Unique identifier of this event
	 *      start: Event start date/time as DateTime object
	 *        end: Event end date/time as DateTime object
	 *     allday: Boolean flag if this is an all-day event
	 *      title: Event title/summary
	 *   location: Location string
	 */
	public function pending_alarms($time, $calendars = null)
	{
		// Not implemented
		return null;
	}
	
	/**
	 * (User) feedback after showing an alarm notification
	 * This should mark the alarm as 'shown' or snooze it for the given amount of time
	 *
	 * @param  string  Event identifier
	 * @param  integer Suspend the alarm for this number of seconds
	 */
	public function dismiss_alarm($event_id, $snooze = 0)
	{
		// Not implemented
		return false;
	}
	
	
}