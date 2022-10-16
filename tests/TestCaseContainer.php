<?php

namespace Letov\HtmlParser\Tests;

use DI\Container;
use PHPUnit\Framework\TestCase;

class TestCaseContainer extends TestCase
{
    protected Container $container;

    public function setUp(): void
    {
        $this->container = require '../../app/bootstrap.php';
    }
}
