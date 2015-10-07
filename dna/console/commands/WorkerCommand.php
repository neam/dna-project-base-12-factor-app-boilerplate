<?php

class WorkerCommand extends CConsoleCommand
{

    use DnaConsoleCommandTrait;

	public function actionFoo($pageSize = 5, $currentPage = 1)
	{
        $this->status("Running foo worker command...");
        echo "\n";
        // insert action here
        $this->status("Done execution with pageSize $pageSize, current page $currentPage. Memory usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MiB");
        echo "\n";
    }

}