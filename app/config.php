<?php

use Letov\HtmlParser\Node\Attr\Attr;
use Letov\HtmlParser\Node\Attr\AttrInterface;
use Letov\HtmlParser\Node\Node;
use Letov\HtmlParser\Node\NodeInterface;
use Letov\HtmlParser\Parser\Parser;
use Letov\HtmlParser\Parser\ParserInterface;
use Letov\HtmlParser\StateMachine\StateMachine;
use Letov\HtmlParser\StateMachine\StateMachineInterface;

return [
    ParserInterface::class => DI\get(Parser::class),
    StateMachineInterface::class => DI\get(StateMachine::class),
    NodeInterface::class => DI\get(Node::class),
    AttrInterface::class => DI\get(Attr::class),
];
