<?php

namespace Letov\HtmlParser\Node;

use Letov\HtmlParser\Node\Attr\AttrInterface;

class Node implements NodeInterface
{
    private ?string $body;
    private ?string $name;
    private array $attrs;
    private array $children;

    public function __construct()
    {
        $this->clear();
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function addAttr(AttrInterface $attr): void
    {
        $this->attrs[] = $attr;
    }

    /**
     * @param AttrInterface[] $attrs
     * @return void
     */
    public function setAttrs(array $attrs): void
    {
        $this->attrs = $attrs;
    }

    /**
     * @return AttrInterface[]
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @param NodeInterface[] $children
     * @return void
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function clear(): void
    {
        $this->body = null;
        $this->name = null;
        $this->attrs = [];
        $this->children = [];
    }

    public function isNullNode(): bool
    {
        return (is_null($this->body) && is_null($this->name));
    }
}
