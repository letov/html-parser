<?php

namespace Letov\HtmlParser\StateMachine;

use Letov\HtmlParser\Node\Attr\AttrInterface;
use Letov\HtmlParser\Node\NodeInterface;

class StateMachine implements StateMachineInterface
{
    private const SYMBOL_OPEN = '<';
    private const SYMBOL_CLOSE = '>';
    private const SYMBOL_WHITESPACE = ' ';
    private const SYMBOL_SLASH = '/';
    private const SYMBOL_EQUAL = '=';
    private const SYMBOL_QUOTE = '"';

    private ?string $parentNodeName;
    private string $symbol;

    private State $state;
    private StateMachineInterface $subStateMachine;
    private AttrInterface $attr;

    private NodeInterface $node;

    private ?string $body;
    private ?string $nodeName;
    /** @var AttrInterface[] $attrs */
    private array $attrs;
    private string $attrName;
    private ?string $attrValue;
    /** @var NodeInterface[] $children */
    private array $children;

    public function __construct(
        NodeInterface $node,
        AttrInterface $attr
    )
    {
        $this->node = $node;
        $this->attr = $attr;
        $this->clear();
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function clear(): void
    {
        $this->clearEntities();

        $this->parentNodeName = null;
        $this->symbol = '';
        $this->state = State::TEXT;
    }

    private function clearEntities(): void
    {
        $this->node->clear();
        $this->attr->clear();

        $this->body = null;
        $this->nodeName = null;
        $this->attrs = [];
        $this->attrName = '';
        $this->attrValue = null;
        $this->children = [];
    }

    public function setParentNodeName(?string $parentNodeName): void
    {
        $this->parentNodeName = $parentNodeName;
    }

    public function step(string $symbol): ?NodeInterface
    {
        $this->symbol = $symbol;
        return match($this->state) {
            State::TEXT => $this->processStateText(),
            State::TAG_OPEN_BEGIN => $this->processStateTagOpenBegin(),
            State::TAG_OPEN_NAME => $this->processStateTagOpenName(),
            State::TAG_OPEN_SINGLE => $this->processStateTagOpenSingle(),
            State::TAG_OPEN_INSIDE => $this->processStateTagOpenInside(),
            State::ATTR =>  $this->processStateAttr(),
            State::ATTR_NAME => $this->processStateAttrName(),
            State::ATTR_VALUE => $this->processStateAttrValue(),
            State::ATTR_VALUE_NO_QUOTES => $this->processStateAttrValueNoQuotes(),
            State::ATTR_VALUE_QUOTES => $this->processStateAttrValueQuotes(),
            State::TAG_CLOSE_NAME => $this->processStateTagCloseName(),
            State::DONE => null,
        };
    }

    private function transitionStateText(): void
    {
        $this->state = State::TEXT;
    }

    private function processStateText(): ?NodeInterface
    {
        if (self::SYMBOL_OPEN === $this->symbol) {
            $this->transitionStateTagOpenBegin();
            return $this->saveNode();
        }

        $this->body .= $this->symbol;
        return null;
    }

    private function saveNode(): ?NodeInterface
    {
        $this->node->setName($this->nodeName);
        $this->node->setAttrs($this->attrs);
        $this->checkOnlyTextChild();
        $this->node->setBody($this->body);
        $this->node->setChildren($this->children);
        $node = clone $this->node;
        $this->clearEntities();
        return $node->isNullNode() ? null : $node;
    }

    private function checkOnlyTextChild(): void
    {
        if (
            1 === count($this->children)
            && is_null($this->children[0]->getName())
        ) {
            $this->body = $this->children[0]->getBody();
            $this->children = [];
        }
    }

    private function transitionStateTagOpenBegin(): void
    {
        $this->state = State::TAG_OPEN_BEGIN;
    }

    private function processStateTagOpenBegin(): ?NodeInterface
    {
        if (self::SYMBOL_SLASH === $this->symbol) {
            $this->transitionStateTagCloseName();
        } else {
            $this->transitionStateTagOpenName();
        }
        return null;
    }

    private function transitionStateTagOpenName(): void
    {
        $this->nodeName = $this->symbol;
        $this->state = State::TAG_OPEN_NAME;
    }

    private function processStateTagOpenName(): ?NodeInterface
    {
        switch ($this->symbol) {
            case self::SYMBOL_WHITESPACE:
                $this->attrs = [];
                $this->transitionStateAttr();
                break;
            case self::SYMBOL_SLASH:
                $this->transitionStateTagOpenSingle();
                break;
            case self::SYMBOL_CLOSE:
                $this->transitionStateTagOpenInside();
                break;
            default:
                $this->nodeName .= $this->symbol;
                break;
        }
        return null;
    }

    private function transitionStateTagOpenSingle(): void
    {
        $this->state = State::TAG_OPEN_SINGLE;
    }

    private function processStateTagOpenSingle(): ?NodeInterface
    {
        $this->transitionStateText();
        return $this->saveNode();
    }

    private function transitionStateTagOpenInside(): void
    {
        $node = clone $this->node;
        $attr = clone $this->attr;
        $this->subStateMachine = new StateMachine($node, $attr);
        $this->subStateMachine->setParentNodeName($this->nodeName);
        $this->state = State::TAG_OPEN_INSIDE;
    }

    private function processStateTagOpenInside(): ?NodeInterface
    {
        $subNode = $this->subStateMachine->step($this->symbol);
        if (!is_null($subNode)) {
            $this->children[] = $subNode;
        }
        if (State::DONE === $this->subStateMachine->getState()) {
            $this->transitionStateText();
            return $this->saveNode();
        }
        return null;
    }

    private function transitionStateAttr(): void
    {
        $this->state = State::ATTR;
    }

    private function processStateAttr(): ?NodeInterface
    {
        $this->saveAttr();
        switch ($this->symbol) {
            case self::SYMBOL_SLASH:
                $this->transitionStateTagOpenSingle();
                break;
            case self::SYMBOL_CLOSE:
                $this->transitionStateTagOpenInside();
                break;
            case self::SYMBOL_WHITESPACE:
                break;
            default:
                $this->transitionStateAttrName();
                break;
        }
        return null;
    }

    private function saveAttr(): void
    {
        if ('' !== $this->attrName) {
            $this->attr->clear();
            $this->attr->setName($this->attrName);
            if ('' !== $this->attrValue) {
                $this->attr->setValue(trim($this->attrValue, '"'));
            }
            $this->attrs[] = clone $this->attr;
            $this->attrName = '';
        }
    }

    private function transitionStateAttrName(): void
    {
        $this->attrName = $this->symbol;
        $this->attrValue = '';
        $this->state = State::ATTR_NAME;
    }

    private function processStateAttrName(): ?NodeInterface
    {
        switch ($this->symbol) {
            case self::SYMBOL_SLASH:
                $this->saveAttr();
                $this->transitionStateTagOpenSingle();
                break;
            case self::SYMBOL_CLOSE:
                $this->saveAttr();
                $this->transitionStateTagOpenInside();
                break;
            case self::SYMBOL_EQUAL:
                $this->transitionStateAttrValue();
                break;
            case self::SYMBOL_WHITESPACE:
                $this->transitionStateAttr();
                break;
            default:
                $this->attrName .= $this->symbol;
                break;
        }
        return null;
    }

    private function transitionStateAttrValue(): void
    {
        $this->state = State::ATTR_VALUE;
    }

    private function processStateAttrValue(): ?NodeInterface
    {
        if (self::SYMBOL_QUOTE === $this->symbol) {
            $this->transitionStateAttrValueQuotes();
        } else {
            $this->attrValue .= $this->symbol;
            $this->transitionStateAttrValueNoQuotes();
        }
        return null;
    }

    private function transitionStateAttrValueNoQuotes(): void
    {
        $this->state = State::ATTR_VALUE_NO_QUOTES;
    }

    private function processStateAttrValueNoQuotes(): ?NodeInterface
    {
        switch ($this->symbol) {
            case self::SYMBOL_SLASH:
                $this->saveAttr();
                $this->transitionStateTagOpenSingle();
                break;
            case self::SYMBOL_CLOSE:
                $this->saveAttr();
                $this->transitionStateTagOpenInside();
                break;
            case self::SYMBOL_WHITESPACE:
                $this->transitionStateAttr();
                break;
            default:
                $this->attrValue .= $this->symbol;
                break;
        }
        return null;
    }

    private function transitionStateAttrValueQuotes(): void
    {
        $this->state = State::ATTR_VALUE_QUOTES;
    }

    private function processStateAttrValueQuotes(): ?NodeInterface
    {
        if (self::SYMBOL_QUOTE === $this->symbol) {
            $this->transitionStateAttr();
        } else {
            $this->attrValue .= $this->symbol;
        }
        return null;
    }

    private function transitionStateTagCloseName(): void
    {
        $this->nodeName = '';
        $this->state = State::TAG_CLOSE_NAME;
    }

    private function processStateTagCloseName(): ?NodeInterface
    {
        switch ($this->symbol) {
            case self::SYMBOL_CLOSE:
                if ($this->nodeName === $this->parentNodeName) {
                    $this->nodeName = null;
                    $this->transitionStateDone();
                    return $this->saveNode();
                }
                $this->transitionStateText();
                break;
            default:
                $this->nodeName .= $this->symbol;
                break;
        }
        return null;
    }

    private function transitionStateDone(): void
    {
        $this->state = State::DONE;
    }
}
