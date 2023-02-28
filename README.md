# DompdfRendererBundle

A Kimai plugin to use Dompdf as PDF renderer (in place of MPdf)

## Requirements

- Requires Kamai 2

## Features

- PDFs created by [dompdf](https://github.com/dompdf/dompdf)
- Much smaller PDFs
- Improved support for CSS 2.1 in templates

## Status

The bundle is in early stages, but appears to be working well.
Not yet tested on release versions of Kimai.

## Issues

Page numbering in templates needs to be positioned manually, with x and y co-ordinates, in an inline php script, due to how Dompdf counts the total number of pages.
See the script at the bottom of the provided [default template](/Resources/invoices/test-default.pdf.twig)


## Installation

First unzip the plugin into to your Kimai `plugins` directory:

```bash
unzip DompdfRendererBundle-x.x.zip -d <kimai path>/var/plugins/
```

And then reload Kimai:

```bash
bin/console kimai:reload
```

DompdfRendererBundle has one dependency (i.e. dompdf), which I thought should
be installed automatically with the above command, but for some reason dompdf
wasn't installing automatically for me. So, while in the top level folder, run
the following command to install dompdf:

```bash
composer require dompdf/dompdf
```


The plugin should appear, and will now be used as the PDF renderer instead of MPdf.

To disable the dompdf backend, either:-

 - Edit [Resources/config/dompdf_config.yaml](/Resources/config/dompdf_config.yaml), changing the `renderer` value to `mpdf` (or anything other than `dompdf`)
 - Set the environment variable `KIMAI_PDF_RENDERER` to something (anything other than `dompdf`).
 
 Then, reload Kimai again:
 
 ```bash
 bin/console kimai:reload
 ```

## Templates

New twig templates can be added to `/Resources/invoices`. An example is provided, based on Kimai's default template.

