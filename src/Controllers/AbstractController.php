<?php

namespace Wallace\Controllers;

abstract class AbstractController
{

    public function render($data = '')
    {
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}
