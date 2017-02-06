<?php

namespace Paymill\Paymill\Services;

/**
 * Services_Paymill_Exception class
 */
class Exception extends \Magento\Framework\Exception\LocalizedException
{
  /**
   * Constructor for exception object
   *
   * @return void
   */
  public function __construct($message, $code)
  {
        parent::__construct($message, $code);
  }
}
