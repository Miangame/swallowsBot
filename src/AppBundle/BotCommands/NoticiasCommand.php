<?php

namespace AppBundle\BotCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;
use Symfony\Bundle\SecurityBundle\Tests\Functional\UserPasswordEncoderCommandTest;


class NoticiasCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'noticias';
    protected $description = 'Te indicaré las ultimas noticias de actualidad sobre las categorías disponibles *(deportes, smartphones, tecnología, política)*.';
    protected $usage = '/noticias <categoría>';
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

        $text ='';

        $newsHelper = $this->telegram->container->get('app.newsHelper');

          
        if ($option != null){

            
            $optionLower = strtolower($option);

            if ($optionLower == 'smartphones' ) {
                foreach( $newsHelper->getNews('https://topesdegama.com/feed')->getFeed() as $item ) {
                    $text .= '*' .$item->getTitle() . "*\n";
                    $text .= $item->getLink() . "\n". "\n";
                }
            }else if ($optionLower == 'tecnologia' OR $optionLower == 'tecnología') {
                foreach( $newsHelper->getNews('https://www.redeszone.net/feed/')->getFeed() as $item ) {
                    $text .= '*' .$item->getTitle() . "*\n";
                    $text .= $item->getLink() . "\n". "\n";
                }
            }else if ($optionLower == 'deportes') {
                foreach( $newsHelper->getNews('https://www.mundodeportivo.com/feed/rss/home')->getFeed() as $item ) {
                    $text .= '*' .$item->getTitle() . "*\n";
                    $text .= $item->getLink() . "\n". "\n";
                }
            }else if ($optionLower == 'politica' OR $optionLower == 'política') {
                foreach( $newsHelper->getNews('https://www.lamarea.com/feed/')->getFeed() as $item ) {
                    $text .= '*' .$item->getTitle() . "*\n";
                    $text .= $item->getLink() . "\n". "\n";
                }
            }
        
        }else {
            $text .= 'Error formato del comando mire las ayudas, con el comando /help';
        }
        

        $data = [];
        $data['chat_id'] = $chat_id;
        $data['text'] = $text;
        $data['image'] = "{{ asset('images/clouds.jpg') }}";
        $data['parse_mode'] = "Markdown";

        return Request::sendMessage($data);
    }


}
