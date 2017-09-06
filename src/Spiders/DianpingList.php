<?php 
namespace Wayne\Spider\Spiders;

use Wayne\Spider\Spider;
use Redis;

class DianpingList extends Spider{
    
    protected $name = 'dianping-list';

    protected $startup = [
        // 'http://www.dianping.com/search/category/2/35/g2926',
    ];

    public function setup()
    {

    }

    public function getPageGenerator()
    {
        static $index = 1;
        static $page = '';
        if ($this->done) {
            $index = 1;
            $page = Redis::spop($this->name);
            if (!$page) {
                return false;
            }
            $this->done = false;
        }
        return $page .'p' . $index ++;
    }

    public function getSchema()
    {
        return [
            'name'=> 'list',
            'data'=>'collection',       // collection|json|array|string|integer
            'type'=>'css',  // css|xpath|regex
            'expression'=>'#shop-all-list li',
            'items' => [
                [
                    'name'=>'name',
                    'data'=>'string',
                    'type'=>'css',
                    'expression'=>'.tit h4::text()',
                ],
                [
                    'name' => 'comment',
                    'data'=>'integer',
                    'type'=>'css',
                    'expression'=>'.review-num b',
                ],
            ],
        ];
    }

    public function handle($data, $response)
    {
        return $data;
    }

    public function terminal($data, $response)
    {
        if (count($data['list']) < 15) {
            $this->done = true;
        }
        logger($data);
    }
}
