<?php

namespace niw\CStatusMessage;

/**
* A testclass
*
*/
class CStatusMessageTest extends \PHPUnit_Framework_TestCase
{
  public function testAddInfoMessage() {
    $status = new \niw\CStatusMessage\CStatusMessage($di);
    $this->assertEquals("test", "test", "The name does not match.");
  }

}
