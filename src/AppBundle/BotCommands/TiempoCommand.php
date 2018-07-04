<?php
namespace AppBundle\BotCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use Symfony\Bundle\SecurityBundle\Tests\Functional\UserPasswordEncoderCommandTest;

use AppBundle\Services\WeatherHelper;

class TiempoCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'tiempo';
    protected $description = 'Te indicaré la previsión metereológica para el dia de hoy.';
    protected $usage = '/tiempo nombreCiudad';
    protected $version = '0.0.1';
    /**#@-*/


    /**
     * {@inheritdoc}
     */
    public function execute()
    {

        /** @var Message $message */
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $option = trim($message->getText(true));



        $weatherHelper = new WeatherHelper();
        $forecastWeather = $weatherHelper->getForecastWeather($option);
        
        $city = $forecastWeather->city->name;

        $forecast = $forecastWeather->weather->description;
        $acTemp = $forecastWeather->temperature->now;
        $maxTemp = $forecastWeather->temperature->max;
        $minTemp = $forecastWeather->temperature->min;
        $humidity = $forecastWeather->humidity;
        $pressure = $forecastWeather->pressure;
        $windSpeed = $forecastWeather->wind->speed;
        $windDirection = $forecastWeather->wind->direction;
        $clouds = $forecastWeather->clouds;
        $precipitation = $forecastWeather->precipitation;

        dump($forecastWeather);
        

        
        

        $actuallyTemperature = str_replace('&deg;C', "℃", $acTemp );
        $maxTemperature = str_replace('&deg;C', "℃", $maxTemp );
        $minTemperature = str_replace('&deg;C', "℃", $minTemp );

        $text = 'Previsión meteorológica para *' . $city . "*\n" . "\n";
        $text .= ' *Previsión* -> ' . $forecast . "\n" ;
        $text .= ' *TempActual* -> ' . $actuallyTemperature . "\n";
        $text .= ' *TempMax* -> ' . $maxTemperature . "\n";
        $text .= ' *TempMin* -> ' . $minTemperature . "\n";
        $text .= ' *Humedad* -> ' . $humidity . "\n";
        $text .= ' *Presión* -> ' . $pressure . "\n";
        $text .= ' *Viento* -> ' . $windSpeed . ', *Dirección* -> ' . $windDirection . "\n";
        $text .= ' *Nubes* -> ' . $clouds . "\n";
        $text .= ' *Lluvia* -> ' . $precipitation ;
        


        $data = [];
        $data['chat_id'] = $chat_id;
        $data['text'] = $text;
        $data['parse_mode'] = "Markdown";

        return Request::sendMessage($data);
    }

    

  
}