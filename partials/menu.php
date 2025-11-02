<?php

namespace DuckyCMS;

/**
 * Render a navigation menu with optional one-level submenus.
 *
 * Each top-level item can include:
 * - name: string
 * - href: string
 * - is_current: bool (optional)
 * - width: int (optional, icon width for top-level)
 * - icon: string (optional; key matching MenuIcon; falls back to name matching)
 * - children: array of submenu items [{ name: string, href: string, is_current?: bool }]
 */
function dcms_render_menu(array $items): string
{
  $html = '<ul>';

  foreach ($items as $item) {
    if (!is_array($item)) {
      continue;
    }

    $name      = (string)($item['name'] ?? '');
    $href      = (string)($item['href'] ?? '#');
    $isCurrent = (bool)($item['is_current'] ?? false);
    $width     = (int)($item['width'] ?? 24);
    $icon      = $item['icon'] ?? null;

    $children = $item['children'] ?? [];
    $hasChildren = is_array($children) && !empty($children);

    /**
     * Check if any child is marked current
     */
    $childCurrent = false;
    if ($hasChildren) {
      foreach ($children as $child) {
        if (is_array($child) && !empty($child['is_current'])) {
          $childCurrent = true;
          break;
        }
      }
    }

    $isOpen = $isCurrent || $childCurrent;

    $liClasses = [];
    if ($hasChildren) { $liClasses[] = 'has-children'; }
    $liClassAttr = !empty($liClasses) ? ' class="' . implode(' ', $liClasses) . '"' : '';

    $html .= '<li' . $liClassAttr . '>';

    if ($hasChildren) {
      $openAttr = $isOpen ? ' open' : '';
      $html .= '<details' . $openAttr . '>';

      $html .= dcms_menu_item([
        'name'       => $name,
        'href'       => $href,
        'is_current' => $isCurrent,
        'width'      => $width,
        'icon'       => $icon,
        'attrs'      => '',
        'element'    => 'summary',
      ]);

      $html .= '<ul class="submenu" role="list" aria-label="' . htmlspecialchars($name) . ' submenu">';

      foreach ($children as $child) {
        if (!is_array($child)) { continue; }
        $cName = (string)($child['name'] ?? '');
        $cHref = (string)($child['href'] ?? '#');
        $cIsCurrent = !empty($child['is_current']);
        $cAria = $cIsCurrent ? ' aria-current="page"' : '';
        $html .= '<li><a href="' . htmlspecialchars($cHref, ENT_QUOTES) . '"' . $cAria . '>' . htmlspecialchars($cName) . '</a></li>';
      }

      $html .= '</ul>';
      $html .= '</details>';
    } else {
      $html .= dcms_menu_item([
        'name'       => $name,
        'href'       => $href,
        'is_current' => $isCurrent,
        'width'      => $width,
        'icon'       => $icon,
        'attrs'      => '',
        'element'    => 'a',
      ]);
    }

    $html .= '</li>';
  }

  $html .= '</ul>';

  return $html;
}