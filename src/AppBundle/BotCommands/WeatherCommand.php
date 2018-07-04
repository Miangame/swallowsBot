<?php
namespace AppBundle\BotCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use Symfony\Bundle\SecurityBundle\Tests\Functional\UserPasswordEncoderCommandTest;

use AppBundle\Services\WeatherHelper;

class WeatherCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'weather';
    protected $description = 'Te indicaré la previsión metereológica para los proximos dias.';
    protected $usage = '/weather';
    protected $version = '0.0.1';
    /**#@-*/


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $weatherHelper = new WeatherHelper();
        $forecastWeather = $weatherHelper->getForecastWeather();
        
        $city = $forecastWeather->city->name;

        $forecast = $forecastWeather->weather->description;
        $acTemp = $forecastWeather->temperature->now;
        $maxTemp = $forecastWeather->temperature->max;
        $minTemp = $forecastWeather->temperature->min;
        $humidity = $forecastWeather->humidity;
        $pressure = $forecastWeather->pressure;
        $wind = $forecastWeather->wind->speed;
        $clouds = $forecastWeather->clouds;
        $precipitation = $forecastWeather->precipitation;

        dump($forecastWeather);
        

        /** @var Message $message */
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        


        $text = 'Previsión meteorológica para la ciudad de ' . $city . "\n" . "\n";
        $text .= ' Prevision -> ' . $forecast . "\n" ;
        $text .= ' TempActual -> ' . $acTemp . "\n";
        $text .= ' TempMax -> ' . $maxTemp . "\n";
        $text .= ' TempMin -> ' . $minTemp . "\n";
        $text .= ' humedad -> ' . $humidity . "\n";
        $text .= ' presión -> ' . $pressure . "\n";
        $text .= ' viento -> ' . $wind . "\n";
        $text .= ' nubes -> ' . $clouds . "\n";
        $text .= ' probabilidad de lluvia -> ' . $precipitation ;
        


        $data = [];
        $data['chat_id'] = $chat_id;
        $data['text'] = $text;
        $data['parse_mode'] = "Markdown";

        return Request::sendMessage($data);
    }

    

  
}