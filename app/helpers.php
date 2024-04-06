<?php
namespace App;

if(!file_exists('pageCount()')){
    function pageCount(): Int
    {
        return 10;
    }
}