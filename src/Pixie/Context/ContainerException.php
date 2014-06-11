<?php

namespace Pixie\Context;

use Pixie\Exception;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException As InteropContainerException;

class ContainerException extends \Pixie\Exception implements InteropContainerException
{
}
