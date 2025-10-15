<?php

use Kirby\Cms\Page;

class DefaultPage extends Page
{
  public function getJsonData(array $content): array
  {
    return $content;
  }
}
