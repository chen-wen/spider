<?php 
namespace Wayne\Spider;

use QL\QueryList;

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
        $config = config('spider.http', []);
        collect($this->startup)->each(function ($url) use ($config) {
            $config['url'] = $config['referrer'] = $url;
            $query = QueryList::run('Request', $config)
                    ->setQuery($selector)
                    ->getData();
            $data = $this->handle($query->data, $query->html);
            $this->terminal($data, $query->html);
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
