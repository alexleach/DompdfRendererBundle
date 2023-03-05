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
The kimai-v1 branch has been tested and used with Kimai 1.30.10 (stable), which
is currently what's installed with the kimai/kimai2:fpm docker image.

## Issues

Page numbering in templates can either be positioned manually, with x and y
co-ordinates, using in an inline php script.  See the script at the bottom of
the provided [default template](/Resources/invoices/test-default.pdf.twig).

Alternatively, use css to insert the current page number, and the string
`__TPC__` will be replaced with the total page count. This is how the provided
[awesome template](Resources/invoices/awesome-dompdf.pdf.twig) does it.
Right-alignment to the page margins is tricky though, as `__TPC__` is the
string that will be right-aligned (not the total page count number, which is
what is finally shown).


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

New twig templates can be added to `/Resources/invoices`. Two examples are
provided, one based on Kimai's default template, and the other being a heavily
modified version of the `din5008` invoice template, obtained from the
[kimai/invoice-templates github repository](https://github.com/kimai/invoice-templates).

An invoice generated from the kimai demo data, and using a fake logo from the
[pigment/fake-logo github repository](https://github.com/pigment/fake-logos) is
shown below and full pdf available in the [examples](/examples) directory.

<img src="/examples/awesome-dompdf-template-example.jpg" alt="awesome example invoice" />
