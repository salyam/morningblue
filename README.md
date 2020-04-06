# MorningBlue #

MorningBlue is a BBCode parsing library which I use in my other projects.

# Table of Contents #

 1. [MorningBlue](#morningblue)
 2. [Installation](#installation)
 3. [Usage](#usage)
 4. [Supported BBCode tags](#supported-bbcode-tags)
 5. [Adding custom BBCode tags](#adding-custom-bbcode-tags)

# Installation #

MorningBlue was tested under PHP 7.2, but it should work under older versions of PHP as well.

## Install via Composer ##

To install MorningBlue, use the following command:
```
composer require salyam/morningblue
```

## Install manually ##

To install MorningBlue manually, simply download src/BBCode.php and include it with 
```php
include_once "path_to_BBCode.php";
```

## Install under Laravel ##

MorningBlue contains a Laravel Service Provider which registers a global BBCode parser object.
This global object can be used in other ServiceProviders to register own BBCode tags.
Composer should automatically install MorningBlue under Laravel, if it does not work check if Salyam\MorningBlue\BBCodeServiceProvider was added to config/app.php.

# Usage #

MorningBlue has only one purpose: parsing BBCode strings and returning a HTML code which can be rendered in the browser.

MorningBlue BBCode parser can be used with the following code after installation:

```php
$parser = new \Salyam\MorningBlue\BBCode();
echo $parser->ToHtml("[b]This text will be bold. [i]This text will be italic and bold.[/i][/b]");
```

The output of the previous code is the following:
```HTML
<b>This text will be bold. <i>This text will be italic and bold.</i></b>
```

By default, MorningBlue will escapes any HTML entities (`<, >, ', ", & `).
This encoding can be disabled by passing a `false` value to the `toHtml` method of a BBCode parser object:
```php
$parser = new \Salyam\MorningBlue\BBCode();
echo $parser->ToHtml("<p>This HTML tag will remain here.</p>", false);
```

## Laravel usage ##

If MorningBlue was successfully installed under Laravel, it is possible to use a globally registered BBCode parser object:

```php
use \Salyam\MorningBlue\BBCode;

// ...

app(BBCode::class)->ToHtml('[b]This text will be bold.[/b]');
```

It is possible to register custom BBCode tags even in the boot function of other Service Providers:
```php
use \Salyam\MorningBlue\BBCode;
use Illuminate\Support\ServiceProvider;

class ExampleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app(BBcode::class)->AddPrismSupport();
        
        // can use also app(BBcode::class)->AddSimpleParserRule();
    }
}
```

MorningBlue also provides a Blade directive, which uses the global BBCode parser object provided by BBCodeServiceProvider.

```blade
  @BBCode("[b]This text will be bold[/b]")
  @BBCode($article->content)
```

# Supported BBCode tags #

## Text formatting ##
    
  * Bold:

        [b] Text [/b]
  * Italic:

        [i] Text [/i]
  * Underlined:

        [u] Text [/u]
  * Strikethrough:
  
        [s] Text [/s]
  * Subscript:

        [sub] Text [/sub]
  * Superscript:

        [sup] Text [/sup]
  * Headings:
  
        [h1]Test[/h1]
        [h2]Test[/h2]
        [h3]Test[/h3]
        [h4]Test[/h4]
        [h5]Test[/h5]
        [h6]Test[/h6]
        
## Images ##

  * Render an image:

        [img]url-to-image[/img]
  * Render an image and specify its width and height:

        [img width=100 height=100]url-to-image[/img]
        [img height=100 width=100]url-to-image[/img]
        [img=100x100]url-to-image[/img]
        
## URLs ##

  * Create a link:

        [url]www.github.com/salyam[/url]
  * Create a link with custom text:

        [url=www.github.com/salyam]Visit my GitHub page[/url]
## Lists ##
  * Unordered lists:

         [ul]
           [li]Item1[/li]
           [li]Item2[/li]
         [/ul]
    or
   
         [list]
           [*]Item1
           [*]Item2
         [/list]
         
  * Ordered lists:

         [ol]
           [li]Item1[/li]
           [li]Item2[/li]
         [/ol]
    or
  
         [ol]
           [*]Item1
           [*]Item2
         [/ol]

## YouTube Videos ##

  * Include a YouTube video:
  
          [youtube]video-id[/youtube]
  * Include a YouTube video and specify its width and height:
  
          [youtube width=100 height=100]video-id[/youtube]
          [youtube height=100 width=100]video-id[/youtube]
          [youtube=100x100]video-id[/youtube]

## Source codes and syntax highlighting ##

  * Preformatted code:

        [code]<php echo "Hello World"; ?>[/code]
MorningBlue supports the Prism syntax highlighter JavaScript library, but it is disabled by default.
 
  * The following code snippet will enable Prism support:

       ```php
       $parser->AddPrismSupport()
       ```
  * After enabling Prism support, the following BBCode tag will be available:
      
         [code lang=php] <?php echo 'Hello world'; ?> [/code]
         
    The lang parameter can be any of the supported languages of Prism.

# Adding custom BBCode tags #

It is possible to add custom BBCode tags or overwrite existing ones.
There are two types of BBCode tag parser rules supported: simple and complex.
Simple in this case means that MorningBlue will use regex to find a BBCode tag and replace it with a constant string (This constant string can contain references to capture groups in the original regex.).

Complex parser rule means that the BBCode tags will be found with a regex expression, and the replacement string will be created by a callback function.

  * Adding simple parser rules
    * A simple parser rule consists of 3 things: a *name*, a regex *pattern*, and a *replacement* string.
    * *name* of a parser rule allows MorningBlue to delete or override the same parser rule.
    * *pattern* is a regex expression, which will be used to find the BBCode tag.
      It can contain regex capture groups and the capture groups can be referenced in the *replacement* string.
      Every occurrence of *pattern* will be replaced with *replacement*.
    * *replacement* is a string which will be used to replace *pattern*.
      The regex capture groups from *pattern* can be used in *replacement* in the following form: `$n` where `n` is the index of the capture group.
      The first capture group will have index 1, the second will have index 2, and so on.
    * Example 1:
        ```php
        $parser = new \Salyam\MorningBlue\BBCode();
        $parser->AddSimpleParserRule(
          'my-own-simple-parser',
          '\[MyBBCodeTag param=(.*)\](.*)\[\/MyBBCodeTag\]',
          'The content of the tag is $2, the parameter is $1.'
        );
        ```
    * Example 2:
        ```php
        $parser = new \Salyam\MorningBlue\BBCode();
        $parser->AddSimpleParserRule(
          'prism',
           '\[code language=(.*)\](.*)\[\/code\]',
           '<pre><code class="language-$1">$2</code></pre>'
         );
        ```
  * Adding a complex parser rule
    * A complex parser rule is similar to a simple one, it only differs in the *replacement* part: instead of a constant string, the replacement string is being constructed by a callable.
    * A complex parser rule consists of 3 things: a *name*, a regex *pattern*, and a *callback* string.
    * *name* is the same as the *name* of a simple parser rule.
    * *pattern* is the same as the *pattern* of a simple parser rule.
    * *callback* is a callable which creates the *replacement* string.
      *callback* receives an array, which contains the data of a regex match: the first element of the array is the whole match, the other elements are the capture groups.
    * Example:
        ```php
        $parser = new \Salyam\MorningBlue\BBCode();
        $parser->AddComplexParserRule(
          'prism',
           '\[code language=(.*)\](.*)\[\/code\]',
           function (array $matches) {
             return 'The count of the matches: ' . count($matches) . ', the programming language is: ' $matches[1];
           }
         );
        ``` 

# Licence #

Copyright (c) 2020 Salyamosy, Andras

Licenced under [MIT](LICENCE.md) Licence