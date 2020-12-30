<?php
/**
 * BabiPHP\Error - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace BabiPHP\Error\Handler;

use BabiPHP\Error\Exception\Inspector;
use BabiPHP\Error\RunInterface;

interface HandlerInterface
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param  RunInterface  $run
     * @return void
     */
    public function setRun(RunInterface $run);

    /**
     * @param  \Throwable $exception
     * @return void
     */
    public function setException($exception);

    /**
     * @param  Inspector $inspector
     * @return void
     */
    public function setInspector(Inspector $inspector);
}
