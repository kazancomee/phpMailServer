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
                    // $jsonData = file_get_contents('php://input');
                    // $data = json_decode($jsonData, true);

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

                    // Görseli PDF'e ekle
                    $pdf->Image($file_tmp, 10, 10, $newWidth, $newHeight, 'JPG');
                    
                    $pdf->SetY($newHeight + 20);
                    $pdf->Cell(0, 10, 'Ad Soyad: ' . $_POST['adSoyad'], 0, 1);
                    $pdf->Cell(0, 10, 'Telefon: ' . $_POST['telefon'], 0, 1);
                    $pdf->Cell(0, 10, 'E-Posta: ' . $_POST['ePosta'], 0, 1);
                    $pdf->Cell(0, 10, 'İkamet Adresi: ' . $_POST['ikametAdresi'], 0, 1);
                    $pdf->Cell(0, 10, 'Ev Tipi: ' . $_POST['evTipi'], 0, 1);
                    $pdf->Cell(0, 10, 'Genişlik: ' . $_POST['genislik'], 0, 1);
                    $pdf->Cell(0, 10, 'Derinlik: ' . $_POST['derinlik'], 0, 1);
                    $pdf->Cell(0, 10, 'Veranda Rengi: ' . $_POST['verandaRengi'], 0, 1);
                    $pdf->Cell(0, 10, 'Üst Cam Tipi: ' . $_POST['ustCamTipi'], 0, 1);
                    $pdf->Cell(0, 10, 'Ön Cam Panel: ' . $_POST['onCamPanel'], 0, 1);
                    $pdf->Cell(0, 10, 'Yan Camlar: ' . $_POST['yanCamlar'], 0, 1);


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