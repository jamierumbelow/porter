<?php namespace JR\Porter\Tests;

use JR\Porter\Tests\Mock\User;
use \PHPUnit_Framework_TestCase;

class User_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->u = new User;
    }

    public function testMayProvidesSimplePermissions()
    {
        $this->u->may('read', 'User');

        $this->assertTrue($this->u->can('read', 'User'));
        $this->assertFalse($this->u->can('update', 'User'));
    }

    public function testMayProvidesComplexCallbackPermissions()
    {
        $variable = FALSE;

        $this->u->may('read', 'User', function($u) use (&$variable)
        {
            $variable = TRUE;
            return TRUE;
        });

        $this->assertTrue($this->u->can('read', 'User'));
        $this->assertTrue($variable, "Callback wasn't called");
    }

    public function testOverlordCanDoAnything()
    {
        $this->u->isOverlord();

        $this->assertTrue($this->u->can('manage', 'User'));
        $this->assertTrue($this->u->can('update', 'anything'));
        $this->assertTrue($this->u->can(rand(0,5), sha1(time())));
    }
}