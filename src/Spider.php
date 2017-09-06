<?php 
namespace Wayne\Spider;

use Atrox\Matcher;
use Jclyons52\PHPQuery\Document;
use GuzzleHttp\Client as GuzzleHttp;

abstract class Spider {

    protected $name = 'default';

    protected $startup = [
        'http://www.dianping.com/search/category/2/35/g2926',
    ];

    public function __construct()
    {
        $this->setup();
    }

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

    protected function parseDescription($description)
    {
        $description = $this->getDescription();
    }

    public function run()
    {
        $config = config('spider.http');
        $selector = array('title'=>['h2 a','text'],'link'=>['h2 a','href']);
        $client = new GuzzleHttp;
        collect($this->startup)->each(function ($url) use ($client) {
            $response = $client->get($url);
            $html = $response->getBody();
            $dom = new Document($html);
            // echo $response;
            
            $data = $this->handle($response, $response);
            $this->terminal($response, $response);
        });
    }

    protected parseData($dom, $desc)
    {
        $return = [];
        if (!array_key_exists('name', $desc)) {
            foreach($desc as $field){
                $return += $this->parseData($dom, $field);
            }
            return $return;
        }

        $name  = $desc['name'];
        $method = 'parse'.ucfirst($desc['type']);
        switch ($desc['data']) {
            case 'collection':
                $list = $this->{$method}($dom, $desc['expression']);
                foreach($list as $item){
                    $return[$name][] =  $this->parseData($item, $desc['items']);
                }
                break;
            case 'json':
                $wrapper = $this->{$method}($dom, $desc['expression']);
                $return[$name] =  $this->parseData($wrapper, $desc['items']);
                break;
            case 'array':
                # code...
                $return[$name] =  $this->{$method}($dom, $desc['expression']);
                break;
            case 'inter':
                $return[$name] =  $this->{$method}($dom, $desc['expression']);
                # code...
                break;
            case 'string':
            default:
                $return[$name] =  $this->{$method}($dom, $desc['expression']);
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

    abstract public function getDescription();

    abstract public function getPageGenerator();
}
