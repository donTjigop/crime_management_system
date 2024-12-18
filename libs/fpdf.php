<?php
// FPDF class definition
class FPDF
{
    // Properties for page setup
    var $page = 0;              // Current page number
    var $n = 0;                 // Total number of pages
    var $buffers = array();     // Pages content buffer
    var $state = 0;             // PDF state
    var $k = 2;                 // Scaling factor
    var $pageWidth = 210;       // Width of the page (A4)
    var $pageHeight = 297;      // Height of the page (A4)
    var $fontFamily = '';       // Current font family
    var $fontStyle = '';        // Current font style
    var $fontSize = 0;          // Current font size
    var $lineWidth = 0;         // Line width
    var $autoPageBreak = true;  // Auto page break flag
    var $bottomMargin = 10;     // Bottom margin for auto page break

    // Constructor: Initialization
    function __construct()
    {
        $this->buffers[0] = '';
        $this->state = 0;  // Initial state
        $this->AddPage();  // Add first page
    }

    // Method to add a new page
    function AddPage()
    {
        $this->page++;
        $this->n++;
        $this->buffers[$this->page] = '';
        $this->state = 0;  // Reset state for a new page
    }

    // Method to set the auto page break
    function SetAutoPageBreak($auto = true, $margin = 10)
    {
        $this->autoPageBreak = $auto;
        $this->bottomMargin = $margin;
    }

    // Method to set font for text
    function SetFont($family, $style, $size)
    {
        $this->fontFamily = $family;
        $this->fontStyle = $style;
        $this->fontSize = $size;
    }

    // Method to output text with specific alignment
    function Cell($width, $height, $text, $border = 0, $ln = 0, $align = 'L')
    {
        $this->buffers[$this->page] .= $text;
    }

    // Method to output the document
    function Output($dest = 'I', $name = '')
    {
        echo $this->buffers[$this->page];
    }

    // Method to add line break
    function Ln($height = 10)
    {
        $this->buffers[$this->page] .= "\n";
    }

    // Check if the page is close to the bottom and add a new page if auto page break is enabled
    function CheckPageBreak($height)
    {
        if ($this->autoPageBreak && ($this->pageHeight - $this->bottomMargin - $height < 0)) {
            $this->AddPage();
        }
    }
}
?>
