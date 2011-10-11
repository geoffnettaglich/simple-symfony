<?php
class gwWeatherService implements gwIWeatherService
{
  const CURRENT  = 'current_conditions';
  const FORECAST = 'expanded_forecast';
  const CLIMATE  = 'one_historical_climate_report';

  private $transport = null;

  function __construct (gwIWeatherTransport $transport)
  {
    $this->transport = $transport;
  }

  function currentConditions($lat = null, $lon = null)
  {
    return $this->performRemoteCall(self::CURRENT, $lat, $lon);
  }

  function extendedForecast($lat = null, $lon = null)
  {
    return $this->performRemoteCall(self::FORECAST, $lat, $lon);
  }

  function climate($lat = null, $lon = null)
  {
    return $this->performRemoteCall(self::CLIMATE, $lat, $lon);
  }

  protected function performRemoteCall($prod, $lat, $lon)
  {
    $out = null;
    
    // we can only proceed if we have some concept of location
    if( $lat && $lon )
    {
      $params = array(
        'product'   => $prod,
        'latitude'  => $lat,
        'longitude' => $lon
      );
  
      $xmlstr = $this->transport->retrieveContent($params);
      $xml = new SimpleXMLElement($xmlstr);
      $out = array();
  
      if(isset($xml->cw_error))
      {
        throw new Exception((string)$xml->cw_error);
      }
  
      if(isset($xml))
      {
        $out['report'] = $this->attributesToArray($xml->attributes());
      }
  
      if(isset($xml->observation))
      {
        foreach($xml->observation as $obs)
        {
          $out['observations'][] = $this->attributesToArray($obs->attributes());
        }
      }
      else if(isset($xml->location))
      {
        if(isset($xml->location->forecast))
        {
          foreach($xml->location as $loc)
          {
            foreach($loc->forecast as $obs)
            {
              $out['forecast'][] = $this->attributesToArray($obs->attributes());
            }
          }
        }
      }
    }

    return $out;
  }

  protected function attributesToArray($attributes)
  {
    $item = array();

    foreach($attributes as $key => $value)
    {
      $item[$key] = (string)$value;
    }

    return $item;
  }
}
?>
