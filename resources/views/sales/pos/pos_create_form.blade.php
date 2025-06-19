@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif


<script>
function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
    var content = {};
    content.message = '<span style="font-size:16px;">' + message + '</span>';
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
    if (style === "withicon") {
        content.icon = 'fa fa-bell';
    } else {
        content.icon = 'none';
    }
    content.url = '#';
    content.target = '_blank';

    $.notify(content, {
        type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
        placement: {
            from: from, // top, bottom
            align: align // right, center, left
        },
        time: 500
    });
}
</script>
<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 5px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}

</style>
<style>
    .warehouse-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #ddd;
        transition: transform 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        height: 100%;
        cursor:pointer;
    }

    .warehouse-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        cursor:pointer;
    }

    .card-image {
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background: #f8f8f8;
        cursor:pointer;
    }

    .card-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        cursor:pointer;
    }

    .card-body {
        padding: 5px !important;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor:pointer;
    }
    .card-body .price, .amount {
        font-size:12px;
    }
    .item-title {
        font-size: 11px;
        font-weight: 600;
        color: #0513ad;
        line-height: 14px;
    }

    .w-100 {
        width: 100%;
    }

    .badge-group {
        display: flex;
        flex-direction: row;
        gap: 2px;
        align-items: center;
        justify-content: space-between;
    }

    .badge {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 4px;
    }
    .bordered-badge {
        padding: 1px 3px;
        font-size: 10px;
        border: 1px solid #4791e2 !important;
        color: #0440d9;
        background: #fff;
        border-radius: 5px;
    }

    /* selected cards */
    .warehouse-selected-card {
        display: flex;
        flex-direction: row;
    }
    .selected-card-image {
        width: 100%;
        aspect-ratio: 4 / 1;
        overflow: hidden;
        cursor:pointer;
    }
    .selected-card-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        cursor:pointer;
        border-radius: 5px;
    }
    .border-bottom {
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .custom-number-list {
    list-style: none;
    padding: 0;
    counter-reset: item;
    width: 100%;
}

.custom-number-list li {
    position: relative;
    margin-bottom: 20px;
}

.circle-number {
    position: absolute;
    top: 36%;
    right: 5px;
    transform: translateY(-50%);
    width: 25px;
    height: 25px;
    line-height: 24px;
    text-align: center;
    background-color: #068bd3;
    color: white;
    border-radius: 50%;
    font-weight: bold;
    font-size: 11px;
}

.total-prices-group {
    display:flex;
    align-items:center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    gap: 3px;
    padding: 4px;
}

</style>



<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title">فورم فروشات  
                                <span class="pull-left">
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default"> برگشت به لیست </button>
                                    </a>
                                </span>
                                
                            </h4>
                        </div>

                        <form id="pos_form" method="POST">
                        @csrf
                        <input type="hidden"  class="form-control" value="{{ $ownBanks->first()->id }}" name="from_account_id" >


                        

                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                            <div class="form-body" style="padding: 0px 0px 15px !important;">
                                <div class="row" style="padding: 10px 20px;">

                                  <div class="d-none" class="company_profile">
                                    <img src="{{ asset($orgbios[0]->logos) }}" id="image_url" alt="navbar brand" class="navbar-brand" style="width: 100px !important;">

                                    <div id="company_name">{{ $orgbios[0]->name }}</div>
                                    <div id="company_address">{{ $orgbios[0]->address }}</div>
                                    <div id="company_phone">{{ $orgbios[0]->phone }}</div>
                                  </div>


                                   
                                   <!-- right (list of items) -->
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                         <div class="col-xs-12">
                                            
                                            <div class="row">
                                                    <div class="col-md-8 col-sm-6 col-xs-12" >
                                                        <input type="text" name="search" placeholder="جستجو به اساس نام / بارکد" 
                                                         class="form-control m-t-10" oninput="searchByName(this.value)">
                                                    </div>

                                                    <div class="col-md-4 col-sm-6 col-xs-12" style="padding: 10px;">
                                                        <select class="form-control select2 col-12" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="currency_id" id="currency_id" required>
                                                            @foreach($currencies as $currency)
                                                                <option value="{{ $currency->id }}">  {{ $currency->name }} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                            </div>
                                           
                                         </div>


                                         <div class="col-sm-12 col-md-12 col-xs-12 border m-t-10" style="background:#f5f3f3; min-height: 300px;">
                                        
                                            <div id="loading-state" class="text-center py-4">
                                                <i class="fa fa-spinner fa-spin" style="font-size:40px;"></i>
                                                <p class="mt-2">در حال بارگذاری اقلام...</p>
                                            </div>
                                            <div id="item_card" class="row m-t-20" style="display: none;"></div>
                                            <div id="error-state" class="text-center py-4" style="display: none;">
                                                <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                                                <p class="mt-2 text-danger">خطا در بارگذاری داده‌ها</p>
                                                <button class="btn btn-sm btn-primary mt-2" onclick="loadItems()">
                                                    <i class="fas fa-redo"></i> تلاش مجدد
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                  <!-- / right -->

                                  <!-- left (added order items) -->
                                    <div class="col-md-4 col-sm-4 col-xs-12 border">
                                    <div class="row">
                                     <div class="col-md-12 col-sm-12 col-xs-12" style="background-color:#f3f8fa; padding: 10px; border-bottom: 1px solid #dcdcdc;">
                                         <select class="form-control select2 col-12" tabindex="0" style="width: 100%; border:none !important; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                            <option value=""> انتخاب مشتری </option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">  {{ $customer->name }} </option>
                                            @endforeach
                                        </select>
                                      </div>    
                                    </div>

                                      <div class="row m-t-20">

                                     <!-- This is where the visual list appears -->
                                    <ol class="custom-number-list" id="order-items-list"></ol>
                                    
                                    <!-- Hidden container for form data -->
                                    <div id="order-items-data" style="display:none;"></div>

                                       <div class="total col-sm-12 col-xs-12">
                                           <div class="total-prices-group">
                                                <div class="total">قیمت مجموعی:</div>
                                                <input type="number" id="total-price" name="total" style="width:80px;" 
                                                required readonly>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">مفاد:</div>
                                                <input type="number" id="total-profit" name="profit" style="width:80px;" 
                                                required readonly >
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">تخفیف:</div>
                                                <input type="number" id="discount" style="width:80px;" name="discount" value="0" 
                                                required oninput="recalculateAfterDiscount(this.value)">
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">قابل پرداخت:</div>
                                                <input type="number" id="payable" style="width:80px;" name="payable"  required>
                                           </div>
                                           <div class="total-prices-group">
                                                <div class="total">دریافت فعلی:</div>
                                                <input type="number" id="cur_pay" style="width:80px;" name="cur_pay"  required>
                                           </div>
                                           <div class="total-prices-group m-t-10">
                                                <button type="button" id="test_print"  class=" btn btn-warning btn-sm form-control"><i class="fas fa-print"></i></button>
                                                 <button type="button" id="submit_and_print"  class=" btn btn-success btn-sm form-control">ثبت و پرنت</button>
                                                 <button type="button" id="submit"  class=" btn btn-info btn-sm form-control">ثبت</button>
                                           </div>
                                       </div>


                                        </div>
                                    </div>
                                <!-- /left -->
                            

                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let searchDebounceTimer;
function searchByName(searchTerm) {
    // Clear previous timer if it exists
    clearTimeout(searchDebounceTimer);
    
    // Only search if term has at least 1 characters or is empty (to reset)
    if (searchTerm.length === 0 || searchTerm.length >= 1) {
        // Set a new timer
        searchDebounceTimer = setTimeout(() => {
            performSearch(searchTerm);
        }, 300); // 300ms delay
    }
}

// findout searched items
function performSearch(searchTerm) {
    // Show loader, hide content and error states
    $('#loading-state').show();
    $('#item_card').hide();
    $('#error-state').hide();
    $('#empty-state').hide(); // Hide empty state initially
    
    $.ajax({
        url: '/sales/item_list',
        type: 'GET',
        data: { search: searchTerm },
        timeout: 300000,
        beforeSend: function() {
            $('#loading-state').show();
        },
        success: function(response) {
            $('#loading-state').hide();
            
            // Check if response contains any items
            if ($(response).find('.warehouse-card').length > 0) {
                $('#item_card').html(response).fadeIn();
                $('#empty-state').hide();
            } else {
                $('#item_card').html('مواردی یافت نشد').fadeIn();
                $('#empty-state').fadeIn();
            }
        },
        error: function(xhr, status, error) {
            $('#loading-state').hide();
            
            if (status === 'timeout') {
                $('#error-state p').text('زمان بارگذاری به پایان رسید');
            } else {
                $('#error-state p').text('خطا در ارتباط با سرور');
            }
            
            $('#error-state').fadeIn();
            console.error('AJAX Error:', status, error);
        }
    });
}

//  Load Items's first
function loadItems() {
    // Show loader, hide content and error states
    $('#loading-state').show();
    $('#item_card').hide();
    $('#error-state').hide();
    
    $.ajax({
        url: '/sales/item_list',
        type: 'GET',
        timeout: 300000, // 60 seconds timeout
        beforeSend: function() {
            // This runs right before AJAX call
            $('#loading-state').show();
        },
        success: function(response) {
            $('#item_card').html(response).fadeIn();
            $('#loading-state').hide();
        },
        error: function(xhr, status, error) {
            $('#loading-state').hide();
            
            if (status === 'timeout') {
                $('#error-state p').text('زمان بارگذاری به پایان رسید');
            } else {
                $('#error-state p').text('خطا در ارتباط با سرور');
            }
            
            $('#error-state').fadeIn();
            
            // Log error for debugging
            console.error('AJAX Error:', status, error);
        },
        complete: function() {
            // This runs after success/error
            // Could add analytics tracking here
        }
    });
}

// Initialize on page load
$(document).ready(function() {
    loadItems();
    
    // Alternatively, if using Turbolinks:
    // $(document).on('turbolinks:load', loadItems);
});
</script>

<script>

function clearSelectedItems() {
    // Clear the orderItems array
    orderItems = [];
    
    // Clear the visual list
    document.getElementById('order-items-list').innerHTML = '';
    
    // Clear the hidden form inputs
    document.getElementById('order-items-data').innerHTML = '';
    
    // Reset all total fields
    $('#total-price').val('0.00');
    $('#total-profit').val('0.00');
    $('#discount').val('0');
    $('#payable').val('0.00');
    $('#cur_pay').val('0.00');

     // Reset customer selection
     $('#customer_account_id').val('').trigger('change');
    
    // Reset currency selection (if needed)
    // $('#currency_id').val($('#currency_id option:first').val()).trigger('change');
    
    // Reset search input
    $('input[name="search"]').val('');
    
    // Reload items to show all available items again
    // loadItems();
}

let orderItems = [];

function addItem(id) {
    // Find the clicked item card
    const itemCard = $(`[data-item-id="${id}"]`);
    
    if (!itemCard.length) return;

    // Extract item data from data attributes
    const item = {
        id: id,
        pre_list_id: itemCard.data('pre-list-id'),
        sell_up: parseFloat(itemCard.data('sell-up')),
        avg_up: parseFloat(itemCard.data('avg-up')),
        item_name: itemCard.data('item-name'),
        image_path: itemCard.data('image-path'),
        unit_name: itemCard.data('unit-name'),
        warehouse_id: itemCard.data('warehouse-id'),
        unit_id: itemCard.data('unit-id'),
        available_amount: parseInt(itemCard.data('available-amount'))
    };

    // Check if item already exists in order
    const existing = orderItems.find(i => i.id === id);
    if (existing) {
        // Check if we can add more of this item
        if (existing.qty >= item.available_amount) {
            showNotification(`حداکثر مقدار موجود برای ${item.item_name} ${item.available_amount} ${item.unit_name} است`, 'warning');
            return;
        }
        existing.qty += 1;
    } else {
        // Check if item is available
        if (item.available_amount < 1) {
            showNotification(`${item.item_name} در انبار موجود نمی باشد`, 'warning');
            return;
        }
        orderItems.push({
            ...item,
            qty: 1
        });
    }
    renderOrderItems();
}

function removeItem(id) {
    orderItems = orderItems.filter(i => i.id !== id);
    renderOrderItems();
}

function renderOrderItems() {
    const list = document.getElementById('order-items-list');
    const dataContainer = document.getElementById('order-items-data');
    list.innerHTML = '';
    dataContainer.innerHTML = '';
    
    let total = 0;
    let profit = 0;

    orderItems.forEach((item, index) => {
        const itemTotal = item.sell_up * item.qty;
        const itemCost = item.avg_up * item.qty;
        const itemProfit = itemTotal - itemCost;
        
        total += itemTotal;
        profit += itemProfit;

        const itemInputs = `
            <input type="hidden" name="items[${index}][id]" value="${item.id}">
            <input type="hidden" name="items[${index}][pre_list_id]" value="${item.pre_list_id}">
            <input type="hidden" name="items[${index}][amount]" value="${item.qty}">
            <input type="hidden" name="items[${index}][sell_up]" value="${item.sell_up}">
            <input type="hidden" name="items[${index}][avg_up]" value="${item.avg_up}">
            <input type="hidden" name="items[${index}][profit]" value="${itemProfit.toFixed(2)}">
            <input type="hidden" name="items[${index}][total]" value="${itemTotal.toFixed(2)}">
            <input type="hidden" name="items[${index}][warehouse_id]" value="${item.warehouse_id}">
            <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
        `;
        dataContainer.insertAdjacentHTML('beforeend', itemInputs);

        const li = document.createElement('li');
        li.innerHTML = `
            <div class="col-md-12 col-sm-12 mb-2 px-2 border-bottom position-relative">
                <div class="warehouse-selected-card">
                    <div class="selected-card-image col-sm-3 col-md-4 col-xs-12">
                        <img src="/storage/${item.image_path}" alt="${item.item_name}">
                    </div>
                    <div class="col-sm-9 col-md-8 col-xs-12">
                        <h5 class="item-title">${item.item_name}
                            <span class="pull-left" onclick="removeItem(${item.id})" style="cursor:pointer;">
                                <i class="fa fa-trash text-danger"></i>
                            </span>
                        </h5>
                        <div class="badge-group">
                            <input type="number" value="${item.qty}" style="width:50px;" 
                                   onchange="updateQty(${item.id}, this.value)" min="1">
                            <small class="text-info" style="font-size:10px">${item.unit_name}</small>
                            <span class="price">مجموع: ${itemTotal.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                <div class="circle-number">${index + 1}</div>
            </div>
        `;
        list.appendChild(li);
    });

    let discount = parseFloat($('#discount').val()) || 0;
    let totalFixed = parseFloat(total.toFixed(2));
    let payable = totalFixed - discount;

    $('#total-price').val(totalFixed.toFixed(2));
    $('#total-profit').val(profit.toFixed(2));
    $('#payable').val(payable > 0 ? payable.toFixed(2) : '0.00');
    $('#cur_pay').val(payable > 0 ? payable.toFixed(2) : '0.00');
}

function updateQty(id, newQty) {
    const item = orderItems.find(i => i.id === id);
    if (item) {
        newQty = parseInt(newQty);
        
        // Find the original item card to check available amount
        const itemCard = $(`[data-item-id="${id}"]`);
        const availableAmount = parseInt(itemCard.data('available-amount'));
        
        if (newQty > availableAmount) {
            showNotification(`حداکثر مقدار موجود ${availableAmount} ${item.unit_name} است`, 'danger');
            // Reset to previous value
            $(event.target).val(item.qty);
            return;
        }
        
        if (newQty < 1) {
            showNotification('مقدار نباید کمتر از 1 باشد', 'danger');
            $(event.target).val(1);
            return;
        }
        
        item.qty = newQty;
        renderOrderItems();
    }
}

function recalculateAfterDiscount() {
    let total_price = parseFloat($('#total-price').val()) || 0;
    let discount = parseFloat($('#discount').val());
    if (isNaN(discount) || discount < 0) {
        discount = 0;
        $('#discount').val(0);
    }

    let payable = parseFloat($('#payable').val()) || 0;
    let curPay = parseFloat($('#cur_pay').val()) || 0;

    let newPayable = total_price - discount;
    let newCurPay = total_price - discount;

    if (newPayable < 0) {
        newPayable = 0;
    }

    $('#payable').val(newPayable.toFixed(2));
    $('#cur_pay').val(newCurPay.toFixed(2));
}

</script>

<script>
function submitForm(print = false,event) 
{
     // Prevent default form submission
     if (event) {
        event.preventDefault();
    }
    
    // Show loading state
    $('#loading-state').show();
    
    // Get form data
    const form = $('#pos_form');
    const formData = new FormData(form[0]);

    
    $.ajax({
        url: '/sales/pos_store',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#loading-state').hide();
            
            if (response.status === 'success') 
            {

                showNotification(response.message, 'success');

                // If print was requested
                if (print) 
                {
                    printReceipt();
                }
                
                // Clear the current order
                clearSelectedItems();
                
                // Then reload items after a short delay to ensure UI updates
                setTimeout(() => {
                    loadItems();
                }, 500);

                
            } 
            else 
            {
                showNotification(response.message, 'danger');
            }
        },
        error: function(xhr) {
            $('#loading-state').hide();
            let message = 'خطا در ارتباط با سرور';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            } else if (xhr.statusText) {
                message = xhr.statusText;
            }
            
            showNotification(message, 'danger');
            console.error('Error:', xhr);
        }
    });
}


