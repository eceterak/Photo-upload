<?php

function getFromSession($name)
{
    $content = $_SESSION[$name];

    session_unset();

    return $content;
}