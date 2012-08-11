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
	private $caldav_path;
	private $calendars;
	private $cache;
	
	public function __construct($cal)
	{
		$this->cal = $cal;
		$this->rc = $cal->rc;
		
		// load library classes
		require_once($this->cal->home . '/lib/caldav-client-v2.php');
		require_once('/usr/share/awl/inc/iCalendar.php');
		
		// get the caldav path
		$this->caldav_path = str_replace('%u', $_SESSION['username'], $this->rc->config->get('caldav_path'));
		
		// Open CalDAV connection
		$this->caldav = new CalDAVClient($this->caldav_path, $_SESSION['username'], $this->rc->decrypt($_SESSION['password']));
	}
	
	
	/**
	 * Get a list of available calendars from this source
	 */
	public function list_calendars()
	{
		if (!$this->calendars)
		{
			$this->calendars = array();
			$calendars = $this->caldav->FindCalendars();
			foreach ($calendars as $val)
			{
				$folder = array();
				$fpath = explode("/", $val->url, -1);
				if (is_array($fpath))
				{
					$id = array_pop($fpath);
					$cal = array();
					$cal['active'] = true;
					$cal['name'] = $val->displayname;
					$cal['color'] = 'cc0000';
					$cal['showalarms'] = 0;
					$this->calendars[$id] = $cal;
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
		$begin = gmdate("Ymd\THis\Z", $start);
		$finish = gmdate("Ymd\THis\Z", $end);
		
		if (empty($calendars))
			$calendars = array_keys($this->calendars);
		else if (is_string($calendars))
			$calendars = explode(',', $calendars);
		
		$items = array();
		
		foreach ($calendars as $id)
		{
			$items = $this->_GetEvents($id, $begin, $finish);
		}
		return $items;
	}
	
	private function _GetEvents($id, $start, $end)
	{
		$path = $this->caldav_path . $id . "/";
		$events = $this->caldav->GetEvents($start, $end, $path);
		foreach ($events as $e)
		{
			$item = array();
			$item['id'] = $e['href'];
			$item['calendar'] = $id;
			
			$ical = new iCalComponent($e['data']);
			
			//Get The Timezone
			$timezones = $ical->GetComponents("VTIMEZONE");
			$timezone = "";
			if (count($timezones) > 0)
			{
				$timezone = $this->_ParseTimezone($timezones[0]->GetPValue("TZID"));
			}
			if (!$timezone)
			{
				$timezone = date_default_timezone_get();
			}
			
			$vevents = $ical->GetComponents("VTIMEZONE", false);
			foreach ($vevents as $event)
			{
				$rec = $event->GetProperties("RECURRENCE-ID");
				if (count($rec) > 0)
				{
					$item['recurrence_id'] = $id;
				}
				$this->_ParseEvent($event, $item);
			}
			$this->cache['id'] = $item;
			$items[] = $item;
		}
		return $items;
	}
	
	private function _ParseEvent($event, &$item)
	{
		$properties = $event->GetProperties();
		foreach ($properties as $property)
		{
			switch ($property->Name())
			{
				case "UID":
					$item['uid'] = $property->Value();
					break;
						
				case "DTSTART":
					$item['start'] = $this->_MakeUTCDate($property->Value(), $this->_ParseTimezone($property->GetParameterValue("TZID")));
					if (strlen($property->Value()) == 8)
					{
						$item['allday'] = true;
					}
					break;
		
				case "DTEND":
					$item['end'] = $this->_MakeUTCDate($property->Value(), $this->_ParseTimezone($property->GetParameterValue("TZID")));
					if (strlen($property->Value()) == 8)
					{
						$item['allday'] = true;
					}
					break;
		
				case "LAST-MODIFIED":
					$item['changed'] = $this->_MakeUTCDate($property->Value());
					break;
						
				case "SUMMARY":
					$item['title'] = $property->Value();
					break;
		
				case "LOCATION":
					$item['location'] = $property->Value();
					break;
		
				case "DESCRIPTION":
					$item['description'] = $property->Value();
					break;
						
				case "RRULE":
					$item['recurrence'] = $this->_ParseRRULE($property);
					break;
					
				case "EXDATE":
					$exdate = $property->Value();
					if (!is_array($item['recurrence']))
					{
						$item['recurrence'] = array();
					}
					$item['recurrence']['EXDATE'] = array();
					$dates = explode(",", $exdate);
					foreach ($dates as $date)
					{
						$item['recurrence']['EXDATE'][] = $this->_MakeUTCDate($date);
					}
					break;
					
				case "CATEGORIES":
					$item['categories'] = $property->Value();
					break;
				
				case "TRANSP":
					switch ($property->Value())
					{
						case "TRANSPARENT":
							$item['free_busy'] = "free";
							break;
						case "OPAQUE":
							$item['free_busy'] = "busy";
							break;
					}
					break;
					
				case "PRIORITY":
					$item['priority'] = $property->Value();
					break;
					
				case "CLASS":
					switch ($property->Value())
					{
						case "PUBLIC":
							$item['sensitivity'] = "0";
							break;
						case "PRIVATE":
							$item['sensitivity'] = "1";
							break;
						case "CONFIDENTIAL":
							$item['sensitivity'] = "2";
							break;
					}
					break;
			}
		}
	}
	
	private function _ParseRRULE($property)
	{
		$rrule = array();
		$freq = $property->GetParameterValue("FREQ");
		if ($freq)
		{
			$rrule['FREQ'] = $freq;
		}
		$rrules = explode(";", $property->Value());
		foreach ($rrules as $rrule)
		{
			$rule = explode("=", $rrule);
			switch ($rule[0])
			{
				case "INTERVAL":
					$rrule['INTERVAL'] = $rule[1];
					break;
				
				case "UNTIL":
					$rrule['UNTIL'] = $this->_MakeUTCDate($rule[1]);
					break;
				
				case "COUNT":
					$rrule['COUNT'] = $rule[1];
					break;
			}
		}
		return $rrule;
	}
	
	/**
	 * Generate date object from string and timezone.
	 * @param string $value
	 * @param string $timezone
	 */
	private function _MakeUTCDate($value, $timezone = null)
	{
		$tz = null;
		if ($timezone)
		{
			$tz = timezone_open($timezone);
		}
		if (!$tz)
		{
			//If there is no timezone set, we use the default timezone
			$tz = timezone_open(date_default_timezone_get());
		}
		//20110930T090000Z
		$date = date_create_from_format('Ymd\THis\Z', $value, timezone_open("UTC"));
		if (!$date)
		{
			//20110930T090000
			$date = date_create_from_format('Ymd\THis', $value, $tz);
		}
		if (!$date)
		{
			//20110930 (Append T000000Z to the date, so it starts at midnight)
			$date = date_create_from_format('Ymd\THis\Z', $value . "T000000Z", $tz);
		}
		return $date;
	}
	
	/**
	 * Generate a tzid from various formats
	 * @param str $timezone
	 * @return timezone id
	 */
	private function _ParseTimezone($timezone)
	{
		//(GMT+01.00) Amsterdam / Berlin / Bern / Rome / Stockholm / Vienna
		if (preg_match('/GMT(\\+|\\-)0(\d)/', $timezone, $matches))
		{
			return "Etc/GMT" . $matches[1] . $matches[2];
		}
		//(GMT+10.00) XXX / XXX / XXX / XXX
		if (preg_match('/GMT(\\+|\\-)1(\d)/', $timezone, $matches))
		{
			return "Etc/GMT" . $matches[1] . "1" . $matches[2];
		}
		///inverse.ca/20101018_1/Europe/Amsterdam or /inverse.ca/20101018_1/America/Argentina/Buenos_Aires
		if (preg_match('/\/[.[:word:]]+\/\w+\/(\w+)\/([\w\/]+)/', $timezone, $matches))
		{
			return $matches[1] . "/" . $matches[2];
		}
		return trim($timezone, '"');
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