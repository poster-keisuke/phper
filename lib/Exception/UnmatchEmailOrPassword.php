<?php

namespace MyApp\Exception;

class UnmatchEmailOrPassword extends \Exception {
  protected $message = 'Unmatch email or password!';
}