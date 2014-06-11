<?php

namespace Pixie\Context;

use Pixie\Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\NotFoundException As InteropNotFoundException;

class NotFoundException extends Exception implements InteropNotFoundException
{
}
