<?php

namespace ArboroGoogleTracking\Subscriber;

use Shopware\Models\Order\Repository;
use Shopware\Models\Order\Order;

/**
 * Class Backend
 * @package ArboroGoogleTracking\Subscriber
 */
class Backend extends AbstractSubscriber
{
    const EVENT = 'Enlight_Controller_Action_PostDispatchSecure_Backend_Order';

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onDispatch($args)
    {
        $viewAssigns = $args->getSubject()
            ->View()
            ->getAssign();
        $data = $viewAssigns['data'];

        /** @var Repository $repository */
        $repository = Shopware()
            ->Models()
            ->getRepository(Order::class);

        $orderBy = [['property' => 'history.changeDate', 'direction' => 'DESC']];
        $history = $repository->getOrderStatusHistoryListQuery($data['id'], $orderBy, 0, 1)
            ->getArrayResult();

        $status = $history[0];
        $statusId = $status['currentOrderStatusId'];

        if($statusId === $this->getConfigElement('stornoStatus') && $statusId !== $status['prevOrderStatusId']) {
            $analyticsData = [
                'v'   => '1',
                'tid' => $this->getConfigElement('trackingID'),
                'cid' => $this->createUUID(),
                't'   => 'event',
                'ec'  => 'Ecommerce',
                'ea'  => 'Refund',
                'ni'  => '1',
                'ti'  => $data['number'],
                'pa'  => 'refund',
            ];

            $content = http_build_query($analyticsData);
            $content = utf8_encode($content);
            $url = 'https://www.google-analytics.com/collect';

            if(function_exists('curl_version')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            } else {
                if(file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
                    file_get_contents($url . $content);
                }
            }
        }
    }

    private function createUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
