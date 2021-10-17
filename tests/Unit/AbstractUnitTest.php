<?php

declare(strict_types=1);

namespace Tests\Unit;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Incubator\Test\PHPUnit\UnitTestCase;
use PHPUnit\Framework\IncompleteTestError;

abstract class AbstractUnitTest extends UnitTestCase
{
    private  $loaded = false;

    protected function setUp(): void
    {
        parent::setUp();

        $di = new FactoryDefault();

        Di::reset();
        Di::setDefault($di);
        $di->set('db', function() {
            $mysql = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                "host" => 'localhost',
                "username" => 'root',
                "password" => '',
                "dbname" => 'phalcon'
            ));
            $mysql->query("set names 'utf8'");
            return $mysql;
        });

        $this->loaded = true;
    }

    public function __destruct()
    {
        if (!$this->loaded) {
            throw new IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }
}