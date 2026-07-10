/* Abdul Latif Erfan custom ajax alert function*/
function tempAlert(msg, duration) {
	var el = document.createElement("div");
	el.setAttribute(
		"style",
		"position:fixed;top:20%;margin-left:20%;margin-right:20%;background-color:white;height:50px;width:60%;color:blue;font-weight:bolder;font-size:25px; text-align:center;border-radius:30px;box-shadow:5px 5px 100px black;padding-top:6px;"
	);
	el.innerHTML = msg;
	setTimeout(function () {
		el.parentNode.removeChild(el);
	}, duration);
	document.body.appendChild(el);
}
function confirmPass() {
	var repassword = $("#repassword").val();
	var password = $("#password").val();
	if (repassword == password && password != "") {
		$("#conf_pass").html(
			"<font color='green'><b>تکرار رمز عبور درست است</b></font>"
		);
		$("#submit_button").fadeIn(1);
	} else {
		$("#conf_pass").html(
			"<font color='red'><b>تکرار رمز عبور تاهنوز اشتباه است</b></font>"
		);
		// $("#repassword").val("");
		// $("#repassword").focus();
		$("#submit_button").fadeOut(1);
	}
}

function password_strength_checking() {
	var myInput = document.getElementById("password");
	var letter = document.getElementById("letter");
	var capital = document.getElementById("capital");
	var number = document.getElementById("number");
	var length = document.getElementById("length");

	// When the user clicks on the password field, show the message box
	myInput.onfocus = function () {
		document.getElementById("message").style.display = "block";
	};
	// When the user clicks outside of the password field, hide the message box
	myInput.onblur = function () {
		document.getElementById("message").style.display = "none";
	};

	// When the user starts to type something inside the password field
	myInput.onkeyup = function () {
		document.getElementById("message").style.display = "block";
		let val = document.getElementById("password").value;
		$("#typedPass").text("");
		$("#typedPass").append(val);
		// Validate lowercase letters
		var lowerCaseLetters = /[a-z]/g;
		if (myInput.value.match(lowerCaseLetters)) {
			letter.classList.remove("invalid");
			letter.classList.add("valid");
		} else {
			letter.classList.remove("valid");
			letter.classList.add("invalid");
		}

		// Validate capital letters
		var upperCaseLetters = /[A-Z]/g;
		if (myInput.value.match(upperCaseLetters)) {
			capital.classList.remove("invalid");
			capital.classList.add("valid");
		} else {
			capital.classList.remove("valid");
			capital.classList.add("invalid");
		}

		// Validate numbers
		var numbers = /[0-9]/g;
		if (myInput.value.match(numbers)) {
			number.classList.remove("invalid");
			number.classList.add("valid");
		} else {
			number.classList.remove("valid");
			number.classList.add("invalid");
		}

		// Validate length
		if (myInput.value.length >= 8) {
			length.classList.remove("invalid");
			length.classList.add("valid");
		} else {
			length.classList.remove("valid");
			length.classList.add("invalid");
		}
		// if all conditions are ok, show submit button

		if (
			myInput.value.match(lowerCaseLetters) &&
			myInput.value.match(upperCaseLetters) &&
			myInput.value.match(numbers) &&
			myInput.value.length >= 8
		) {
			$("#submit_button").fadeIn(100);
			document.getElementById("message").style.display = "none";
		} else {
			$("#submit_button").fadeOut(100);
		}
	};
}

function doConfirm() {
	var conf = confirm("آیا با عملیه حذف موافق هستید ؟");
	if (conf) {
		return true;
	} else {
		return false;
	}
}
function doConfirmAttendance() {
	var conf = confirm("میخواهید حاضری این ماه را تایید نمایید ؟");
	if (conf) {
		return true;
	} else {
		return false;
	}
}

// function findRelatedVillages(burl, id) {
// 	$("#dynamic_village").html(
// 		'<center><img src="' +
// 			burl +
// 			'assets/img/loader.gif" style="width:20%;margin-top:20px;" alt="Loading"/></center>'
// 	);
// 	$.ajax({
// 		type: "POST",
// 		data: { id: id },
// 		url: burl + "createAccount/CreateAccount/showVillagesByNation_id",
// 		success: function(result) {
// 			$("#dynamic_village").html(result);
// 		},
// 		error: function(xhr, status) {
// 			$("#dynamic_village").html("Error, انترنت ضعیف است باردیگر کوشش نمایید ");
// 		}
// 	});
// }

