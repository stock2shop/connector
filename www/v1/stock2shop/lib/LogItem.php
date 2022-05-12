<?php

namespace stock2shop\lib;

/**
 * Log Item
 * @package stock2shop\vo
 */
class LogItem
{
    /** @var int $level Severity level of the log. */
    public int $level;

    /** @var string $message Human-readable message. */
    public string $message;

    /** @var array $context Key value array of additional data to log. (optional). */
    public array $context;

    /** @var array $tags An array of tag names with which this log item is associated. */
    public $tags = [];

    public function getLevel() {
        return LogWriter::LOG_WRITER_SEVERITY_LEVELS[$this->level];
    }

    /**
     * Sanitize
     *
     * This calls the sanitizeProperty() method of this class
     * statically for each property on this object.
     *
     * @return void
     */
    public function sanitize()
    {
        $this->message = $this->sanitizeProperty($this->message);
//        foreach ($this->context as $key => $value) {
//            if (is_array($value)) {
//                foreach ($value as $k => $v) {
//                    $this->context[$key][$k] = $this->sanitizeProperty($v);
//                }
//            } else {
//                $this->context[$key] = $this->sanitizeProperty($value);
//            }
//        }
    }

    /**
     * Sanitize Property
     *
     * Removes any sensitive data from the logging message and context.
     * Uses regex to find and replace the string text which matches any
     * of the default patterns to replace.
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeProperty(string $string): string
    {
        // Iterate over all patterns and replace where required.
        foreach (LogWriter::LOG_WRITER_SANITIZE_PATTERNS as $sensitivePattern) {
            $matches = [];
            $matchPattern = '~' . $sensitivePattern . '=[\w+]~';
            if (preg_match($matchPattern, $string, $matches) > 0) {
                // Replace the value in the string.
                $replacement = $sensitivePattern . '=' . LogWriter::LOG_WRITER_SANITIZE_MASK;
                return str_replace($matchPattern, $replacement, $string);
            }
        }
        return $string;
    }
}