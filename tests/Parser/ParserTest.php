<?php

namespace Letov\HtmlParser\Tests\Parser;

use Letov\HtmlParser\Parser\ParserInterface;
use Letov\HtmlParser\Tests\TestCaseContainer;

class ParserTest extends TestCaseContainer
{
    public const FILE_0 = '../../storage/test0.html';
    public const FILE_1 = '../../storage/test1.html';
    public const FILE_2 = '../../storage/test2.html';

    /*
     <a attr1="http://asd" attr3>
       <tag attr="value" attr2/>
     </a>
     */
    public function test0(): void
    {
        $parser = $this->container->get(ParserInterface::class);
        $parser->parse(self::FILE_0);
        $dom = $parser->getDom();
        $attrs = $dom[0]->getChildren()[0]->getAttrs();
        $this->assertCount(2, $attrs);
        $this->assertSame('value', $attrs[0]->getValue());
        $this->assertSame('attr2', $attrs[1]->getName());
    }

    /*
     <a>
        text0
        <b attr/>
        <c>
            <d>
                find-it
            </d>
        </c>
     </a>
     */
    public function test1(): void
    {
        $parser = $this->container->get(ParserInterface::class);
        $parser->parse(self::FILE_1);
        $dom = $parser->getDom();
        $d = $dom[0]->getChildren()[2]->getChildren()[0];
        $this->assertSame('d', $d->getName());
        $this->assertSame('find-it', $d->getBody());
    }

    public function test2(): void
    {
        $parser = $this->container->get(ParserInterface::class);
        $parser->parse(self::FILE_2);
        $tags = $parser->calcTags();
        $this->assertSame(6, $tags['div']);
    }
}