function showPassword() {
	var elem = document.getElementById("passwd");
	if (elem.type === "password") {
		elem.type = "text";
		$("#togglePassword").removeClass("fa fa-eye");
		$("#togglePassword").addClass("fa fa-eye-slash");
		$("#togglePassword").animate({ borderRadius: "100px" });
	} else {
		elem.type = "password";
		$("#togglePassword").removeClass("fa fa-eye-slash");
		$("#togglePassword").addClass("fa fa-eye");
		$("#togglePassword").animate({ borderRadius: "100px" });
	}
}

function show_search_form(id) {
	var elem = document.getElementById("searchWrapper" + id);
	if (elem.style.display === "none") {
		elem.style.display = "block";
	} else {
		elem.style.display = "none";
	}
}

function toggleForm(id) {
	var elem = document.getElementById("toggleFormWrapper" + id);
	if (elem.style.display === "none") {
		elem.style.display = "block";
	} else {
		elem.style.display = "none";
	}
}

function print_report() {
	var data = document.getElementById("print_area").innerHTML;
	var printWindow = window.open("", "pw", "");
	printWindow.document.write(
		"<html><head><title></title><style type='text/css'>"
	);
	printWindow.document.write(
		"body{direction:rtl !important;text-align:right !important;margin: 1mm 1mm 55mm 1mm;}table{border-collapse:collapse;}.hidden-print{display:none !important;}.visible-print{display:block !important;width:100% !important;}.center{text-align:center;display:inline}.header{font-size:10px !important;} table tr, table td{page-break-inside:avoid;} thead{display: table-header-group;} thead { display:table-header-group } tfoot{page-break-inside:avoid;} h1, h2, h3, h5, h6{line-height: 1.4px !important;} table.signedFormTable tr th, table.signedFormTable tr td{border:1px solid #000;padding: 10px; text-align:right;}.m-t-50{margin-top:50px}.m-t-30{margin-top:30px}.myHR{border: 1px solid #333;}.myHR2{border: 3px solid #333;margin-top: -14px !important;}.m-b-30{margin-bottom:30px}.m-b-50{margin-bottom:50px}*{-webkit-print-color-adjust: exact !important; color-adjust: exact !important;}"
	);
	printWindow.document.write("</style><body>" + data + "</body></html>");
	printWindow.print();
	printWindow.close();
}

function print_page() {
	var data = document.getElementById("print_area").innerHTML;
	var printWindow = window.open("", "pw", "");
	printWindow.document.write(
		"<html><head><title></title><style type='text/css'>"
	);
	printWindow.document.write(
		"body{direction:rtl !important;text-align:right !important;margin: 1mm 1mm 55mm 1mm;}table{border-collapse:collapse !important;} table tr td{border:1px solid #444 !important;}.table-bordered > thead > tr > th,td{border: 1px solid #444;font-size:14px !important;padding:5px;text-align:right;}table thead tr{background-color:#007aab; color:#fff;}.hidden-print{display:none !important;}.visible-print{display:block !important;width:100% !important;}.my_table{width:100% !important;}.center{text-align:center;display:inline}.header{font-size:10px !important;} thead{display: table-header-group;} table{border:1px solid #777;}tr{ page-break-inside:avoid;}td { page-break-inside:avoid; } th{border:1px solid #666;} thead { display:table-header-group }tfoot{page-break-inside:avoid;}h1, h2, h3, h5, h6{line-height: 1.4px !important;}table th.date_width{width:200px !important;}.dataTables_length,.dataTables_info,#table_filter, #table2_filter, #table_paginate, #table2_paginate{display:none;}.m-t-50{margin-top:50px}.m-t-20{margin-top:20px}.m-t-30{margin-top:30px}.myHR{border: 1px solid #333;}.myHR2{border: 3px solid #333;margin-top: -14px !important;}.m-b-30{margin-bottom:30px}.m-b-20{margin-bottom:20px}.m-b-50{margin-bottom:50px}*{-webkit-print-color-adjust: exact !important; color-adjust: exact !important;}.dataTables_filter,.dataTables_paginate,.dataTables_info{display:none}td.price-section{background-color: #f6f6f6;}.final-total{background-color:#007aab;color: #fff;font-size: 20px;font-weight:bolder;}"
	);
	printWindow.document.write("</style><body>" + data + "</body></html>");
	printWindow.print();
	printWindow.close();
}


