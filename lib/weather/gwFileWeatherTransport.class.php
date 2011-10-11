<?php
class gwFileWeatherTransport implements gwIWeatherTransport
{
  private $params = array();

  public function __construct(array $params = array())
  {
    if(is_null($params))
    {
      $params = array();
    }
    
    $defaults = array(
        'dir' => dirname(__FILE__),
        'prefix' => 'weather-sample-'
    );
    
    $this->params = array_merge($defaults, $params);
  }

  public function retrieveContent(array $params)
  {
    $filename = $this->params['dir'].'/'.$this->params['prefix'].$params['product'].'.xml';
    $contents = file_get_contents($filename);
    return $contents;
  }
}
?>
