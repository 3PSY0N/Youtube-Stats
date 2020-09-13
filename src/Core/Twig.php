<?php

namespace App\Core;

use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Extra\Intl\IntlExtension;

class Twig
{
    /**
     * @var \Twig\Environment
     */
    private $twig;
    
    /**
     * Twig constructor.
     */
    public function __construct() {
        $loader = new FilesystemLoader('../views/');
        $twig = new Environment($loader, [
            'cache' => '../cache/',
//            'cache' => false,
        ]);
        
//        $twig = new Environment($loader, ['debug' => true]);
        
        $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
            public function load($class) {
                if (MarkdownRuntime::class === $class) {
                    return new MarkdownRuntime(new DefaultMarkdown());
                }
            }
        });
        $twig->addExtension(new MarkdownExtension());
        $twig->addExtension(new IntlExtension());
        $this->twig = $twig;
    }
    
    /**
     * @param $template
     * @param array $array
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($template, $array = [])
    {
        return $this->twig->render($template, $array);
    }
}