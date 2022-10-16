<?php

namespace Letov\HtmlParser\Node\Attr;

interface AttrInterface
{
    public function setName(string $name): void;
    public function getName(): string;
    public function setValue(?string $value): void;
    public function getValue(): ?string;
    public function clear(): void;
}
