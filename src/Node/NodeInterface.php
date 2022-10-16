<?php

namespace Letov\HtmlParser\Node;

use Letov\HtmlParser\Node\Attr\AttrInterface;
use phpDocumentor\Reflection\Types\Boolean;

interface NodeInterface
{
    public function setBody(?string $body): void;
    public function getBody(): ?string;
    public function setName(?string $name): void;
    public function getName(): ?string;
    public function addAttr(AttrInterface $attr): void;
    /**
     * @param AttrInterface[] $attrs
     * @return void
     */
    public function setAttrs(array $attrs): void;
    /**
     * @return AttrInterface[]
     */
    public function getAttrs(): array;

    /**
     * @param NodeInterface[] $children
     * @return void
     */
    public function setChildren(array $children): void;
    /**
     * @return NodeInterface[]
     */
    public function getChildren(): array;
    public function clear(): void;
    public function isNullNode(): bool;
}
