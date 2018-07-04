<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 27/04/18
 * Time: 17:58
 */

namespace AppBundle\BotCommands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Request;

class HelpCommand extends UserCommand
{
    protected $name = 'help';
    protected $description = 'Muestra los comandos disponibles';
    protected $usage = '/help or /help <command>';
    protected $version = '1.0.1';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $message_id = $message->getMessageId();
        $command = trim($message->getText(true));
        $nickTelegram = $message->getFrom()->getUsername();

        // Get Commands
        $commands = array();
        $allCommands = $this->telegram->getCommandsList();

        foreach ($allCommands as $key => $value) {
            $commands[$key] = $value;
        }

        //If no command parameter is passed, show the list
        if ($command === '') {
            $text = $message->getBotUsername() . ' v. ' . $this->telegram->getVersion() . "\n\n";

            $text .= '*¡Hola ' . $nickTelegram . '! Al habla tu bot favorito!*' . "\n";
            $text .= '_Aquí tienes la lista de comandos con todo lo que puedo hacer_.' . "\n";
            $text .= "\n";
            foreach ($commands as $command) {
                $text .= '/' . $command->getName()  . '  *¿Cómo se usa? -> ' .  $command->getUsage() . '* - ' . $command->getDescription() . "\n" . "\n";
            }

            $text .= "\n" . 'Para saber que hace un comando: /help <comando>';
        } else {
            $command = str_replace('/', '', $command);
            if (isset($commands[$command])) {
                $command = $commands[$command];
                $text = 'Comando: ' . $command->getName() . ' v' . $command->getVersion() . "\n";
                $text .= 'Descripción: ' . $command->getDescription() . "\n";
                $text .= 'Uso: ' . $command->getUsage();
            } else {
                $text = 'No hay ayuda disponible: Comando /' . $command . ' no encontrado';
            }
        }

        $data = [
            'chat_id' => $chat_id,
            'reply_to_message_id' => $message_id,
            'text' => $text,
            'parse_mode' => "Markdown"
        ];

        return Request::sendMessage($data);
    }
}