<?php
namespace Straylightagency\DataLayer;

use Kint;
use Illuminate\Session\SessionManager;
use \Straylightagency\DataLayer\SessionHandler as BasicSessionHandler;
use \Straylightagency\DataLayer\Laravel\SessionHandler as LaravelSessionHandler;

/**
 * Helper class for handling DataLayer object with Google Tag Manager
 *
 * @package Straylightagency\DataLayer
 * @author Anthony Pauwels <anthony@straylightagency.be>
 */
class DataLayerManager
{
    /** @var array */
    protected array $data = [];

    /** @var SessionHandlerInterface */
    protected SessionHandlerInterface $session;

    /** @var string */
    protected string $gtm_id;

    /** @var string */
    const string SESSION_KEY = 'datalayer';

    /**
     * DataLayer constructor.
     *
     * @param SessionHandlerInterface $session
     * @param string $gtm_id
     */
    public function __construct(SessionHandlerInterface $session, string $gtm_id)
    {
        $this->session = $session;
        $this->gtm_id = $gtm_id;

        $this->load();
    }

    /**
     * @param string $gtm_id
     * @return static
     */
    static public function newUsingBasicSession(string $gtm_id): static
    {
        return new self( new BasicSessionHandler, $gtm_id );
    }

    /**
     * @param SessionManager $manager
     * @param string $gtm_id
     * @return static
     */
    static public function newUsingLaravelSession(SessionManager $manager, string $gtm_id): static
    {
        return new self( new LaravelSessionHandler( $manager ), $gtm_id );
    }

    /**
     * Load data from the session
     */
    public function load(): void
    {
        $this->data = array_merge( $this->session->get(), [] );
    }

    /**
     * Clear all data from the array
     */
    public function clear(): void
    {
        $this->session->put( [] );
    }

    /**
     * Save the data into the session
     */
    public function save(): void
    {
        $this->session->put( $this->data );
    }

    /**
     * Get the data array
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Method alias to getData()
     *
     * @return array
     */
    public function data(): array
    {
        return $this->getData();
    }

    /**
     * Set a value with name into the data array
     *
     * @param array|string $name
     * @param array|string|null $value
     * @param bool $save
     * @return self
     *
     * @example
     * DataLayer::with( 'name', 'value' )->with( 'foo', 'bar' );
     * DataLayer::with( [ 'name' => 'value' ] );
     */
    public function with(array|string $name, array|string $value = null, bool $save = false): self
    {
        if ( is_array( $name ) ) {
            return $this->withArray( $name, $save );
        }

        $this->data[ $name ] = $value;

        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Set a full array into the data
     *
     * @param array $data
     * @param bool $save
     * @return self
     */
    public function withArray(array $data, bool $save = false): self
    {
        $this->data = array_merge( $this->data, $data );

        if ( $save ) {
            $this->save();
        }

        return $this;
    }

    /**
     * Print the datalayer object into the page; options can be used to control the init and the script
     *
     * @param bool $init
     * @param bool $script
     * @param bool $clear
     * @return void
     */
    public function print(bool $init = true, bool $clear = true, bool $script = true): void
    {
        $html = '';

        if ( $init ) {
            $html .= $this->init();
        }

        $html .= $this->pushData( $this->data, $clear );

        if ( $script ) {
            $html .= $this->script( $this->gtm_id );
        }

        echo $html;
    }

    /**
     * @param string|null $gtm_id
     * @return void
     */
    public function printNoscript(?string $gtm_id = null):void
    {
        echo $this->noScript( $gtm_id );
    }

    /**
     * Init the JS DataLayer object
     * @return string
     */
    public function init():string
    {
        return '<script>window.dataLayer = window.dataLayer || []</script>' . PHP_EOL;
    }

    /**
     * Push the data into the JS DataLayer object
     *
     * @param array $data
     * @param bool $clear
     * @return string
     */
    public function pushData(array $data, bool $clear = true):string
    {
        $html = '';

        if ( ! empty( $data ) ) {
            $data = json_encode( $data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT );

            $html = "<script>window.dataLayer.push($data)</script>" . PHP_EOL;
        }

        if ( $clear ) {
            $this->clear();
        }

        return $html;
    }

    /**
     * Print the Google tag manager init script with given Google id
     *
     * @param string|null $gtm_id
     * @return string
     */
    public function script(?string $gtm_id = null):string
    {
        $gtm_id = $gtm_id ?: $this->gtm_id;

        return "
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','$gtm_id');</script>
        <!-- End Google Tag Manager -->" . PHP_EOL;
    }

    /**
     * Print the Google tag manager no-script tag with given google id
     *
     * @param string|null $gtm_id
     * @return string
     */
    public function noScript(?string $gtm_id = null):string
    {
        $gtm_id = $gtm_id ?: $this->gtm_id;

        return "
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=$gtm_id\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->" . PHP_EOL;
    }

    /**
     * Dump & Die the data; debug purpose
     */
    #[NoReturn]
    public function dump(): void
    {
        if ( function_exists( 'dd' ) ) {
            dd( $this->data );
        }

        if ( ! in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && ! headers_sent() ) {
            header('HTTP/1.1 500 Internal Server Error');
        }

        if ( class_exists( Kint::class ) ) {
            Kint::dump( $this->data );
            exit;
        }

        echo '<pre>';
        var_dump( $this->data );
        echo '</pre>';
        exit;
    }

    /**
     * Clear all data from the array at the end of the script
     */
    public function __destruct()
    {
        $this->data = [];
    }
}
