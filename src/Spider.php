<?php 
namespace Wayne\Spider;

use GuzzleHttp\Client as GuzzleHttp;

abstract class Spider {

    protected $name = 'default';

    protected $startup = [

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
            
            $data = $this->handle($response, $response);
            $this->terminal($response, $response);
        });
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
