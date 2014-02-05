<?php
namespace PDSL;

class Launcher {

    private $option;

    /**
     *
     * @param array $options
     */
    public function __construct ($options=array())
    {
        $this->setOptions($options);
    }

    /**
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption( $name, $value )
    {
        $this->option[$name] = $value;
    }

    /**
     *
     * @param array $options
     */
    public function setOptions( $options )
    {
        $this->option = $option;
    }

    /**
     *
     * @param string $optionName
     */
    public function hasOption( $optionName )
    {
        return isset($this->option[$optionName]);
    }

    /**
     *
     * @param string $optionName
     */
    public function getOption( $optionName )
    {
        return $this->option[$optionName];
    }

    public function start ()
    {
        if ( !$this->hasOption('host') ) {
            $this->getDataFromConstant();
        }

        if ( !$this->hasOption('host') ) {
            $this->getFromPhpUnitXml();
        }

        if ( !$this->hasOption('host') ) {
            throw new \RuntimeException("PHP build-in Server can't startet!");
        }

        // Command that starts the built-in web server
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            $this->getOption('host'),
            $this->getOption('port'),
            realpath($this->getOption('docRoot'))
        );

        // Execute the command and store the process ID
        $output = array();
        exec($command, $output);
        $pid = (int) $output[0];

        echo sprintf(
            '%s - Web server started on %s:%d (%s) with PID %d',
            date('r'),
            $this->getOption('host'),
            $this->getOption('port'),
            realpath($this->getOption('docRoot')),
            $pid
        ) . PHP_EOL;

        // Kill the web server when the process ends
        register_shutdown_function(function () use ($pid) {
            echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
            exec('kill ' . $pid);
        });
    }

    protected function getDataFromConstant ()
    {
        if (!defined('WEB_SERVER_HOST')) {
            return false;
        }

        $this->option['host']=WEB_SERVER_HOST;
        $this->option['port']=WEB_SERVER_PORT;
        $this->option['docRoot']=WEB_SERVER_DOCROOT;

    }

    /**
     * @return string
     */
    protected function getPhpUnitXmlFile ()
    {
        $phpunitXmlFiles = array(
            './tests/phpunit.xml',
            './tests/phpunit.xml.dist',
        );
        if ( $this->hasOption('phpunitxml') ) {
            $phpunitXmlFiles= array($this->getOptions('phpunitxml'));
        }

        foreach ($phpunitXmlFiles as $filePath) {
            if (file_exists($filePath)) {
                return $filePath;
            }
        }

        throw new \RuntimeException("phpunit xml file not found, given [" . implode(',', $phpunitXmlFiles));
    }

    protected function getFromPhpUnitXml ()
    {
        $filePath = $this->getPhpUnitXmlFile();
        $phpunitXml = new SimpleXMLElement(file_get_contents($filePath));

        $constMap =array (
            'host'=>'WEB_SERVER_HOST',
            'port'=>'WEB_SERVER_PORT',
            'docRoot'=>'WEB_SERVER_DOCROOT'
        );

        if (isset($phpunitXml->php->const)) {
            foreach ($phpunitXml->php->const as $const) {
                $const = (array) $const;

                if (! in_array($const['@attributes']['name'], $constMap ) ) {
                    continue;
                }

                $this->setOption($const['@attributes']['name'],$const['@attributes']['value']);
            }
        }

    }

    /**
     *
     * @param array $options
     */
    public static function staticRun ($options=array())
    {
        static $that;

        if ($that === null) {
            $that = new static($options);
        }

        $that->start();
    }
}
