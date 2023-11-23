<?php


if (! function_exists('statusId')) {

    function statusId(string $name)
    {
        return App\Models\Status::where('name', $name)->first()->id;
    }
}