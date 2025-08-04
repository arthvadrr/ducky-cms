<?php

namespace DuckyCMS;

enum AlertType: string
{
  case info = 'info';
  case success = 'success';
  case warning = 'warning';
  case danger = 'danger';

  public function svg(): string
  {
    $color = match ($this) {
      self::info => 'blue',
      self::success => 'green',
      self::warning => 'goldenrod',
      self::danger => 'red',
    };

    $paths = match ($this) {
      self::info => <<<SVG
      <path d="M12 9h.01 M11 12h1v4h1"/>
    SVG,
      self::success => <<<SVG
      <path d="m9 12l2 2l4-4"/>
    SVG,
      self::warning, self::danger => <<<SVG
      <path d="M12 9v4 M12 16h.01"/>
    SVG,
    };

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke="$color" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
  <rect x="2" y="2" width="20" height="20" rx="2"/>
  $paths
</svg>
SVG;
  }
}

function dcms_alert(
  string    $message,
  AlertType $type = AlertType::info): string
{
  $class = htmlspecialchars("alert alert-$type->value");
  $icon  = $type->svg();
  $msg   = htmlspecialchars($message);

  return <<<HTML
  <div role="alert" class="$class">
    <span class="icon" aria-hidden="true">$icon</span>
    <span class="message">$msg</span>
  </div>
  HTML;
}