function print_page_with_image(id=null) 
{
	if(id == 2) 
	{
		var data = document.getElementById("print_area"+id).innerHTML;
	}
	else 
	{
		var data = document.getElementById("print_area").innerHTML;
	}

    var printWindow = window.open("", "PrintWindow", "");
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body {
                    direction: rtl !important;
                    text-align: right !important;
                    margin: 4mm;
                }
                img {
                    display: block !important;
                    max-width: 100% !important;
                    height: auto !important;
                }
                .visible-print {
                    display: block !important;
                    width: 100% !important;
                }
                table {
                    border-collapse: collapse !important;
                    width: 100% !important;
                }
                table tr td, table th {
                    border: 1px solid #444 !important;
                    font-size: 14px !important;
                    padding: 5px;
                    text-align: right;
                }
                table thead tr {
                    background-color: #007aab;
                    color: #fff;
                }
                .hidden-print {
                    display: none !important;
                }
                .visible-print {
                    display: block !important;
                    width: 100% !important;
                }
                .header {
                    font-size: 10px !important;
                }
                thead {
                    display: table-header-group;
                }
                tr, td, th {
                    page-break-inside: avoid;
                }
                h1, h2, h3, h5, h6 {
                    line-height: 1.4px !important;
                }
                .m-t-50 { margin-top: 50px; }
                .m-t-20 { margin-top: 20px; }
                .m-b-30 { margin-bottom: 30px; }
                .m-b-20 { margin-bottom: 20px; }
                .m-b-50 { margin-bottom: 50px; }
                td.price-section {
                    background-color: #f6f6f6;
				}
				.dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
					display:none;
				}
                .final-total {
                    background-color: #007aab;
                    color: #fff;
                    font-size: 20px;
                    font-weight: bold;
                }
                * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
            </style>
        </head>
        <body>${data}</body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}


