<?php

use L20n\Compiler;
use L20n\Parser;

class GlobalsTest extends PHPUnit_Framework_TestCase
{
    /** @var Parser */
    private $parser;
    /** @var Compiler */
    private $compiler;
    /** @var int */
    private $max_nesting_level_to_set = 200;

    /**
     *
     */
    public function setUp()
    {
        // To avoid the fatal error of nested function caused by xdebug.
        $current_max_nesting_level = ini_get('xdebug.max_nesting_level');
        ini_set('xdebug.max_nesting_level', $this->max_nesting_level_to_set);
        $this->max_nesting_level_to_set = $current_max_nesting_level;

        $this->parser = new Parser();
        $this->compiler = new Compiler();
    }

    /**
     *
     */
    public function tearDown()
    {
        ini_set('xdebug.max_nesting_level', $this->max_nesting_level_to_set);

        Compiler::reset();
    }

    public function test_global_callable()
    {
        $source = '
<foo "{{ @hour }}">
<bar "{{ @test(12345) }}">
';

        $this->compiler->registerGlobal(new TestGlobal());

        $ast = $this->parser->parse($source);
        $env = $this->compiler->compile($ast);

        $this->assertEquals('12,345', $env->bar->getString());
    }

    public function test_not_registered_global()
    {
        $source = '
<bar "{{ @test }}">
';

        $ast = $this->parser->parse($source);
        $env = $this->compiler->compile($ast);

        try {
            $env->bar->getString();
            $this->assertTrue(false);
        } catch (\L20n\Compiler\Exception\ValueException $ex) {
            $this->assertEquals('No globals set (tried @test)', $ex->getMessage());
        }
    }
}

class TestGlobal extends \L20n\Platform\GlobalBase
{
    public $id = 'test';

    protected function _get()
    {
        return function ($args) {
            $number = isset($args[0]) ? $args[0] : null;

            return number_format($number, 0, '.', ',');
        };
    }
}