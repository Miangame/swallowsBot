<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 27/04/18
 * Time: 17:31
 */

namespace AppBundle\Model;


use Longman\TelegramBot\Botan;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class BotTelegram extends Telegram
{
    protected $version = '0.0.1';

    public $container;

    public function __construct($api_key, $bot_name, $dir_bots, $container = null)
    {
        parent::__construct($api_key, $bot_name);

        if ($container != null) $this->container = $container;

        if (empty($api_key)) {
            throw new TelegramException('API KEY not defined!');
        }

        if (empty($bot_name)) {
            throw new TelegramException('Bot Username not defined!');
        }

        $this->api_key = $api_key;
        $this->bot_name = $bot_name;

        //Set default download and upload path
        $webDir = $container->get('kernel')->getRootDir() . '/../web';
        $this->setDownloadPath($webDir . '/TelegramDownload');
        $this->setUploadPath($webDir . '/TelegramUpload');


        // Add custom Bots
        $this->addCommandsPath($dir_bots);

        Request::initialize($this);


    }

    public function handle()
    {
        $result = parent::handle();

        $post = json_decode($this->input, true);

        $message = $post['message']['text'];

        if (strlen($message) > 1000){
            $message = substr($message, 0, 1000);
        }

        $fecha = new \DateTime();

        $fecha->setTimestamp($post['message']["date"]);

        $chatId = $post['message']['chat']['id'];
        $username = $post['message']["from"]["username"];
        $language = 'es';
        if(array_key_exists('language_code', $post['message']["from"])) {
            $language = $post['message']["from"]["language_code"];
        }
        $groupName = '';
        if(array_key_exists('title', $post['message']['chat'])) {
            $groupName = $post['message']['chat']['title'];
        }

        return $result;
    }

    public function executeCommand($command)
    {
        $command_obj = $this->getCommandObject($command);

        if (!$command_obj || !$command_obj->isEnabled()) {
            //Failsafe in case the Generic command can't be found
            if ($command === 'Generic') {
                // Enabled wit.ai
                return false;
                // throw new TelegramException('Generic command missing!');
            }

            //Handle a generic command or non existing one
            $this->last_command_response = $this->executeCommand('Generic');
        } else {
            //Botan.io integration, make sure only the command user executed is reported
            if ($this->botan_enabled) {
                Botan::lock($command);
            }

            //execute() method is executed after preExecute()
            //This is to prevent executing a DB query without a valid connection
            $this->last_command_response = $command_obj->preExecute();

            //Botan.io integration, send report after executing the command
            if ($this->botan_enabled) {
                Botan::track($this->update, $command);
            }
        }

        return $this->last_command_response;
    }

    public function getCommandObject($command)
    {
        $which = ['System'];
        ($this->isAdmin()) && $which[] = 'Admin';
        $which[] = 'User';

        foreach ($which as $auth) {
            //$command_namespace = __NAMESPACE__ . '\\Commands\\' . $auth . 'Commands\\' . $this->ucfirstUnicode($command) . 'Command';
            $command_namespace = "AppBundle\\BotCommands\\" . $this->ucfirstUnicode($command) . "Command";
            if (class_exists($command_namespace)) {
                return new $command_namespace($this, $this->update, $this->container);
            }
        }

        return null;
    }
}