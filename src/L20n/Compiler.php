<?php

namespace L20n;

use L20n\Compiler\Exception\CompilerException;
use L20n\Platform\GlobalBase;

/**
 * Class Compiler
 * @package L20n
 */
class Compiler
{
    /** @var array */
    protected static $_entryTypes = [
        'Entity' => '\\L20n\\Compiler\\Entity',
        'Macro'  => '\\L20n\\Compiler\\Macro'
    ];

    /**
     * @param array $ast
     * @param \stdClass $env
     * @param \stdClass $globals
     * @return \stdClass
     * @throws \Exception
     */
    public function compile(array $ast, \stdClass $env = null, \stdClass $globals = null)
    {
        if ($env === null) {
            $env = new \stdClass();
        }

        if ($globals === null) {
            $globals = new \stdClass();
        }

        foreach ($ast['body'] as $entry) {
            if (isset(static::$_entryTypes[$entry['type']])) {
                /** @var string $constructor */
                $constructor = static::$_entryTypes[$entry['type']];
                /** @var string $name */
                $name = $entry['id']['name'];

                try {
                    $env->$name = new $constructor($entry, $env, $globals);
                } catch (CompilerException $e) {
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }

        return $env;
    }
}
