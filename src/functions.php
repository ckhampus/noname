<?php

function underscore($string_to_underscore)
{
    $s = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1_$2', $string_to_underscore);
    $s = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $s);
    $s = str_replace('-', '_', $s);
    $s = strtolower($s);

    return $s;
}
