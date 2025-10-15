<?php

use Kirby\Cms\Page;

class HomePage extends Page
{
  public function getJsonData(array $content): array
  {
    return $content;
  }
}
