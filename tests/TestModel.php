<?php


namespace Dymantic\CloudinaryModelImages\Tests;


use Dymantic\CloudinaryModelImages\CloudinaryImages;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use CloudinaryImages;
}