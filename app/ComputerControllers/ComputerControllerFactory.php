<?php

namespace App\ComputerControllers;

use App\ComputerControllers\ComputerControllerInterface;

class ComputerControllerFactory
{
    public function create(): ComputerControllerInterface
    {
        // TODO: this should take a server from the pool
        $server = config('scrape.servers')[0];

        return new VncComputerController(
            $server['vnc'],
            $server['control'],
            1024, // TODO $server['width'],
            768, // TODO $server['height'],
            1, // TODO $server['display_number']
        );
    }
}
