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

        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', '-'), 's-geta9-236');
        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', '_'), 's_geta9_236');
        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', ''), 'sgeta9236');

        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', '-', false), 's-GetA9-236');
        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', '_', false), 's_GetA9_236');
        $this->assertEquals(Parser::parseSlug('s GętĄ9 2[3]"\'6', '', false), 'sGetA9236');
    }
}