function printReceipt() {
    // Get company profile info from hidden div
    const companyLogo = $('#image_url').attr('src');
    const companyName = $('#company_name').text();
    const companyAddress = $('#company_address').text();
    const companyPhone = $('#company_phone').text();
    let currencyName = $('#currency_id option:selected').text();
    
    const saleData = {
        items: orderItems,
        total: $('#total-price').val(),
        discount: $('#discount').val(),
        payable: $('#payable').val(),
        cur_pay: $('#cur_pay').val(),
        customer: $('#customer_account_id option:selected').text() || 'عمومی',
        date: new Date().toLocaleString('fa-IR') // Persian locale format
    };

    // Create a printable HTML template with RTL and table layout
    const printContent = `
        <!DOCTYPE html>
        <html dir="rtl">
        <head>
            <meta charset="UTF-8">
            <title>فاکتور فروش</title>
            <style>
                // uncomment for pos small size print
                @page { size: auto; margin: 0; }
                body { 
                    font-family: Tahoma, Arial, sans-serif; 
                    width: 80mm; 
                    margin: 0 auto; 
                    padding: 5px;
                    padding: 0px;
                    direction: rtl;
                }

                // @page { size: A4; margin: 20mm; }
                // body { 
                //     font-family: Tahoma, Arial, sans-serif; 
                //     width: 100%;
                //     margin: 0 auto; 
                //     padding: 0px;
                //     direction: rtl;
                // }

                .receipt-container {
                    border: 2px solid #333;
                    border-radius: 5px;
                    padding: 10px;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 10px;
                    border-bottom: 1px dashed #333;
                    padding-bottom: 10px;
                }
                .company-logo {
                    max-width: 80px;
                    max-height: 60px;
                    margin: 0 auto;
                }
                .company-name {
                    font-size: 16px;
                    font-weight: bold;
                    margin: 5px 0;
                }
                .company-info {
                    font-size: 12px;
                    margin: 3px 0;
                }
                .receipt-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin: 10px 0;
                    text-align: center;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 10px 0;
                }
                th {
                    background-color: #187ba5;
                    color: white;
                    padding: 5px;
                    text-align: center;
                    font-size: 12px;
                }
                td {
                    padding: 5px;
                    border-bottom: 1px solid #ddd;
                    font-size: 12px;
                }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-left { text-align: left; }
                .total-row {
                    font-weight: bold;
                    background-color: #f5f5f5;
                }
                .footer {
                    text-align: center;
                    margin-top: 15px;
                    font-size: 11px;
                    border-top: 1px dashed #333;
                    padding-top: 10px;
                }
                .receipt-no {
                    text-align: right;
                    font-size: 11px;
                    margin-bottom: 5px;
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="header">
                    ${companyLogo ? `<img src="${companyLogo}" class="company-logo">` : ''}
                    <div class="company-name">${companyName}</div>
                    <div class="company-info">${companyAddress}</div>
                    <div class="company-info">تلفن: ${companyPhone}</div>
                </div>
                
                <div class="receipt-title">فاکتور فروش</div>
                
                <div class="receipt-no">
                    <span>تاریخ: ${saleData.date}</span>
                </div>
                
                <div class="receipt-no">
                    <span>مشتری: ${saleData.customer}</span>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th class="text-right" width="40%"> جنس</th>
                            <th class="text-right" width="15%">تعداد</th>
                            <th class="text-right" width="25%">قیمت</th>
                            <th class="text-right" width="20%">مجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${saleData.items.map(item => `
                            <tr>
                                <td>${item.item_name}</td>
                                <td class="text-right">${item.qty} ${item.unit_name}</td>
                                <td class="text-right">${item.sell_up.toFixed(2)}</td>
                                <td class="text-right">${(item.sell_up * item.qty).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                
                <table>
                    <tr class="total-row">
                        <td class="text-right">جمع کل:</td>
                        <td class="text-right">${saleData.total}</td>
                    </tr>
                    <tr>
                        <td class="text-right">تخفیف:</td>
                        <td class="text-right">${saleData.discount}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-right">قابل پرداخت:</td>
                        <td class="text-right">${saleData.payable}</td>
                    </tr>
                    <tr>
                        <td class="text-right">پرداخت فعلی:</td>
                        <td class="text-right">${saleData.cur_pay}</td>
                    </tr>
                    <tr>
                        <td class="text-right"> کرنسی:</td>
                        <td class="text-right">${currencyName}</td>
                    </tr>
                </table>
                
                <div class="footer">
                    <div>با تشکر از خرید شما</div>
                </div>
            </div>
        </body>
        </html>
    `;

    // Open print window
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Trigger print after content loads
    setTimeout(() => {
        printWindow.print();
        // printWindow.close(); // Optional: keep window open for preview
    }, 500);
}

$(document).ready(function() {
    function validateAndSubmit(print = false, is_test_print=false) {
        const account_id = $('#customer_account_id').val();
        
        // Check if customer is selected
        if (account_id <= 0) {
            showNotification('مشتری را انتخاب نمایید', 'danger');
            return;
        }
        
        // Check if at least one item is selected
        if (orderItems.length === 0) {
            showNotification('حداقل یک آیتم باید انتخاب شود', 'danger');
            return;
        }

        if(is_test_print)
        {
            printReceipt();
        }
        else 
        {
            submitForm(print);
        }
       
    }

    $('#submit').on('click', function(e) {
        e.preventDefault();
        validateAndSubmit(false,false);
    });

    $('#submit_and_print').on('click', function(e) {
        e.preventDefault();
        validateAndSubmit(true,false);
    });
    $('#test_print').on('click', function(e) {
        e.preventDefault();
        validateAndSubmit(false,true);
    });
    
});


</script>
@endsection


