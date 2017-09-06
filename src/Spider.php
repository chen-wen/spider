<?php 
namespace Wayne\Spider;

use Atrox\Matcher;
use Jclyons52\PHPQuery\Document;
use GuzzleHttp\Client as GuzzleHttp;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

abstract class Spider {

    protected $name = 'default';

    protected $inputEncodeing = null;

    protected $done = true;

    protected $startup = [];

    public function __construct()
    {
        $this->setup();
    }


    abstract public function getSchema();

    abstract public function getPageGenerator();

    public function is($name)
    {
        return $this->name === $name;
    }

    public function shouldSkip()
    {
        return false;
    }

    public function setup()
    {
        // todo something
    }

    public function run()
    {
        $cookies = SetCookie::fromString(config('spider.cookie'));
        $config = [
            'headers' => config('spider.headers', []),
            'cookie'  => new CookieJar(false, $cookies),
        ];
        $client = new GuzzleHttp($config);

        $handle = function ($url) use ($client) {
            try {
                $response = $client->get($url);
                $html = $response->getBody();
                $this->inputEncodeing = $this->getEncode($html);
                $data = $this->parseDocument($html, $this->getSchema());
                $data = $this->handle($data, $html);
                $this->terminal($data, $html);
            } catch(\Exception $e) {
                $this->error($e, $url);
            }
        };

        if (!empty($this->startup)) {
            collect($this->startup)->each($handle);
        }

        while ($url = $this->getPageGenerator()) {
            $handle($url);
        }
    }

    private function getEncode($string)
    {
        return mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    }

    protected function parseSelector($selector) {
        $return = [
            'method' => null,
            'selector' => null,
            'attribute' => null,
        ];
        $reg = '/^(.+?)(::([a-z]+)(\(([a-z\-]+)?\))?)?$/';
        preg_match($reg, $selector, $matches);
        $count = count($matches);
        if($count == 2){
            $return['selector'] = $matches[1];
            $return['method'] = 'text';
        } else if($count == 4 || $count == 5){
            $return['selector'] = $matches[1];
            $return['method'] = $matches[3];
        } else if($count == 6){
            $return['selector'] = $matches[1];
            $return['method'] = $matches[3];
            $return['attribute'] = $matches[5];
        }
        return $return;
    }

    protected function parseCss($html, $selector, $all = false)
    {
        if (!$html) {
            return null;
        }

        $html = method_exists($html, 'toString') ? $html->toString() : strval($html);
        $html = '<meta charset="'.$this->inputEncodeing.'"/>'.$html;
        $dom = new Document($html);
        $option = $this->parseSelector($selector);
        $query = $all ? $dom->querySelectorAll($option['selector']) : $dom->querySelector($option['selector']);
        if (!$query) {
            return null;
        }
        if ($option['method'] == 'attr') {
            return $query->{$option['method']}($option['attribute']);
        } elseif ($option['method'] === 'text') {
            return $query->text();
        } else {
            return $all ? $query : $query->toString();
        }
    }

    protected function parseXpath($html, $xpath, $all = false)
    {
        if (!$html) {
            return null;
        }

        $m = Matcher::single($xpath)->fromHtml();
        return $m($html);
    }
    protected function parseRegex($html, $regex, $all = false)
    {
        if (!$html) {
            return null;
        }

        if ($all) {
            preg_match_all($regex, $html, $matches);
            return $matches;
        } else {
            preg_match($regex, $html, $matches);
            return count($matches) > 1 ? $matches[1] : null;
        }
    }

    protected function parseDocument($dom, $schema)
    {
        $return = [];
        if (!array_key_exists('name', $schema)) {
            foreach($schema as $field){
                $return += $this->parseDocument($dom, $field);
            }
            return $return;
        }

        $name  = $schema['name'];
        $method = 'parse'.ucfirst($schema['type']);
        switch ($schema['data']) {
            case 'collection':
                $expression = ends_with($schema['expression'], '::html()') 
                    ? $schema['expression'] : $schema['expression'].'::html()'; 
                $list = $this->{$method}($dom, $expression, true);
                foreach($list as $item){
                    $return[$name][] =  $this->parseDocument($item, $schema['items']);
                }
                break;
            case 'json':
                if ($schema['type'] !== 'regex') {
                    $expression = ends_with($schema['expression'], '::html()') 
                        ? $schema['expression'] : $schema['expression'].'::html()';
                    $wrapper = $this->{$method}($dom, $expression, true);
                    $return[$name] = $this->parseDocument($wrapper, $schema['items']);
                } else {
                    $content = $this->{$method}($dom, $schema['expression']);
                    $return[$name] = $content;
                    $array = json_decode($return[$name], true);
                    if (is_array($array)) {
                        $return[$name] = $array;
                    }
                }
                break;
            case 'array':
                $return[$name] =  $this->{$method}($dom, $schema['expression'], true);
                break;
            case 'inter':
                $return[$name] = (int) $this->{$method}($dom, $schema['expression']);
                break;
            case 'string':
            default:
                $return[$name] =  $this->{$method}($dom, $schema['expression']);
                break;
        }
        return $return;
    }

    public function handle($data, $response)
    {
        return $data; 
    }

    public function terminal($data, $response)
    {
        // todo something
    }

    protected function error($e, $url) 
    {
        logger($url);
        logger($e);
        $this->done = true;
    }
}
