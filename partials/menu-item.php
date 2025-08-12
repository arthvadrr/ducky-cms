<?php

namespace DuckyCMS;

enum MenuIcon: string
{
  case pages = 'pages';
  case settings = 'settings';
  case users = 'users';
  case dashboard = 'dashboard';

  public function svg(int $width = 24, ?int $height = null, string $fill = '#ffffff'): string
  {
    if ($height === null) {
      $height = $width;
    }

    $paths = match ($this) {
      self::dashboard => '<path fill="' . $fill . '" d="M2 2h9v7H2zm11 0h9v11h-9zM2 11h9v11H2zm11 4h9v7h-9z"/>',
      self::pages => '<path fill="' . $fill . '" d="M15.414 1H3v22h18V6.586zM14.5 7.5V3L19 7.5zM17 14H7v-2h10zm0 4H7v-2h10z"/>',
      self::settings => '<path fill="' . $fill . '" d="M14.82 1H9.18l-.647 3.237a8.5 8.5 0 0 0-1.52.88l-3.13-1.059l-2.819 4.884l2.481 2.18a8.6 8.6 0 0 0 0 1.756l-2.481 2.18l2.82 4.884l3.129-1.058c.472.342.98.638 1.52.879L9.18 23h5.64l.647-3.237a8.5 8.5 0 0 0 1.52-.88l3.13 1.059l2.82-4.884l-2.482-2.18a8.6 8.6 0 0 0 0-1.756l2.481-2.18l-2.82-4.884l-3.128 1.058a8.5 8.5 0 0 0-1.52-.879zM12 16a4 4 0 1 1 0-8a4 4 0 0 1 0 8"/>',
      self::users => '',
    };

    return <<<SVG
<svg 
  xmlns="http://www.w3.org/2000/svg" 
  width="$width" 
  height="$height" 
  viewBox="0 0 24 24"
  aria-hidden="true"
>
  $paths
</svg>
SVG;
  }
}

function dcms_menu_item(array $options): string
{
  $nameRaw = (string)($options['name'] ?? '');
  $name    = htmlspecialchars($nameRaw);
  $width   = (int)($options['width'] ?? 24);
  $fill    = (string)($options['fill'] ?? '#ffffff');
  $href    = (string)($options['href'] ?? '#');

  $isCurrent = (bool)($options['is_current'] ?? false);

  /**
   * Validate the enum value
   */
  $iconEnum = MenuIcon::tryFrom(strtolower($nameRaw));
  if (!$iconEnum) {
    return '';
  }

  $iconSvg     = $iconEnum->svg($width, null, $fill);
  $class       = 'menu-item' . ($isCurrent ? ' is-current' : '');
  $ariaCurrent = $isCurrent ? ' aria-current="page"' : '';

  return
    <<<HTML
      <a href="$href" class="$class" role="menuitem" $ariaCurrent>
        $iconSvg
        <span class="menu-name">$name</span>
      </a>
    HTML;
}