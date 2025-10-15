<?php

use Kirby\Cms\Page;

class SectiontPage extends Page
{
  public function getJsonData(array $content): array
  {
    return $content;
  }
}
