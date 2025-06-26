<?php

namespace support;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;

class ResponseUtil {
    
    public static function result(int $code = Result::CODE_OK, string $message = Result::MESSAGE_OK, $data = []):Result {
        return new Result($code,$message,$data);
    }

    public static function ok($data = []):Result {
        return self::result(Result::CODE_OK,Result::MESSAGE_OK,$data);
    }

    public static function error(string $message):Result {
        return self::result(Result::CODE_ERROR,$message);
    }
    
    public static function collection($data,$transformer,array $meta = []):Result {
        $resource = new Collection($data,self::getTransformer($transformer));
        $resource->setMeta($meta);
        $manager = self::createManager();
        return self::ok($manager->createData($resource)->toArray());
    }

    public static function item($data,$transformer,array $meta = []):Result {
        $resource = new Item($data,self::getTransformer($transformer));
        $resource->setMeta($meta);
        $manager = self::createManager();
        return self::ok($manager->createData($resource)->toArray());
    }

    private static function createManager() {
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        return $manager;
    }

    private static function getTransformer($transformer) {
        if(!$transformer) {
            throw new \Exception('Transformer is required');
        }
        if(is_callable($transformer) || $transformer instanceof TransformerAbstract) {
            return $transformer;
        }
        if(is_object($transformer) && !$transformer instanceof TransformerAbstract) {
            throw new \Exception('Transformer class '.get_class($transformer).' must be a subclass of '.TransformerAbstract::class);
        }
        
        if(!class_exists($transformer,true)) {
            throw new \Exception('Transformer class '.$transformer.' not found');
        }
        if(!is_subclass_of($transformer,TransformerAbstract::class)) {
            throw new \Exception('Transformer class '.$transformer.' must be a subclass of '.TransformerAbstract::class);
        }
        return new $transformer();
    }
}