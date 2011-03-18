<?php

class majaxMediaCommandExecuter
{
  protected $executable = '';

  protected $arguments = array();

  public function __construct($executable, $arguments = array())
  {
    $this->setExecutable($executable);
    $this->setArguments($arguments);
  }

  public function setExecutable($executable)
  {
    if ($executable == false)
    {
      $this->executable = '';
      return;
    }
    if (!file_exists($executable))
      throw new InvalidArgumentException('The executable "'.$executable.'" does not exist.');
    if (!is_executable($executable))
      throw new InvalidArgumentException('The executable "'.$executable.'" is not, in fact, executable.');
    $this->executable = $executable;
  }

  public function setArguments($arguments)
  {
    if (!is_array($arguments))
      throw new InvalidArgumentException('Parameter passed was not an array');
    $this->arguments = array_merge($this->arguments, $arguments);
  }

  public function setArgument($argument)
  {
    $this->arguments[] = strval($argument);
  }

  public function clearArguments()
  {
    $this->arguments = array();
  }

  protected function buildShellCommand()
  {
    $exe = escapeshellcmd($this->executable);
    $params = array();
    foreach($this->arguments as $argument)
    {
      $params[] = escapeshellarg($argument);
    }
    $line = trim($exe.' '.implode(' ', $params));

    return $line;
  }

  public function execute()
  {
    $line = $this->buildShellCommand();
    return $this->doExecute($line);
  }

  protected function doExecute($line)
  {
    return shell_exec($line);
  }
}
