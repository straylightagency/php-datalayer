<?php
namespace Straylightagency\DataLayer;

/**
 * Default Session Handler
 *
 * @package Straylightagency\DataLayer
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class SessionHandler implements SessionHandlerInterface
{
    /**
     * Put a value into session global var
     *
     * @param array $value
     */
    public function put(array $value): void
    {
        $_SESSION[ DataLayerManager::SESSION_KEY ] = $value;
    }

    /**
     * Get a value from session global var or return a default value if it does not exist
     *
     * @param array $default
     * @return array
     */
    public function get(array $default = []): array
    {
        return $_SESSION[DataLayerManager::SESSION_KEY] ?? $default;
    }
}