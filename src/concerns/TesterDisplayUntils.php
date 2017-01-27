<?php
trait TesterDisplayUntils
{
    public function display($text, $status)
    {
        if ($this->colorize_output) {
            return Util::colorize($text, $status);
        } else {
            return $text;
        }

    }
}
