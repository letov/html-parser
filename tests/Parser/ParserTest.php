<?php

namespace Letov\HtmlParser\Tests\Parser;

use Letov\HtmlParser\Parser\ParserInterface;
use Letov\HtmlParser\Tests\TestCaseContainer;

class ParserTest extends TestCaseContainer
{
    public const FILE0 = '../../storage/test0.html';
    public const FILE1 = '../../storage/test1.html';

    public function test0(): void
    {
        $parser = $this->container->get(ParserInterface::class);
        $dom = $parser->parse(self::FILE0);
        $attrs = $dom[0]->getChildren()[0]->getAttrs();
        $this->assertCount(2, $attrs);
        $this->assertSame('value', $attrs[0]->getValue());
        $this->assertSame('attr2', $attrs[1]->getName());
    }

    /*
    <a>
        text0
        <b attr />
        <c>               2
            <d>           0
                find-it
            </d>
        </c>
    </a>
     */
    public function test1(): void
    {
        $parser = $this->container->get(ParserInterface::class);
        $dom = $parser->parse(self::FILE1);
        $d = $dom[0]->getChildren()[2]->getChildren()[0];
        $this->assertSame('d', $d->getName());
        $this->assertSame('find-it', $d->getBody());
    }
}
