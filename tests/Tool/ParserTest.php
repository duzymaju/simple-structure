<?php

use PHPUnit\Framework\TestCase;
use SimpleStructure\Tool\Parser;

final class ParserTest extends TestCase
{
    /**
     * Test slug parsing
     */
    public function testSlugParsing()
    {
        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6'), 's-geta9-236');

        $this->assertEquals('s-geta9-236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '-'));
        $this->assertEquals('s_geta9_236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '_'));
        $this->assertEquals('sgeta9236', Parser::parseSlug('s GętĄ9 2[3]"\'6', ''));

        $this->assertEquals('s-GetA9-236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '-', false));
        $this->assertEquals('s_GetA9_236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '_', false));
        $this->assertEquals('sGetA9236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '', false));
    }
}
