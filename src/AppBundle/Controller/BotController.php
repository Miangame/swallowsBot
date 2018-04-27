<?php
/**
 * Created by PhpStorm.
 * User: miguel
 * Date: 27/04/18
 * Time: 16:56
 */

namespace AppBundle\Controller;

use AppBundle\Model\BotTelegram;
use Longman\TelegramBot\Exception\TelegramException;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BotController
 * @package AppBundle\Controller
 * @Route("/bot")
 */
class BotController extends Controller
{
    /**
     * @Route("/tryBot", name="bot_try")
     * @param Request $request
     */
    public function tryBotAction(Request $request)
    {
        $API_KEY = $this->getParameter('telegram_api_key');

        if ($request->get('code') != $API_KEY) {
            return new JsonResponse("Access Denied");
        }

        $BOT_NAME = $this->getParameter('telegram_bot_name');
        try {
            // Create Telegram API object
            $dir_bots = $this->getParameter('kernel.root_dir') . '/../src/AppBundle/BotCommands';
            $telegram = new BotTelegram($API_KEY, $BOT_NAME, $dir_bots, $this->container);
        } catch (TelegramException $e) {

        }

        $chatId = $request->get('chatId');
        $text = $request->get('text');

        if (true) {
            $customInput = array(
                'update_id' => 87842865,
                'message' => array(
                    "message_id" => 100,
                    'from' => array(
                        'id' => $chatId,
                        'first_name' => 'Swallows',
                        'last_name' => 'Swallows',
                        'username' => '@swallows'
                    ),
                    'chat' => array(
                        'id' => $chatId,
                        'first_name' => 'Swallows',
                        'last_name' => 'Swallows',
                        'username' => '@swallows',
                        'type' => 'private'
                    ),
                    'date' => 1476179342,
                    'text' => $text
                )
            );

            $telegram->setCustomInput(json_encode($customInput));

            // Handle telegram webhook request
            $result = $telegram->handle();

            return new JsonResponse($result);
        }
        return new JsonResponse("Access Denied");
    }

    /**
     * @Route("/webhook/{code}", name="bot_webhook")
     */
    public function webhookAction($code)
    {
        $API_KEY = $this->getParameter('telegram_api_key');
        if ($code != $API_KEY) {
            die("Auth");
        }
        $BOT_NAME = $this->getParameter('telegram_bot_name');
        try {
            // Create Telegram API object
            $dir_bots = $this->getParameter('kernel.root_dir') . '/../src/AppBundle/BotCommands';
            $telegram = new BotTelegram($API_KEY, $BOT_NAME, $dir_bots, $this->container);

            // Handle telegram webhook request
            $telegram->handle();
        } catch (TelegramException $e) {
            /** @var Logger $telegramLogger */
            $telegramLogger = $this->get("monolog.logger.telegram");
            $telegramLogger->info("Ha ocurrido un error: ".$e->getMessage());
        }

        return new JsonResponse([
            'ok' => true
        ]);
    }
}