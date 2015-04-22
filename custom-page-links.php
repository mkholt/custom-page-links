<?php

/*
Plugin Name: Custom Page Links
Plugin URI: https://github.com/mkholt/custom-page-links
Description: Set a custom list of links on a page or news post. The links can be added as a widget to a sidebar.
Version: 1.0
Author: morten
Author URI: http://t-hawk.com
License: GPL2
*/

/*  Copyright 2015 Morten Holt (email : thawk@t-hawk.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace dk\mholt\CustomPageLinks;

class CustomPageLinks
{
	/**
	 * @return CustomPageLinks
	 */
	public static function initialize()
	{
		$cpl = new CustomPageLinks();

		return $cpl;
	}

	private function __construct()
	{

	}
}

CustomPageLinks::initialize();