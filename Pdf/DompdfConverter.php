<?php

namespace KimaiPlugin\DompdfRendererBundle\Pdf;

use App\Constants;
use App\Utils\FileHelper;
use App\Pdf\HtmlToPdfConverter;

use Dompdf\Dompdf;
use Dompdf\Options;


final class DompdfConverter implements HtmlToPdfConverter
{
    const TOTAL_PAGE_COUNT_PLACEHOLDER = "__TPC__";

    public function __construct(private FileHelper $fileHelper, private string $cacheDirectory)
    {
    }

    /**
     * @param string $html
     * @param array $options
     * @return string
     * @throws \Dompdf\DompdfException
     */
    public function convertToPdf(string $html, array $options = []): string
    {
        $options['tempDir'] = $this->cacheDirectory;
        $options['isPhpEnabled'] = true;
        $options['isRemoteEnabled'] = true;

        if (\array_key_exists('fonts', $options)) {
            $options['fontDir'] = $this->fileHelper->getDataDirectory('fonts');
        }

        $dompdfOptions = new Options($options);
        $dompdf = new Dompdf($dompdfOptions);
        $dompdf->addInfo("Creator", Constants::SOFTWARE);

        // some OS'es do not follow the PHP default settings
        if ((int) \ini_get('pcre.backtrack_limit') < 1000000) {
            @ini_set('pcre.backtrack_limit', '1000000');
        }

        // large amount of data take time
        @ini_set('max_execution_time', '120');

        $dompdf->loadHtml($html);

        // Set default paper size and orientation.
        // There has to be a better way to allow configuration of this?
        $dompdf->setPaper('A4', 'portrait');

        // Render as PDF
        $dompdf->render();

        // Replace __TPC__ with the total number of pages.
        // N.B. We only know the page count after the pdf is rendered.
        $this->injectPageCount($dompdf);

        // Output to stream
        return $dompdf->output($options);
    }

    // Replace __TPC__ with the total number of pages
    // Taken from https://github.com/vianetz/pdf-generator/commit/26cce3805fcea8254e45adda30e379444064701e
    private function injectPageCount(Dompdf $dompdf)
    {
        $canvas = $dompdf->getCanvas();
        $pdf = $canvas->get_cpdf();
        $n_pages = $canvas->get_page_count();

        foreach ($pdf->objects as &$o) {
            if ($o['t'] === 'contents') {
                $o['c'] = str_replace(
                    self::TOTAL_PAGE_COUNT_PLACEHOLDER, $n_pages, $o['c']
                );
            }
        }
    }
}

