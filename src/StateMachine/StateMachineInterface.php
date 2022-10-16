<?php

namespace Letov\HtmlParser\StateMachine;

use Letov\HtmlParser\Node\NodeInterface;

interface StateMachineInterface
{
    public function step(string $symbol): ?NodeInterface;
    public function getState(): State;
    public function clear(): void;
}
