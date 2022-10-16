<?php

namespace Letov\HtmlParser\Node\Attr;

class Attr implements AttrInterface
{

    public string $name;
    public ?string $value;

    public function __construct()
    {
        $this->clear();
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function clear(): void
    {
        $this->name = '';
        $this->value = null;
    }
}
