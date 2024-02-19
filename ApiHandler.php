<?php

    require 'MailSender.php';

    class ApiHandler
    {
        public static function handleRequest()
        {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') 
            {
                $requestUrl = isset($_GET['url']) ? $_GET['url'] : '/';
                if ($requestUrl === 'send') 
                {
                    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
                    $pdf->SetFont('dejavusans', '', 20);
                    $pdf->setPrintHeader(false);
                    $pdf->setAutoPageBreak(false, 0);
                    $pdf->AddPage();

                    $pdf->SetFont('dejavusans', 'B', 12);
                    
                    $file_tmp = $_FILES['gorsel']['tmp_name'];
                    list($width, $height) = getimagesize($file_tmp);
                    $maxWidth = 180;
                    $maxHeight = 240;

                    $scaleWidth = $maxWidth / $width;
                    $scaleHeight = $maxHeight / $height;
                    $scale = min($scaleWidth, $scaleHeight);

                    if ($width > $height) {
                        $newWidth = $maxWidth;
                        $newHeight = round($height * $scale);
                    } else {
                        $newWidth = round($width * $scale);
                        $newHeight = $maxHeight;
                    }

                    
                    $pdf->Image($file_tmp, 10, 10, $newWidth, $newHeight, 'JPG');
                    
                    $pdf->SetY($newHeight + 20);

                    $bilgiler = array(
                        'Ad Soyad' => $_POST['adSoyad'],
                        'Telefon' => $_POST['telefon'],
                        'E-Posta' => $_POST['ePosta'],
                        'İkamet Adresi' => $_POST['ikametAdresi'],
                        'Ev Tipi' => $_POST['evTipi'],
                        'Genişlik' => $_POST['genislik'],
                        'Derinlik' => $_POST['derinlik'],
                        'Veranda Rengi' => $_POST['verandaRengi'],
                        'Üst Cam Tipi' => $_POST['ustCamTipi'],
                        'Ön Cam Panel' => $_POST['onCamPanel'],
                        'Yan Camlar' => $_POST['yanCamlar']
                    );                    
                    $html = '<table border="1" cellpadding="5">';
                    foreach ($bilgiler as $baslik => $icerik) {
                        $html .= '<tr><th>' . $baslik . '</th><td>' . $icerik . '</td></tr>';
                    }
                    $html .= '</table>';

                    $pdf->writeHTML($html, true, false, true, false, '');

                    $pdfFilePath = __DIR__ . '/example.pdf';
                    $pdfData = $pdf->Output($pdfFilePath, 'S');

                    $mailSender = new MailSender();
                    $mailSender->sendMail($pdfData);

                } else {
                    echo json_encode(array('status' => 'error', 'message' => 'Geçersiz istek.'));
                }
            }
        }
    }

?>