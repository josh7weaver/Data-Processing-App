<?php namespace DataStaging\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use DataStaging\DataStager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DataStagerRunCommand extends Command implements SelfHandling{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'datastaging:run';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Run the data staging process (import/export & adjusters)";

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $start = Carbon::now();
		$this->comment("Beginning Data Staging Process...");
		$this->comment("See the timestamped log at /log/import_TIMESTAMP.log");

        if($this->hasOptions()){
            // parse options if they exist
            $datastager = new DataStager($this->argument('school_code'), $this->transformOptions());
            $datastager->run();
        } else {
            // else just call datastager with all components enabled
            $datastager = new DataStager($this->argument('school_code'));
            $datastager->run();
        }

		$duration = Carbon::now()->diffForHumans($start, true);
		$this->comment("SUCCESS: Data Staging Complete in $duration. \nFinished at ". date("Y-m-d H:i:s"));
	}

    /**
     * Get the console command options.
     *
     * @return array
     */
	 protected function getOptions()
	 {
	 	return [
	 		['import', 'i', InputOption::VALUE_NONE, 'Run Import Component Only', null],
            ['export', 'e', InputOption::VALUE_NONE, 'Run Export Component Only', null],
            ['adjust', 'a', InputOption::VALUE_NONE, 'Run Adjusters Only', null],
	 	];
	 }

    protected function getArguments()
    {
        return [
          ['school_code', InputArgument::REQUIRED, "The school code for the school you want to run process for"]
        ];
    }

    protected function transformOptions()
    {
        $import = $this->assignOrBlank('import');
        $export = $this->assignOrBlank('export');
        $adjust = $this->assignOrBlank('adjust');

        return array_merge($import, $export, $adjust);
    }

    protected function assignOrBlank($item)
    {
        return $this->option($item) ? [$item] : [];
    }

    protected function hasOptions()
    {
        return $this->option('import') ||
               $this->option('adjust') ||
               $this->option('export');
    }

}
