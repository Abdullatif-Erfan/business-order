@extends('layouts.app')

@section('content')

<style>
    .print-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: flex-start;
    }

    .barcode-box {
        width: 130px;
        border: 1px solid #999;
        padding: 10px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .barcode-box img {
        width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .barcode-box p {
        margin: 10px 0 0;
        font-size: 14px;
        text-align: center;
    }

    .pagination-wrapper-custom {
        display: inline-block;
        text-align: center;
        margin-top: 20px;
    }

    .pagination-custom {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .pagination-custom a {
        padding: 8px 16px;
        background-color: #436fa7;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    .pagination-custom a:hover {
        background-color: #2d5b8f;
    }

    .pagination-custom a.active {
        background-color: #2d5b8f;
        font-weight: bold;
    }

    .pagination-custom a.disabled {
        background-color: #e0e0e0;
        color: #9e9e9e;
    }
</style>


<!-- Scripts required for exporting to Word -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html-docx-js/0.4.1/html-docx.min.js"></script>

<script>
    async function exportToWord() {
        const container = document.getElementById("print_area").cloneNode(true);

        // Remove error and pagination messages
        container.querySelectorAll('.text-danger').forEach(el => el.remove());
        container.querySelectorAll('.pagination-wrapper-custom').forEach(el => el.remove());

        // Convert all images to base64
        const images = container.querySelectorAll("img");
        for (let img of images) {
            const base64 = await toDataUrl(img.src);
            img.setAttribute("src", base64);
        }

        const html = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Barcodes</title>
                <style>
                    body {
                        font-family: Tahoma, sans-serif;
                        direction: rtl;
                        text-align: center;
                    }
                    .barcode-box {
                        width: 130px;
                        border: 1px solid #999;
                        padding: 10px;
                        margin: 10px;
                        display: inline-block;
                        text-align: center;
                        page-break-inside: avoid;
                    }
                    .barcode-box img {
                        width: 100px;
                        height: auto;
                        display: block;
                        margin: 0 auto;
                    }
                    .barcode-box p {
                        margin-top: 10px;
                        font-size: 14px;
                    }
                </style>
            </head>
            <body>
                ${container.innerHTML}
            </body>
            </html>
        `;

        const converted = htmlDocx.asBlob(html, { orientation: 'portrait' });
        saveAs(converted, 'barcodes.docx');
    }

    // Helper: Convert image URL to base64
    function toDataUrl(url) {
        return fetch(url)
            .then(response => response.blob())
            .then(blob => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            }));
    }

</script>


<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                        <div class="card-body">

                            <h3 style="margin-bottom: 15px">
                                <a href="{{ route('buyprelist.index') }}" class="pull-left">
                                    <i class="fa fa-arrow-left"></i>
                                </a>
                                لیست بارکدها برای چاپ 
                                   
                                <button onclick="print_page_with_image_grid()"  class="btn btn-info btn-sm pull-left m-l-10">
                                چاپ بارکد - چهارکالم</button>

                                <button onclick="print_page_with_image_grid_single_column()"  class="btn btn-info btn-sm pull-left m-l-10">چاپ بارکد - یک کالم</button>
                                
                                

                                <button onclick="print_page_with_image_grid_single_image_per_page()"  class="btn btn-info btn-sm pull-left m-l-10">چاپ بارکد - یک کالم - یک بارکد</button>
                                

                                <button onclick="print_page_with_image_grid_labelPrinter()"  class="btn btn-info btn-sm pull-left m-l-10">چاپ بارکد توسط لیبل پرنتر</button>

                                <button onclick="exportToWord()" class="btn btn-success btn-sm pull-left m-l-10">اکسپورت به Word</button>

                            </h3>

                            <div class="container p10">
                               <div id="print_area">
                                <div class="row print-grid">
                                    @forelse ($preList as $item)
                                        @if($item->barcode_path)
                                            <div class="barcode-box">
                                                <img src="{{ asset('storage/' . $item->barcode_path) }}" alt="Barcode">
                                                <p>{{ $item->code }}</p>
                                            </div>
                                        @else
                                            <div class="barcode-box">
                                                <span class="text-danger">ندارد</span>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="col-12 text-center text-danger">
                                            هیچ داده‌ای موجود نیست
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            </div>

                            <div class="col-md-12 m-t-20 text-center m-b-20">
                               <!-- pagination -->
                               <div class="pagination-wrapper-custom">
                                    <div class="pagination-custom">
                                        {{-- Previous Button --}}
                                        @if ($preList->onFirstPage())
                                            <span class="disabled page-link">قبلی</span>
                                        @else
                                            <a href="{{ $preList->previousPageUrl() }}" class="page-link">قبلی</a>
                                        @endif

                                        {{-- Page Numbers --}}

                                        {{--  @for ($i = 1; $i <= $preList->lastPage(); $i++)
                                            <a href="{{ $preList->url($i) }}" class="page-link {{ $i == $preList->currentPage() ? 'active' : '' }}">{{ $i }}</a>
                                        @endfor --}}

                                        {{-- Next Button --}}
                                        @if ($preList->hasMorePages())
                                            <a href="{{ $preList->nextPageUrl() }}" class="page-link">بعدی</a>
                                        @else
                                            <span class="disabled page-link">بعدی</span>
                                        @endif
                                    </div>
                               </div>
                               <!-- /pagination -->
                            </div>

                        </div> 
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>


@endsection
