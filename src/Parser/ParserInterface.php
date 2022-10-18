<?php

namespace Letov\HtmlParser\Parser;

use Letov\HtmlParser\Node\NodeInterface;

interface ParserInterface
{
    public function parse(string $filePath): void;
    /**
     * @return NodeInterface[]
     */
    public function getDom(): array;
    public function calcTags(): array;
}
