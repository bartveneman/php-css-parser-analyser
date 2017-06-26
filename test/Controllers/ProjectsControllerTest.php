<?php

namespace Wallace;

use Wallace\Controllers\AbstractController;
use Wallace\Controllers\ImportsController;
use PHPUnit\Framework\TestCase;

class ProjectsControllerTest extends TestCase
{
    public function setUp()
    {
        $this->importsController = new ImportsController();
    }

    public function tearDown()
    {
        unset($this->importsController);
    }

    public function testConstructor()
    {
        $this->assertTrue($this->importsController instanceof AbstractController);
    }
}
