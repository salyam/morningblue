<?php

namespace Salyam\MorningBlue;

/**
 * Class BBCodeParser
 * @package Salyam\MorningBlue
 */
final class BBCode
{
    /** Parses a raw string an replaces supported BBCode tags with corresponding HTML tags.
     *
     * @param string $value The original string to be parsed.
     * @param bool $removeOriginalHtmlTags If true, all HTML tags will be escaped in the original string, before parsing the BBCode tags.
     * @return string The parsed string.
     */
    public function ToHtml(string $value, bool $removeOriginalHtmlTags = true) : string
    {
        if($removeOriginalHtmlTags)
        {
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
        }

        foreach ($this->SimpleParserRule as $parserRuleName => $parserRule)
        {
            $pattern = '/' . $parserRule['pattern'] . '/sU';
            $replace = $parserRule['replace'];
            while(preg_match($pattern, $value) === 1)
            {
                $value = preg_replace($pattern, $replace, $value);
            }
        }

        foreach ($this->ComplexParserRules as $parserRuleName => $parserRule)
        {
            $pattern = '/' . $parserRule['pattern'] . '/sU';
            $callback = $parserRule['callback'];
            while(preg_match($pattern, $value) === 1)
            {
                $value = preg_replace_callback($pattern, $callback, $value);
            }
        }

        return $value;
    }

    /** Adds a BBCode parser which supports the Prism syntax highlighter library. */
    public function AddPrismSupport()
    {
        $this->AddSimpleParserRule(
            'prism',
            '\[code language=(.*)\](.*)\[\/code\]',
            '<pre><code class="language-$1">$2</code></pre>');
    }

    /** Adds a 'BBCode' parser which converts every linebreaks to br HTML tags. */
    public function AddLineBreakSupport()
    {
        $this->AddSimpleParserRule(
            'linebreak',
            "(\r\n|\r|\n)",
            '<br/>');
    }

    /** Adds a new simple parser rule to a BBCode parser.
     *
     * If a rule already exists with the given name, it will be overridden.
     *
     * @param string $name The name of the parser rule to be added.
     * @param string $pattern A regex pattern which will be used when parsing the newly added parser rule.
     * @param string $replace The string to which the original pattern will be replaced.
     */
    public function AddSimpleParserRule(string $name, string $pattern, string $replace)
    {
        $this->SimpleParserRule[$name] =
            [
                'pattern' => $pattern,
                'replace' => $replace
            ];
    }

    /** Adds a new complex parser rule to a BBCode parser.
     *
     * If a rule already exists with the given name, it will be overridden.
     *
     * @param string $name The name of the parser rule to be added.
     * @param string $pattern A regex pattern which will be used when parsing the newly added parser rule.
     * @param callable $callback The callback function which will create the replacement string to a regex match.
     */
    public function AddComplexParserRule(string $name, string $pattern, callable $callback)
    {
        $this->ComplexParserRules[$name] =
            [
                'pattern' => $pattern,
                'callback' => $callback
            ];
    }

