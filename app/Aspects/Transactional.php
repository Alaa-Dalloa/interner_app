<?php

namespace App\Aspects;
namespace App\Http\Controllers\FileController;
use AhmadVoid\SimpleAOP\Aspect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\cache;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Transactional implements Aspect
{

    // The constructor can accept parameters for the attribute
    public function __construct( 
     public $expiration = 10,
     public $maxAttempting = 5,
     public $key= null
    )
    {

    }

    public function executeBefore($request, $FileController, $reserveFiles)
    {
        // Get or generating a unique key
        $lockKey = $this->key ?? $FileController . '_' . $method;

        $lock = Cache::lock($lockKey , $this->expiration);

        //Attempt to accquire the lock for the given  number of secound
        $lock->block($this->maxAttemptingTime);

        //Store the lock instance in the request attributes
        $request->attributes->set('lock',$lock);
    }

    public function executeAfter($request, $FileController, $reserveFiles, $response)
    {
        //Get the lock instance from the request attributes
        $lock = $request->attributes->get('lock');

        //Release the lock
        $lock?->release();
    }

    public function executeException($request, $FileController, $reserveFiles, $exception)
    {
        //Get the lock instance from the request attributes
        $lock = $request->attributes->get('lock');

        // Release the lock 
        $lock?->release();
    }
}
