<?php 
namespace Wayne\Spider;

class SpiderManager {

    public function __construct()
    {
        $this->spiders = config('spider.spiders');
    }

    protected $spiders = [
        // spider class name here
    ];

    protected function handle($name = null)
    {
        if (!empty($name)) {
            $spider = collect($this->spiders)
                ->first(function ($item) use ($name) {
                    return (new $item)->is($name);
                });
            if ($spider) {
                (new $spider)->run();
            }
        } else {
            collect($this->spiders)
            ->each(function ($item) {
                $spider = new $item;
                if (!$spider->shouldSkip()) {
                    $spider->run();
                }
            });
        }
    }

    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }
}
