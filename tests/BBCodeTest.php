<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TextFormattingTest extends TestCase
{
    public function testCanParseSimpleTextFormatting()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<b>This text should be bold.</b>', $parser->ToHtml('[b]This text should be bold.[/b]'));
        $this->assertEquals('<i>This text should be italic.</i>', $parser->ToHtml('[i]This text should be italic.[/i]'));
        $this->assertEquals('<u>This text should be underline.</u>', $parser->ToHtml('[u]This text should be underline.[/u]'));
        $this->assertEquals('<s>This text should be strikethrough.</s>', $parser->ToHtml('[s]This text should be strikethrough.[/s]'));
        $this->assertEquals('<sub>This text should be subscript.</sub>', $parser->ToHtml('[sub]This text should be subscript.[/sub]'));
        $this->assertEquals('<sup>This text should be superscript.</sup>', $parser->ToHtml('[sup]This text should be superscript.[/sup]'));
    }
}

final class ImageFormattingTest extends TestCase
{
    public function testCanParseImageTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<img src="www.test.tst/test.png">', $parser->ToHtml('[img]www.test.tst/test.png[/img]'));
        $this->assertEquals('<img src="www.test.tst/test.png" style="width: 20px; height: 30px;">', $parser->ToHtml('[img width=20 height=30]www.test.tst/test.png[/img]'));
        $this->assertEquals('<img src="www.test.tst/test.png" style="width: 20px; height: 30px;">', $parser->ToHtml('[img height=30 width=20]www.test.tst/test.png[/img]'));
        $this->assertEquals('<img src="www.test.tst/test.png" style="width: 20px; height: 30px;">', $parser->ToHtml('[img=20x30]www.test.tst/test.png[/img]'));
    }
}

final class URLFormattingTest extends TestCase
{
    public function testCanParseURLTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<a href="www.test.tst">www.test.tst</a>', $parser->ToHtml('[url]www.test.tst[/url]'));
        $this->assertEquals('<a href="www.test.tst">Test Test</a>', $parser->ToHtml('[url=www.test.tst]Test Test[/url]'));
    }
}

final class ListFormattingTest extends TestCase
{
    public function testCanParseUnorderedListTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<ul><li>item1</li><li>item2</li></ul>', $parser->ToHtml('[ul][li]item1[/li][li]item2[/li][/ul]'));
        $this->assertEquals('<ul><li>item1</li><li>item2</li><ul><li>inner item1</li><li>inner item2</li></ul></ul>', $parser->ToHtml('[ul][li]item1[/li][li]item2[/li][ul][li]inner item1[/li][li]inner item2[/li][/ul][/ul]'));
    }

    public function testCanParseOrderedListTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<ol><li>item1</li><li>item2</li></ol>', $parser->ToHtml('[ol][li]item1[/li][li]item2[/li][/ol]'));
        $this->assertEquals('<ol><li>item1</li><li>item2</li><ol><li>inner item1</li><li>inner item2</li></ol></ol>', $parser->ToHtml('[ol][li]item1[/li][li]item2[/li][ol][li]inner item1[/li][li]inner item2[/li][/ol][/ol]'));
    }
}

final class LineBreakFormattingTest extends TestCase
{
    public function testCanParseLineBreaks()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals("\n\n", $parser->ToHtml("\n\n"));
        $parser->AddLineBreakSupport();
        $this->assertEquals("<br/><br/>", $parser->ToHtml("\n\n"));
        $this->assertEquals("<br/><br/><br/><br/>", $parser->ToHtml("\n\n

"));
    }
}

final class MixedTests extends TestCase
{
    public function testHtmlTagRemove()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('&lt;i&gt;This text should be italic.&lt;/i&gt;', $parser->ToHtml('<i>This text should be italic.</i>'));
        $this->assertEquals('<i>This text should be italic.</i>', $parser->ToHtml('<i>This text should be italic.</i>', false));
    }

    public function testComplexRules()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $parser->AddComplexParserRule('complex-rule-1', '\[gallery\](.*)\[\/gallery\]', function(array $matches){
            return $matches[1];
        });
        $this->assertEquals('My awesome gallery', $parser->ToHtml('[gallery]My awesome gallery[/gallery]'));
    }

    public function testCanParseNestedTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<b>This text will be bold. <i>This text will be italic and bold.</i></b>', $parser->ToHtml('[b]This text will be bold. [i]This text will be italic and bold.[/i][/b]'));
    }

    public function testCanParseYoutubeTags()
    {
        $parser = new \Salyam\MorningBlue\BBCode();
        $this->assertEquals('<iframe width="560" height="315" src="https://www.youtube.com/embed/Jo_-KoBiBG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $parser->ToHtml('[youtube]Jo_-KoBiBG0[/youtube]'));
        $this->assertEquals('<iframe width="800" height="450" src="https://www.youtube.com/embed/Jo_-KoBiBG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $parser->ToHtml('[youtube width=800 height=450]Jo_-KoBiBG0[/youtube]'));
        $this->assertEquals('<iframe width="800" height="450" src="https://www.youtube.com/embed/Jo_-KoBiBG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $parser->ToHtml('[youtube height=450 width=800]Jo_-KoBiBG0[/youtube]'));
        $this->assertEquals('<iframe width="800" height="450" src="https://www.youtube.com/embed/Jo_-KoBiBG0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>', $parser->ToHtml('[youtube=800*450]Jo_-KoBiBG0[/youtube]'));
    }
}