    /**
     * @var array The regex pattern-replacer pairs which will be used to convert BBCode to HTML with preg_replace.
     *
     * This array must contain elements that are themselves arrays. Every element must have a 'pattern' and a 'replace'
     * key, where 'pattern' is a regex expression which will be replaced with the value of 'replace'.
     * If 'pattern' contains a regex capture group, the captured string can be included in 'replace'. The capture groups
     * can be reference by an index starting with '1'. The indexes should be preceded with a '$' character.
     *
     * Example: assuming the following pattern: \[ExampleTag\](.*)\[\/ExampleTag\]
     *   In this case, a string written between [ExampleTag] and [/ExampleTag] will be captured, and the captured string
     *   will have an index of '1'.
     *   The captured value can be referenced in the replace string with the following expression: '$1'.
     */
    private $SimpleParserRule =
        [
            // Text formatting
            'bold' =>
                [
                    'pattern' => '\[b\](.*)\[\/b\]',
                    'replace' => '<b>$1</b>'
                ],
            'italic' =>
                [
                    'pattern' => '\[i\](.*)\[\/i\]',
                    'replace' => '<i>$1</i>'
                ],
            'underline' =>
                [
                    'pattern' => '\[u\](.*)\[\/u\]',
                    'replace' => '<u>$1</u>'
                ],
            'strikethrough' =>
                [
                    'pattern' => '\[s\](.*)\[\/s\]',
                    'replace' => '<s>$1</s>'
                ],
            'subscript' =>
                [
                    'pattern' => '\[sub\](.*)\[\/sub\]',
                    'replace' => '<sub>$1</sub>'
                ],
            'superscript' =>
                [
                    'pattern' => '\[sup\](.*)\[\/sup\]',
                    'replace' => '<sup>$1</sup>'
                ],

            // Images

            'image' =>
                [
                    'pattern' => '\[img\](.*)\[\/img\]',
                    'replace' => '<img src="$1">'
                ],
            'image-resized' =>
                [
                    'pattern' => '\[img width=(.*) height=(.*)\](.*)\[\/img\]',
                    'replace' => '<img src="$3" style="width: $1px; height: $2px;">'
                ],
            'image-resized-alt-1' =>
                [
                    'pattern' => '\[img height=(.*) width=(.*)\](.*)\[\/img\]',
                    'replace' => '<img src="$3" style="width: $2px; height: $1px;">'
                ],
            'image-resized-alt-2' =>
                [
                    'pattern' => '\[img=(.*)x(.*)\](.*)\[\/img\]',
                    'replace' => '<img src="$3" style="width: $1px; height: $2px;">'
                ],

            // URLs:

            'url-without-text' =>
                [
                    'pattern' => '\[url\](.*)\[\/url\]',
                    'replace' => '<a href="$1">$1</a>'
                ],
            'url-with-text' =>
                [
                    'pattern' => '\[url=(.*)\](.*)\[\/url\]',
                    'replace' => '<a href="$1">$2</a>'
                ],

            // Lists:

            'ordered-list' =>
                [
                    'pattern' => '\[ol\](.*)\[\/ol\]',
                    'replace' => '<ol>$1</ol>'
                ],
            'unordered-list' =>
                [
                    'pattern' => '\[ul\](.*)\[\/ul\]',
                    'replace' => '<ul>$1</ul>'
                ],
            'unordered-list-alt' =>
                [
                    'pattern' => '\[list\](.*)\[\/list\]',
                    'replace' => '<ul>$1</ul>'
                ],
            'list-item' =>
                [
                    'pattern' => '\[li\](.*)\[\/li\]',
                    'replace' => '<li>$1</li>'
                ],
            'list-item-alt' =>
                [
                    'pattern' => "\[\*\](.*)\n",
                    'replace' => '<li>$1</li>'
                ],

            // Youtube

            'youtube' =>
                [
                    'pattern' => '\[youtube\](.*)\[\/youtube\]',
                    'replace' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ],
            'youtube-resized' =>
                [
                    'pattern' => '\[youtube width=(.*) height=(.*)\](.*)\[\/youtube\]',
                    'replace' => '<iframe width="$1" height="$2" src="https://www.youtube.com/embed/$3" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ],
            'youtube-resized-alt-1' =>
                [
                    'pattern' => '\[youtube height=(.*) width=(.*)\](.*)\[\/youtube\]',
                    'replace' => '<iframe width="$2" height="$1" src="https://www.youtube.com/embed/$3" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ],
            'youtube-resized-alt-2' =>
                [
                    'pattern' => '\[youtube=(.*)x(.*)\](.*)\[\/youtube\]',
                    'replace' => '<iframe width="$1" height="$2" src="https://www.youtube.com/embed/$3" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ],

            // Other

            'code' =>
                [
                    'pattern' => '\[code\](.*)\[\/code\]',
                    'replace' => '<pre><code>$1</code></pre>'
                ],
    ];

    /**
     * @var array The regex pattern-replacer pairs which will be used to convert BBCode to HTML with preg_replace_callback.
     *
     * This array must contain elements that are themselves arrays. Every element must have a 'pattern' and a 'callback'
     * key, where 'pattern' is a regex expression which will be replaced with the return value of 'callback'.
     */
    private $ComplexParserRules =
        [

        ];
}