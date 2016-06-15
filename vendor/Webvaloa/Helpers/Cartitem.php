<?php

namespace Webvaloa\Helpers;

use stdClass;
use RuntimeException;

class BabypandaCartitem
{
    public $item;

    public function __construct()
    {
        $this->item = new stdClass();
        $this->item->id = false;
        $this->item->amount = 1;
        $this->item->size = false;
        $this->item->updateAmount = false;
        $this->item->sizeAsHash = false;
        $this->item->url = false;
    }

    public function __set($k, $v)
    {
        if (isset($this->item->{$k})) {
            $this->item->{$k} = $v;
        }

        throw new RuntimeException('property does not exist');
    }

    public function __get($k)
    {
        if (isset($this->item->{$k})) {
            return $this->item->{$k};
        }

        throw new RuntimeException('property does not exist');
    }
}
