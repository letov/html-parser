<?php

namespace Letov\HtmlParser\Parser;

use Letov\HtmlParser\Node\NodeInterface;

interface ParserInterface
{
    /**
     * @param string $filePath
     * @return NodeInterface[]
     */
    public function parse(string $filePath): array;
}
