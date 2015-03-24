<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.03.2015
 * Time: 18:35
 */


namespace App\Payment;


use App\Model\Order;
use App\Pixie;
use App\Utils\RUtils;
use Endroid\QrCode\QrCode;

class ReceiptService
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var array
     */
    protected $config;

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;

        $this->config = $this->pixie->config->get('parameters.receipt', []);
    }

    /**
     * Generates a QR code from a given string
     *
     * @param $text
     * @return QrCode|null
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     */
    public function renderQRCode($text)
    {
        $qrCode = new QrCode();
        return $qrCode
            ->setText($text)
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->render();
    }

    /**
     * @param number $sum Payment sum in rubles.
     * @param string $purpose Payment purpose
     * @return string
     */
    public function generateStringForQRCode($sum, $purpose)
    {
        return 'ST00012'
            . "|Name=" . $this->config['company_name']
            . "|PersonalAcc=" . $this->config['company_account']
            . "|BankName=" .  $this->config['bank_name']
            . "|BIC=" . $this->config['bank_bic']
            . "|CorrespAcc=" . $this->config['bank_account']
            . "|Sum=" . round($sum * 100)
            . "|Purpose=" . trim($purpose)
            . "|PayeeINN=" . $this->config['company_inn']
            . "|KPP=" . $this->config['company_kpp'];
    }

    /**
     * @param Order $order
     * @return QrCode|null
     * @throws \ErrorException
     */
    public function renderQRCodeForOrder(Order $order)
    {
        if (!$order->loaded()) {
            throw new \ErrorException("Invalid order");
        }

        $df = RUtils::dt();
        $date = new \DateTime($order->created_at);
        $purpose = "Оплата по счету №" . $order->uid . " от " . $df->ruStrFTime([
            'format' => 'j F Y',
            'monthInflected' => true,
            'date' => $date
        ]);

        return $this->renderQRCode($this->generateStringForQRCode($order->amount, $purpose));
    }
}