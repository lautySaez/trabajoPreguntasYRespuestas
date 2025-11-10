<?php

class IncludeFileRenderer
{
    public function __construct()
    {
    }

    public function render($template, $data = null)
    {
        include_once("views/partial/header.mustache");
        include_once("vista/" . $template . "Vista.php");
        include_once("views/partial/footer.mustache");
    }
}