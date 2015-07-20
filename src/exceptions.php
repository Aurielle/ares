<?php

namespace Aurielle\Ares;

abstract class AresException extends \Exception {}

class IdentificationNumberNotFoundException extends AresException {}

class UndefinedOffsetException extends AresException {}

