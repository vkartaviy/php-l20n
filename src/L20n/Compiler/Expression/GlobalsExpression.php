<?php

namespace L20n\Compiler\Expression;

use L20n\Compiler\EntryInterface;
use L20n\Compiler\Exception\RuntimeException;
use L20n\Compiler;
use L20n\Compiler\Locals;
use L20n\Platform\GlobalBase;

/**
 * Class Compiler\Expression\GlobalsExpression
 * @package L20n
 */
class GlobalsExpression implements ExpressionInterface
{
    /** @var string */
    private $name = '';

    /** @var GlobalBase */
    private $global;

    /**
     * @param array $node
     * @param EntryInterface|null $entry
     * @param array|null $index
     * @throws RuntimeException
     */
    public function __construct(array $node, EntryInterface $entry = null, array $index = null)
    {
        $this->name = $node['id']['name'];

        if (isset($entry->globals->{$this->name})) {
            $this->global = $entry->globals->{$this->name};
        }
    }

    /**
     * @param Locals $locals
     * @param \stdClass|null $ctxdata
     * @param string|null $prop
     * @return array
     * @throws RuntimeException
     */
    public function __invoke(Locals $locals, \stdClass $ctxdata = null, $prop = null)
    {
        if (!isset($this->global)) {
            throw new RuntimeException(sprintf('Reference to an unknown global @%s', $this->name));
        }

        /** @var mixed $value */
        $value = null;

        try {
            $value = $this->global->get();
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf('Cannot evaluate global @%s', $this->name));
        }

        return [$locals, $value];
    }
}
