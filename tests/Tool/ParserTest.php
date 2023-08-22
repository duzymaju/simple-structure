<?php

use PHPUnit\Framework\TestCase;
use SimpleStructure\Tool\Parser;

final class ParserTest extends TestCase
{
    /**
     * Test bool parsing
     */
    public function testBoolParsing()
    {
        $this->assertEquals(true, Parser::parseBool('true'));
        $this->assertEquals(true, Parser::parseBool('TRue'));
        $this->assertEquals(true, Parser::parseBool('1'));
        $this->assertEquals(true, Parser::parseBool(1));
        $this->assertEquals(true, Parser::parseBool('abc'));
        $this->assertEquals(true, Parser::parseBool('-'));
        $this->assertEquals(true, Parser::parseBool(-0.1));

        $this->assertEquals(false, Parser::parseBool('false'));
        $this->assertEquals(false, Parser::parseBool('faLSE'));
        $this->assertEquals(false, Parser::parseBool(false));
        $this->assertEquals(false, Parser::parseBool('null'));
        $this->assertEquals(false, Parser::parseBool('NULL'));
        $this->assertEquals(false, Parser::parseBool(null));
        $this->assertEquals(false, Parser::parseBool('0'));
        $this->assertEquals(false, Parser::parseBool(0));
        $this->assertEquals(false, Parser::parseBool(0.0));
    }

    /**
     * Test slug parsing
     */
    public function testSlugParsing()
    {
        $this->assertEquals('s-geta9-236', Parser::parseSlug('s GętĄ9 2[3]"\'6'));

        $this->assertEquals('s-geta9-236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '-'));
        $this->assertEquals('s_geta9_236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '_'));
        $this->assertEquals('sgeta9236', Parser::parseSlug('s GętĄ9 2[3]"\'6', ''));

        $this->assertEquals('s-GetA9-236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '-', false));
        $this->assertEquals('s_GetA9_236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '_', false));
        $this->assertEquals('sGetA9236', Parser::parseSlug('s GętĄ9 2[3]"\'6', '', false));
    }
}
