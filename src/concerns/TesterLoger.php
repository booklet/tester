<?php
trait TesterLoger
{
    /**
     * Logs the result of a test. keeps track of results for later inspection, Overridable to log elsewhere.
     **/
    public function log(TesterResult $result)
    {
        if ($result->status == 'success') {
            echo CLIUntils::colorize('.', 'SUCCESS');
        }

        if ($result->status == 'pending') {
            echo CLIUntils::colorize('P', 'WARNING');
        }

        if ($result->status == 'error') {
            echo CLIUntils::colorize('F', 'FAILURE');
            printf( "\n\nTest: %s was a failure (lines: %d-%d; file: %s)\n\n"
                ,$result->get_name()
                ,$result->get_test()->getStartLine()
                ,$result->get_test()->getEndLine()
                ,$result->get_test()->getFileName()
            );

            printf( "%s\n\n"
                ,$result->exception
            );
        }
    }
}
