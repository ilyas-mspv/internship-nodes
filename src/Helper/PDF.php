<?php


namespace App\Helper;
use App\Dto\NodeDto;
use Fpdf\Fpdf;

class PDF extends FPDF
{

    function generateTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');

        // Header
        $w = array(30, 30, 50, 50);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();

        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Data
        $fill = false;
        foreach($data as $row)
        {
            /**
             * @var $row NodeDto
             */
            $this->Cell($w[0],6,number_format($row->id),'LR',0,'C',$fill);
            $this->Cell($w[1],6,number_format($row->parentId),'LR',0,'C',$fill);
            $this->Cell($w[2],6,$row->name,'LR',0,'C',$fill);
            $this->Cell($w[3],6,$row->createdAt,'LR',0,'C',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }

}