function print_page_with_image_grid() 
{
    var data = document.getElementById("print_area").innerHTML;

    var printWindow = window.open("", "PrintWindow", "");
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                body {
                    direction: rtl;
                    text-align: center;
                    margin: 10mm;
                    font-family: Tahoma, sans-serif;
                }
                .print-grid {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: flex-start;
                    gap: 10px;
                }
                .barcode-box {
                    width: 130px;
                    border: 1px solid #999;
                    padding: 8px;
                    margin-bottom: 10px;
                    text-align: center;
                    page-break-inside: avoid;
                }
                .barcode-box img {
                    width: 100px;
                    height: auto;
                }
                .barcode-box p {
                    margin-top: 5px;
                    font-size: 14px;
                }
                @media print {
                    .page-break {
                        page-break-after: always;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-grid">
                ${Array.from(document.querySelectorAll('#print_area .barcode-box')).map(el => el.outerHTML).join('')}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function print_page_with_image_grid_single_column() {
    const data = Array.from(document.querySelectorAll('#print_area .barcode-box'));

    const printWindow = window.open("", "PrintWindow", "");
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @page {
                    size: A4;
                    margin: 10mm;
                }

                body {
                    direction: rtl;
                    font-family: Tahoma, sans-serif;
                    margin: 0;
                    padding: 0;
                }

                .print-grid {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    gap: 5mm;
                }

                .barcode-box {
                    height: 40mm;
                    border: 1px solid #999;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    box-sizing: border-box;
                    padding: 5mm;
                    page-break-inside: avoid;
                }

                .barcode-box img {
                    max-height: 25mm;
                    width: auto;
                    max-width: 90%;
                    object-fit: contain;
                }

                .barcode-box p {
                    margin: 3mm 0 0;
                    font-size: 13px;
                    line-height: 1.2;
                    white-space: nowrap;
                }

                @media print {
                    .barcode-box {
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-grid">
                ${data.map(el => el.outerHTML).join('')}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function print_page_with_image_grid_single_image_per_page() {
	const barcodeBoxes = Array.from(document.querySelectorAll('#print_area .barcode-box'));

    const printWindow = window.open("", "PrintWindow", "");
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @page {
                    size: A4;
                    margin: 0;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma, sans-serif;
                    direction: rtl;
                    text-align: center;
                }

                .label-page {
                    width: 210mm;
                    height: 297mm;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    box-sizing: border-box;
                    page-break-after: always;
                }

                .barcode-box {
                    width: 100%;
                    height: 40mm;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    box-sizing: border-box;
                }

                .barcode-box img {
                    max-height: 25mm;
                    max-width: 90%;
                    object-fit: contain;
                }

                .barcode-box p {
                    margin-top: 4mm;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            ${barcodeBoxes.map(box => `<div class="label-page">${box.outerHTML}</div>`).join('')}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}


function print_page_with_image_grid_labelPrinter() {
    const barcodeBoxes = Array.from(document.querySelectorAll('#print_area .barcode-box'));

    const printWindow = window.open("", "PrintWindow", "");
    printWindow.document.write(`
        <html>
        <head>
            <title>Print</title>
            <style>
                @page {
                    size: 58mm 38mm;
                    margin: 0;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: Tahoma, sans-serif;
                    direction: rtl;
                    text-align: center;
                }

                .label-page {
                    width: 58mm;
                    height: 38mm;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    page-break-after: always;
                    box-sizing: border-box;
                }

                .barcode-box {
                    border: none;
                    width: 100%;
                    padding: 0;
                    box-sizing: border-box;
                }

                .barcode-box img {
                    width: 100%;
                    height: auto;
                    max-width: 52mm;
                }

                .barcode-box p {
                    margin: 1mm 0 0;
                    font-size: 11px;
                    line-height: 1.2;
                    white-space: nowrap;
                }
            </style>
        </head>
        <body>
            ${barcodeBoxes.map(box => `<div class="label-page">${box.outerHTML}</div>`).join('')}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}




// table tr{page-break-before:always;}

function printGeneralRequest() {
	var data = document.getElementById("print_area").innerHTML;
	var printWindow = window.open("", "pw", "");
	printWindow.document.write(
		"<html><head><title></title><style type='text/css'>"
	);
	printWindow.document.write(
		"body{direction:rtl !important;text-align:right !important;margin: 1mm 1mm 55mm 1mm;}table{border-collapse:collapse !important;} table tr td{border:1px solid #000 !important;}.table-bordered > thead > tr > th,td{border:1px solid #000;font-size:14px !important;padding:5px;text-align:right;}.hidden-print{display:none !important;}.visible-print{display:block !important;width:100% !important;}.myTable{width:100% !important;}.center{text-align:center;display:inline}.header{font-size:10px !important;}table.myTable th{font-weight:normal !important;background-color: #f4f1f1 !important;color: #000 !important;text-align: center !important;padding: 10px !important; } thead{display: table-header-group;} table{border:1px solid #fff;}tr{ page-break-inside:avoid; }td { page-break-inside:avoid;}thead { display:table-header-group }tfoot{ page-break-inside:avoid;}.row{display:none !important;}.form-inline .form-control{border:none !important;} input[type=text]{border:none !important;}.table_reprot td{text-align:right;}input[type=text]{text-align:right important;direction:rtl;}h1{font-size:22px;}.LeaveFormTable_noBorder tr, .LeaveFormTable_noBorder td{border:1px solid #fff !important;padding:5px 10px;}.myHR{border:1px solid #333;}.myHR2{border:3px solid #333;margin-top: -5px !important;}.sign_image img{width:70px;height:70px;position: absolute;top: 430px;right: 100px;}.sign_image_lm img{width:70px;height:70px;position: absolute;top: 430px;left: 60px;}.sign_image_hr img{width:70px;height:70px;position: absolute;top: 760px;left: 80px;}.sign_image_gm img{width:70px;height:70px;position: absolute;top: 850px;right: 150px;}.noBorder tr, .noBorder td{border:none !important;}.sign_emp img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_omer img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_hr img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_gm img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.monthly_table th,.tablePrint th{background-color:#007aab; color:#fff;}#exampleprint_filter,#exampleprint_length, .dt-buttons, #exampleprint_info, #exampleprint_paginate{display:none;}td.borderbottom{border-bottom:1px solid #666 !important;}h1, h2, h3, h5, h6{line-height: 1.4px !important;}table.ltrTable tr, table.ltrTable tr td{text-align:left;}p.lheight{line-height: 0.50 !important; font-size:16px;}"
	);
	printWindow.document.write("</style><body>" + data + "</body></html>");
	printWindow.print();
	printWindow.close();
}
function print_page2() {
	var data = document.getElementById("print_area").innerHTML;
	var printWindow = window.open("", "pw", "");
	printWindow.document.write(
		"<html><head><title></title><style type='text/css'>"
	);
	printWindow.document.write(
		"body{direction:rtl !important;text-align:right !important;margin: 1mm 1mm 55mm 1mm;}table{border-collapse:collapse !important;} table tr td{border:1px solid #000 !important;}.table-bordered > thead > tr > th,td{border:1px solid #000;font-size:14px !important;padding:5px;text-align:right;}.hidden-print{display:none !important;}.visible-print{display:block !important;width:100% !important;}.myTable{width:100% !important;}.center{text-align:center;display:inline}.header{font-size:10px !important;}table.myTable th{font-weight:normal !important;background-color: #f4f1f1 !important;color: #000 !important;text-align: center !important;padding: 10px !important; } thead{display: table-header-group;} table{border:1px solid #fff;}tr{ page-break-inside:avoid;}td { page-break-inside:avoid;}thead { display:table-header-group }tfoot{page-break-inside:avoid;}.row{display:none !important;}.form-inline .form-control{border:none !important;} input[type=text]{border:none !important;}.table_reprot td{text-align:right;}input[type=text]{text-align:right important;direction:rtl;}h1{font-size:22px;}.LeaveFormTable_noBorder tr, .LeaveFormTable_noBorder td{border:1px solid #fff !important;padding:5px 10px;}.myHR{border:1px solid #333;}.myHR2{border:3px solid #333;margin-top: -5px !important;}.sign_image img{width:70px;height:70px;position: absolute;top: 430px;right: 100px;}.sign_image_lm img{width:70px;height:70px;position: absolute;top: 430px;left: 60px;}.sign_image_hr img{width:70px;height:70px;position: absolute;top: 760px;left: 80px;}.sign_image_gm img{width:70px;height:70px;position: absolute;top: 850px;right: 150px;}.noBorder tr, .noBorder td{border:none !important;}.sign_emp img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_omer img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_hr img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_gm img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.monthly_table th,.tablePrint th{background-color:#007aab; color:#fff;}"
	);
	printWindow.document.write("</style><body>" + data + "</body></html>");
	printWindow.print();
	printWindow.close();
}
function print_page_byNumber() {
	var data = document.getElementById("print_area").innerHTML;
	var printWindow = window.open("", "pw", "");
	printWindow.document.write(
		"<html><head><title>" + "title" + "</title><style type='text/css'>"
	);
	printWindow.document.write(
		"body{direction:rtl !important;text-align:right !important;margin: 1mm 1mm 55mm 1mm;}table{border-collapse:collapse !important;} table tr td{border:1px solid #000 !important;}.table-bordered > thead > tr > th,td{border:1px solid #000;font-size:14px !important;padding:5px;text-align:right;}.hidden-print{display:none !important;}.visible-print{display:block !important;width:100% !important;}.myTable{width:100% !important;}.center{text-align:center;display:inline}.header{font-size:10px !important;}table.myTable th{font-weight:normal !important;background-color: #f4f1f1 !important;color: #000 !important;text-align: center !important;padding: 10px !important; } thead{display: table-header-group;} table{border:1px solid #fff;}tr{ page-break-inside:avoid;}td { page-break-inside:avoid;}thead { display:table-header-group }tfoot{page-break-inside:avoid;}.row{display:none !important;}.form-inline .form-control{border:none !important;} input[type=text]{border:none !important;}.table_reprot td{text-align:right;}input[type=text]{text-align:right important;direction:rtl;}h1{font-size:22px;}.LeaveFormTable_noBorder tr, .LeaveFormTable_noBorder td{border:1px solid #fff !important;padding:5px 10px;}.myHR{border:1px solid #333;}.myHR2{border:3px solid #333;margin-top: -5px !important;}.sign_image img{width:70px;height:70px;position: absolute;top: 430px;right: 100px;}.sign_image_lm img{width:70px;height:70px;position: absolute;top: 430px;left: 60px;}.sign_image_hr img{width:70px;height:70px;position: absolute;top: 760px;left: 80px;}.sign_image_gm img{width:70px;height:70px;position: absolute;top: 850px;right: 150px;}.noBorder tr, .noBorder td{border:none !important;}.sign_emp img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_omer img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_hr img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.sign_gm img{width:92px !important;position: relative;bottom: -20px;right: 200px;}.monthly_table th,.tablePrint th{background-color:#007aab; color:#fff;}.rounded-circle{border-radius:50%;object-fit:cover;}"
	);
	printWindow.document.write("</style><body>" + data + "</body></html>");
	printWindow.print();
	printWindow.close();
}
function doTax(salary = 0) {
	var netSalary = 0;
	if (salary > 100000) {
		netSalary = ((salary - 100000) * 20) / 100 + 8900;
	} else if (salary > 12500) {
		netSalary = ((salary - 12500) * 10) / 100 + 150;
	} else if (salary > 5000) {
		netSalary = ((salary - 5000) * 2) / 100;
	} else {
		netSalary = 0;
	}
	return netSalary;
}
$(document).ready(function () {
	$('a[data-toggle="tab"]').on("show.bs.tab", function (e) {
		localStorage.setItem("activeTab", $(e.target).attr("href"));
	});
	var activeTab = localStorage.getItem("activeTab");
	if (activeTab) {
		$('#myTab2 a[href="' + activeTab + '"]').tab("show");
	}
});
$(document).ready(function () {
	$('[data-toggle="popover"]').popover();
});
