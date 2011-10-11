<?php
require_once dirname(__FILE__).'/../../../../bootstrap/unit.php';

class unit_gwWeatherServiceTest extends sfPHPUnitBaseTestCase
{
  private $forecastXml = '<report city_name="Test" latitude="55" longitude="25"><location city="50039" city_name="TEST (local XML)" latitude="25.26"
		longitude="55.31" location="TEST (local XML)" state="" state_name=""
		country="TC" country_name="United Arab Emirates" distance="18.17">
		<forecast day_sequence="1" day_of_week="3" weekday="Tuesday"
			daylight="D" date="051011" iso8601="2011-05-10T00:00:00.00+04:00"
			high_temp="37.02" low_temp="28.97" sky_desc="1" sky="Sunny"
			precip_desc="*" precip="" temp_desc="12" temp="Extremely hot"
			air_desc="14" air="Windy" description="Sunny. Extremely hot."
			uv_index="10" uv="Extreme" wind_speed="36.85" wind_dir="260"
			wind_short="W" wind_long="West" humidity="31" dew_point="17.84"
			comfort="39.13" rainfall="*" snowfall="*" precip_prob="0" icon="1"
			icon_name="sunny" beaufort="6" beaufort_desc="Strong breeze"
			baro_pressure="1000.00" />
			</location>
			</report>';
  
  private $currentXml  = '<report city_name="Test" latitude="55" longitude="25"></report>';
  private $climateXml  = '<report city_name="Test" latitude="55" longitude="25" name="historical_climate_report" >
  		<observation city_name="Test" location="Dubai International Airport" latitude="25.25" longitude="55.33" month="1" avg_high_temperature="22.2" avg_low_temperature="14.4" mean_temperature="18.8" typical_sky_cover="SCT" avg_precipitation="*" num_precip_days="5" />
  	</report>';
  
  public function testCurrentConditions()
  {
    $transport = $this->mockTransport($this->currentXml);
    $service = new gwWeatherService($transport);
    $result = $service->currentConditions(55, 25);
    
    $this->checkReport($result);
  }

  public function testExtendedForecast()
  {
    $transport = $this->mockTransport($this->forecastXml);
    $service = new gwWeatherService($transport);
    $result = $service->extendedForecast(55, 25);
    
    $this->checkReport($result);
  }
  
  public function testClimate()
  {
    $transport = $this->mockTransport($this->climateXml);
    $service = new gwWeatherService($transport);
    $result = $service->climate(55, 25);
    
    $this->checkReport($result);
    $this->assertEquals('1',    $result['observations'][0]['month']);
    $this->assertEquals('14.4', $result['observations'][0]['avg_low_temperature']);
    $this->assertEquals('22.2', $result['observations'][0]['avg_high_temperature']);
    $this->assertEquals('18.8', $result['observations'][0]['mean_temperature']);
  }
  
  protected function checkReport($result)
  {
    $this->assertEquals('Test', $result['report']['city_name']);
    $this->assertEquals(55,     $result['report']['latitude']);
    $this->assertEquals(25,     $result['report']['longitude']);
  }
  
  protected function mockTransport($value)
  {
    $transport = $this->getMock('gwIWeatherTransport');
    $transport->expects($this->any())
              ->method('retrieveContent')
              ->will($this->returnValue($value));
    return $transport;
  }
}