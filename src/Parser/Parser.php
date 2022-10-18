<?php

namespace Letov\HtmlParser\Parser;

use Letov\HtmlParser\Node\NodeInterface;
use Letov\HtmlParser\StateMachine\StateMachineInterface;

use function PHPUnit\Framework\isEmpty;

class Parser implements ParserInterface
{
    private StateMachineInterface $stateMachine;
    /** @var NodeInterface[] $dom */
    private array $dom;
    private array $summaryTags;

    public function __construct(StateMachineInterface $stateMachine)
    {
        $this->stateMachine = $stateMachine;
        $this->dom = [];
        $this->summaryTags = [];
    }

    public function parse(string $filePath): void
    {
        $html = file_get_contents($filePath);
        $symbols = mb_str_split($html);
        $this->dom = [];
        foreach ($symbols as $key => $symbol) {
            $node = $this->stateMachine->step($symbol);
            if (null !== $node) {
                $this->dom[] = $node;
            }
        }
    }

    /**
     * @return NodeInterface[]
     */
    public function getDom(): array
    {
        return $this->dom;
    }

    public function calcTags(): array
    {
        $this->summaryTags = [];
        $this->_calcTags($this->dom);
        return $this->summaryTags;
    }

    /**
     * @param NodeInterface[] $nodes
     */
    private function _calcTags(array $nodes): void
    {
        if (empty($nodes)) {
            return;
        }
        foreach ($nodes as $node) {
            $name = $node->getName();
            if (!is_null($name)) {
                if (isset($this->summaryTags[$name])) {
                    $this->summaryTags[$name]++;
                } else {
                    $this->summaryTags[$name] = 1;
                }
            }
            $this->_calcTags($node->getChildren());
        }
    }
}
