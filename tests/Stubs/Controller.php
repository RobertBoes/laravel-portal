<?php


namespace RobertBoes\LaravelPortal\Tests\Stubs;


class Controller
{
    public function auth()
    {
        return response()->json([
            'success' => true,
            'type' => 'auth',
        ]);
    }

    public function guest()
    {
        return response()->json([
            'success' => true,
            'type' => 'guest',
        ]);
    }
}
