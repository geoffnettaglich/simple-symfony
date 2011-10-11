<?php
class gwHttpWeatherTransport implements gwIWeatherTransport
{
  private $urlBase = 'http://xml.customweather.com/xml?';

  private $defaultParams = array();

  private $timeout = 5; // seconds

  public function __construct(array $params = array())
  {
    if(is_null($params) || empty($params) )
    {
      $params = array(
        'client' => 'blastradius_test',
        'client_password' => 't3mp'
      );
    }

    $this->defaultParams = $params;
  }

  public function retrieveContent(array $params)
  {
    $queryString = http_build_query(array_merge($this->defaultParams, $params));
    $url = $this->urlBase.$queryString;
    $s = curl_init();
    curl_setopt($s,CURLOPT_URL,$url);
    curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($s,CURLOPT_TIMEOUT,$this->timeout);
    $response = curl_exec($s);
    $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
    curl_close($s);
    return $response;
  }
}
?>
