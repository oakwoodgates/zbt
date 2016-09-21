<?php

class ZBT_Gravityforms_Test extends WP_UnitTestCase {

	function test_sample() {
		// replace this with some actual testing code
		$this->assertTrue( true );
	}

	function test_class_exists() {
		$this->assertTrue( class_exists( 'ZBT_Gravityforms') );
	}

	function test_class_access() {
		$this->assertTrue( zbt()->gravityforms instanceof ZBT_Gravityforms );
	}
}
