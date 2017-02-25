<?php
trait TesterDisplayUntils
{
    public function display($text, $status)
    {
        if ($this->colorize_output) {
            return CLIUntils::colorizeConsoleOutput($text, $status);
        } else {
            return $text;
        }

    }
}
