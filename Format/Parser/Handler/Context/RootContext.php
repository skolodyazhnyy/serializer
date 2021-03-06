<?php
/**
 * This file is part of the serializer project
 *
 * (c) Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 *
 */

namespace Bcn\Component\Serializer\Format\Parser\Handler\Context;

class RootContext implements ContextInterface
{
    /** @var ContextInterface */
    protected $context;

    /** @var string */
    protected $rootNode;

    /**
     * @param ContextInterface $context
     * @param string           $rootNode
     */
    public function __construct(ContextInterface $context, $rootNode = null)
    {
        $this->context  = $context;
        $this->rootNode = $rootNode;
    }

    /**
     * @param  string           $name
     * @param  array            $attributes
     * @return ContextInterface
     * @throws \Exception
     */
    public function start($name, array $attributes = array())
    {
        if ($this->rootNode !== null && $this->rootNode != $name) {
            throw new \Exception('Root node name mismatch');
        }

        return $this->context;
    }

    /**
     * @param string $data
     */
    public function append($data)
    {
    }

    /**
     * Close sub context
     *
     * @param string $name
     * @param mixed  $value
     */
    public function end($name, $value)
    {
    }

    /**
     * Reset context state
     */
    public function reset()
    {
    }

    /**
     * Fetch context content
     *
     * @return mixed
     */
    public function fetch()
    {
        return $this->context->fetch();
    }
}
