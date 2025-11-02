<?php

namespace DuckyCMS;

enum MenuIcon: string
{
  case pages = 'pages';
  case settings = 'settings';
  case users = 'users';
  case dashboard = 'dashboard';
  case logout = 'logout';

  public function svg(int $width = 24, ?int $height = null): string
  {
    $svgClass   = 'icon icon-' . $this->value;
    $heightAttr = $height === null ? '' : ' height="' . $height . '"';

    $paths = match ($this) {
      self::dashboard =>
        '<path class="primary" d="M2 2h9v7H2z M13 15h9v7h-9z"/>' .
        '<path class="secondary" d="M13 2h9v11h-9z M2 11h9v11H2z"/>',
      self::pages =>
        '<path class="primary" d="M15.414 1H3v22h18V6.586z"/>' .
        '<path class="secondary" d="M14.5 7.5V3L19 7.5z"/>' .
        '<path class="secondary" d="M17 14H7v-2h10zm0 4H7v-2h10z"/>',
      self::settings =>
        '<path class="primary" d="M14.82 1H9.18l-.647 3.237a8.5 8.5 0 0 0-1.52.88l-3.13-1.059l-2.819 4.884l2.481 2.18a8.6 8.6 0 0 0 0 1.756l-2.481 2.18l2.82 4.884l3.129-1.058c.472.342.98.638 1.52.879L9.18 23h5.64l.647-3.237a8.5 8.5 0 0 0 1.52-.88l3.13 1.059l2.82-4.884l-2.482-2.18a8.6 8.6 0 0 0 0-1.756l2.481-2.18l-2.82-4.884l-3.128 1.058a8.5 8.5 0 0 0-1.52-.879z"/>' .
        '<path class="secondary" d="M12 16a4 4 0 1 1 0-8a4 4 0 0 1 0 8"/>',
      self::logout =>
        '<path class="secondary" d="M13.496 21H6.5c-1.105 0-2-1.151-2-2.571V5.57c0-1.419.895-2.57 2-2.57h7"/>' .
        '<path class="primary" d="M12 17l-5-5 5-5v3h8v4h-8v3z"/>',
      self::users => '',
    };

    return <<<SVG
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        width="$width"{$heightAttr}
        viewBox="0 0 24 24"
        aria-hidden="true"
        class="$svgClass"
        role="img"
      >
        $paths
      </svg>
    SVG;
  }
}

function dcms_menu_item(array $options): string
{
  $nameRaw   = (string)($options['name'] ?? '');
  $name      = htmlspecialchars($nameRaw);
  $width     = (int)($options['width'] ?? 24);
  $isCurrent = (bool)($options['is_current'] ?? false);
  $href      = (string)($options['href'] ?? '#');
  $extraClass = trim((string)($options['extra_class'] ?? ''));
  /**
   * Raw attributes string, built by caller with proper escaping
   */
  $extraAttrs = (string)($options['attrs'] ?? '');
  $element    = strtolower((string)($options['element'] ?? 'a'));
  $element = in_array($element, ['a','button','summary'], true) ? $element : 'a';

  /**
   * Resolve icon key
   */
  $iconKey = $options['icon'] ?? null;
  $iconEnum = null;
  if (is_string($iconKey) && $iconKey !== '') {
    $iconEnum = MenuIcon::tryFrom(strtolower($iconKey));
  }
  if (!$iconEnum) {
    $iconEnum = MenuIcon::tryFrom(strtolower($nameRaw));
  }
  if (!$iconEnum) {
    return '';
  }

  $iconSvg     = $iconEnum->svg($width);
  $class       = trim('menu-item' . ($isCurrent ? ' is-current' : '') . ($extraClass !== '' ? ' ' . $extraClass : ''));
  $ariaCurrent = $isCurrent ? ' aria-current="page"' : '';

  if ($element === 'button') {
    return <<<HTML
      <button type="button" class="$class"$ariaCurrent $extraAttrs>
        {$iconSvg}
        <span class="menu-name">$name</span>
        <span class="chevron" aria-hidden="true"></span>
      </button>
    HTML;
  }

  if ($element === 'summary') {
    return <<<HTML
      <summary class="$class"$ariaCurrent $extraAttrs>
        {$iconSvg}
        <span class="menu-name">$name</span>
        <span class="chevron" aria-hidden="true"></span>
      </summary>
    HTML;
  }

  $hrefAttr = ' href="' . htmlspecialchars($href, ENT_QUOTES) . '"';

  return <<<HTML
    <a{$hrefAttr} class="$class"$ariaCurrent $extraAttrs>
      {$iconSvg}
      <span class="menu-name">$name</span>
    </a>
  HTML;
}