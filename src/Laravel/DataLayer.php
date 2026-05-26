<?php
namespace Straylightagency\DataLayer\Laravel;

use Illuminate\Support\Facades\Facade;
use Straylightagency\DataLayer\DataLayerManager;

/**
 * Facade.
 * Provide quick access methods to the DataLayer helper class
 *
 * @method static void load()
 * @method static void clear()
 * @method static void save()
 * @method static array getData()
 * @method static array data()
 * @method static DataLayerManager with(array|string $name, array|string $value = null, bool $save = false)
 * @method static DataLayerManager withArray(array $data, bool $save = false)
 * @method static void print(bool $init = true, bool $clear = true, bool $script = true, array $attributes = [])
 * @method static void printIf(bool $boolean, bool $init = true, bool $clear = true, bool $script = true, array $attributes = [])
 * @method static void printNoScript(string $gtm_id = null)
 * @method static void printNoScriptIf(bool $boolean, string $gtm_id = null)
 * @method static string init()
 * @method static string pushData(array $data, bool $clear = false)
 * @method static string script(string $gtm_id = null, array $attributes = [])
 * @method static string noScript(string $gtm_id = null)
 * @method static void dump()
 *
 * @package Straylightagency\DataLayer
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class DataLayer extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DataLayerManager::class;
    }
}
