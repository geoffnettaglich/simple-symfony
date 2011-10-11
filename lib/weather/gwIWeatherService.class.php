<?php

interface gwIWeatherService
{
  function currentConditions($lat = null, $lon = null);

  function extendedForecast($lat = null, $lon = null);

  function climate($lat = null, $lon = null);
}