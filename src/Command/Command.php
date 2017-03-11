<?php

namespace XhprofFlamegraph\Command;

use XhprofFlamegraph\Exception\InvalidOptionException;
use XhprofFlamegraph\Exception\InvalidProfileDataException;
use XhprofFlamegraph\Profile\Analyzer;
use XhprofFlamegraph\Profile\Parser;
use XhprofFlamegraph\XhprofFlamegraph;

class Command
{
    protected $shortopts = "hf:";
    protected $longopts  = [
        "profile:",
        "metrics:",
        "help",
    ];

    protected $metrics = ['ect', 'ewt', 'ecpu', 'emu', 'epmu'];

    public static function main()
    {
        $command = new static();

        $options = $command->parseOptions();

        if (isset($options['profile'])) {
            $data = file_get_contents($options['profile']);
        } else {
            $data = trim(fgets(STDIN));
        }
        $data = unserialize($data);

        if (!$data){
            throw new InvalidProfileDataException();
        }

        if (!isset($options["metrics"])) {
            $options["metrics"] = 'ewt';
        }
        $parser = new Parser();
        $analyzer = new Analyzer();
        $xhprofFlamegraph = new XhprofFlamegraph($data, $parser, $analyzer, $options["metrics"]);
        $xhprofFlamegraph->show();
    }

    /**
     * @return mixed
     * @throws InvalidOptionException
     */
    public function parseOptions()
    {
        $options = getopt($this->shortopts, $this->longopts);
        $result = [];
        foreach ($options as $option => $value) {
            switch ($option){
                case "help":
                case "h":
                    $this->showHelp();
                    exit(1);
                    break;
                case "profile":
                case "f":
                    if (!file_exists($value)) {
                        throw new InvalidOptionException("profile is not found.");
                    }
                    $result["profile"] = $value;
                    break;
                case "metrics":
                    if (in_array($value, $this->metrics) === false){
                        throw new InvalidOptionException("metrics option is given invalid value. ".$value." is given.");
                    }
                    $result["metrics"] = $value;
                    break;
                default:
                    break;
            }
        }
        return $result;
    }

    protected function showHelp()
    {
        echo <<<HELP
usage: xhprof-flamegraph [-h, --help] [--f, --profile] [--metrics]
options:
    -h, --help      show help
    -f, --profile   file path of xhprof profile data
    --metrics       select target metrics (ect/ewt/ecpu/emu/epmu)

HELP;
    }
}
