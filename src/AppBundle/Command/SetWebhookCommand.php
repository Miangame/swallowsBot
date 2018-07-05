<?php


namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetWebhookCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webhook:set')
            ->setDescription('Levanta el bot')
            ->setHelp('Levanta el bot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $API_KEY = $this->getContainer()->getParameter('telegram_api_key');

        $urlServidor = $this->getContainer()->getParameter('url_server');

        $url = "https://api.telegram.org/bot" . $API_KEY . "/setWebhook?url=" . $urlServidor . "/swallowsBot/web/bot/webhook/" . $API_KEY;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);

        curl_close($ch);
    }
}