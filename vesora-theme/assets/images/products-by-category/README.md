# Luxshine product image archive

Generated on 2026-10-01 from the public WooCommerce Store API at
`https://luxshinehair.com/wp-json/wc/store/v1/products`.

## Contents

- `original/`: 1,220 original featured product images, grouped by product category.
- `vesora/`: 1,220 branded copies using `assets/images/logo-mobile.png`.
- `_manifest/products.csv`: product ID, product name, category, source page, source image URL, and local path.
- `_manifest/products.json`: JSON version of the product manifest.
- `_manifest/branding.csv`: per-image branding result (`replaced` or `added`).
- `_manifest/summary.json`: API and output summary.

The source files are never overwritten. Luxshine marks on white product labels are
replaced with Vesora marks in place. Older catalog photos with a repeated watermark
across the hair receive an opaque Vesora-branded strip over the old watermark so no
Luxshine text remains visible without blurring the hair through generative inpainting.
Photos without a detectable Luxshine mark receive a restrained Vesora mark.

The 13 images directly consumed by the theme in `assets/images/products` are also
updated in place. Their originals are backed up in `assets/images/products-original`.

## Re-run

```powershell
& .\tools\fetch-luxshine-products.ps1 -DownloadImages
& .\.venv-image\Scripts\python.exe .\tools\brand-luxshine-products.py --workers 6 --force
```
