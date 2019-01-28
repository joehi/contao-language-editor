<?php

/**
 * Contao Language editor
 *
 * @package    Language Editor
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012 InfinitySoft 2012
 * @copyright  2015-2019 netzmacht David Molineus
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-language-editor/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\LanguageEditor;

/**
 * Language editor class
 */
class LanguageEditor extends \Backend
{
    /**
     * Default language groups.
     *
     * @var array
     */
    public static $defaultGroups = array(
        // @codingStandardsIgnoreStart
        'CNT'    => 'countries', // countries
        'ERR'    => 'default',   // Error messages
        'PTY'    => 'default',   // Page types
        'FOP'    => 'default',   // File operation permissions
        'CHMOD'  => 'default',   // CHMOD levels
        'DAYS'   => 'default',   // Day names
        'MONTHS' => 'default',   // Month names
        'MSC'    => 'default',   // Miscellaneous
        'UNITS'  => 'default',   // Units
        'XPL'    => 'explain',   // Explanations
        'LNG'    => 'languages', // Languages
        'MOD'    => 'modules',   // Back end modules
        'SEC'    => 'default',   // Security questions
        'CTE'    => 'default',   // Content elements
        'FMD'    => 'default'    // Front end modules
        // @codingStandardsIgnoreEnd
    );

    /**
     * Singleton instance.
     *
     * @var LanguageEditor
     */
    protected static $objInstance = null;

    /**
     * Get singleton instance.
     *
     * @return LanguageEditor
     */
    public static function getInstance()
    {
        if (self::$objInstance === null) {
            self::$objInstance = new LanguageEditor();
        }

        return self::$objInstance;
    }

    /**
     * Singleton constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Get the language file name for a language group.
     *
     * @param string $group The group name.
     *
     * @return string
     */
    public function getLanguageFileName($group)
    {
        if (isset(static::$defaultGroups[$group])) {
            return static::$defaultGroups[$group];
        }

        return $group;
    }

    /**
     * Get all language groups which belongs to a file.
     *
     * @param string $fileName The file name.
     *
     * @return array
     */
    public function getLanguageGroups($fileName)
    {
        return array_filter(
            static::$defaultGroups,
            function ($item) use ($fileName) {
                return $item === $fileName;
            }
        );
    }

    /**
     * Get language value.
     *
     * @param array $parent The parent array.
     * @param array $path   The language path as array.
     * @param bool  $raw    Get value as raw value. Otherwise format it nicely.
     *
     * @return array|string
     */
    public function getLangValue(&$parent, $path, $raw = false)
    {
        $next = array_shift($path);

        // language path not found
        if (!isset($parent[$next])) {
            return 'not found!';
        }

        // walk deeper
        if (count($path)) {
            return $this->getLangValue($parent[$next], $path, $raw);
        }

        // return raw value
        if ($raw) {
            return $parent[$next];
        }

        // value is array (like label)
        if (is_array($parent[$next])) {
            return '&ndash; ' . implode('<br>&ndash; ', $this->plainEncode($parent[$next]));
        }

        // value is something else
        return $this->plainEncode($parent[$next]);
    }

    /**
     * Plain encode value.
     *
     * @param mixed $varValue String or array.
     *
     * @return array|string
     */
    public function plainEncode($varValue)
    {
        if (is_array($varValue)) {
            foreach ($varValue as $k => $v) {
                $varValue[$k] = $this->plainEncode($v);
            }
            return $varValue;
        } else {
            return htmlentities($varValue, (ENT_QUOTES | ENT_HTML401), 'UTF-8');
        }
    }
}
