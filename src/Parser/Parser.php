<?php

namespace Letov\HtmlParser\Parser;

use Letov\HtmlParser\Node\NodeInterface;
use Letov\HtmlParser\StateMachine\StateMachineInterface;

class Parser implements ParserInterface
{
    public StateMachineInterface $stateMachine;

    public function __construct(StateMachineInterface $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * @param string $filePath
     * @return NodeInterface[]
     */
    public function parse(string $filePath): array
    {
        $html = file_get_contents($filePath);
        $symbols = mb_str_split($html);
        $result = [];
        foreach ($symbols as $key => $symbol) {
            $node = $this->stateMachine->step($symbol);
            if (null !== $node) {
                $result[] = $node;
            }
        }
        return $result;
    }
}
