<?php

namespace AppBundle\Services;

//api openWeather
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

class WeatherHelper 
{
    public function getForecastWeather($option) {

                                
                // Language of data (try your own language here!):
        $lang = 'es';

        // Units (can be 'metric' or 'imperial' [default]):
        $units = 'metric';

        // Create OpenWeatherMap object. 
        // Don't use caching (take a look into Examples/Cache.php to see how it works).
        $owm = new OpenWeatherMap('1ee6d5b605e1678f54b5e24aa2f32acc');

        try {
            $weather = $owm->getWeather($option, $units, $lang);
        } catch(OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        } catch(\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        }

        return $weather;

    }

}


?>