<?php

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#

require_once('PHPUnit/Framework.php');
require_once('../smoothgallery.php');
 
class SmoothGallery extends PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function get_smoothgallery_parameter() {
		$meta = "h:500\nw=333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));

		$meta = "height=500\nwidth=333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h', 'height')));
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h', 'HeIgHt')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w', 'wiDTh')));
		$meta = "h=500\nw:333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('height', 'h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('wIdtH', 'w')));

		$meta = "h= 500  \nw : 333";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));
		$meta = "h = 500 \nw   :    333  ";
		$this->assertEquals('500', get_smoothgallery_parameter($meta, array('h')));
		$this->assertEquals('333', get_smoothgallery_parameter($meta, array('w')));

		$values = array(null, 'hurz');
		foreach ($values as $value1) {
			foreach ($values as $value2) {
				$this->assertFalse(get_smoothgallery_parameter($value1, array($value2)));
			}
		}
	}
 
}

?>
