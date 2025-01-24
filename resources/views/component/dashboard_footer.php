	
   <?php $bul = base_url();  ?>
   <?php 			
		$incomes=[];
		$outcomes=[];
		for($i=1;$i<=12;$i++)
		{
			// data: [3, 2, 9, 5, 4, 6, 4, 6, 7, 8, 7, 4],
			$incomes[] = array(show_monthly_income($i,$year,$currency_id));
			$outcomes[] = array(show_monthly_outcom($i,$year,$currency_id));
		}
		// echo json_encode($incomes);
		// die();
		// $totalTalabat = show_total_talabat_till_now($year,$currency_id);
		// $totalLoan = show_total_loan_till_now($year,$currency_id);

		$totalTalabat = show_total_talabat_till_now($year,$currency_id);
		$totalLoan = show_total_loan_till_now($year,$currency_id);
		$finalBalance = $totalLoan - $totalTalabat;
		

    ?>
    </div> <!--/ wrapper-->

	<script src="<?php echo $bul; ?>assets/js/core/custom.js"></script>
    <script src="<?php echo $bul; ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Chart Circle -->
	<script src="<?php echo $bul; ?>assets/js/plugin/chart-circle/circles.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jqvmap/jquery.vmap.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jqvmap/maps/jquery.vmap.world.js"></script>
	<script src="<?php echo $bul; ?>assets/js/demo.js"></script>
	<!--   Core JS Files   -->
	<script src="<?php echo $bul; ?>assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/core/popper.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/core/bootstrap.min.js"></script>
	<!-- jQuery UI -->
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<!-- Chart JS -->
	<script src="<?php echo $bul; ?>assets/js/plugin/chart.js/chart.min.js"></script>
	
	<!-- jQuery Scrollbar -->
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<!-- Atlantis JS -->
	<script src="<?php echo $bul; ?>assets/js/atlantis.min.js"></script>
	<script>
		var 	barChart = document.getElementById('barChart').getContext('2d'),
				pieChart = document.getElementById('pieChart').getContext('2d');
				
		var myBarChart = new Chart(barChart, {
			type: 'bar',
			data: {
				// labels: ["حوت", "دلو", "جدی", "قوس", "عقرب", "میزان", "سنبله", "اسد", "سرطان", "جوزا", "ثور", "حمل"],
				labels: ["حمل", "ثور","جوزا","سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
				// labels: ["جنوری", "فبروری", "مارچ", "آپریل", "می", "جون", "جولای", "اگست", "سپتمبر", "اکتوبر", "نومبر", "دسمبر"],
				datasets : [
				{
					label: "قرضه",
					backgroundColor: '#3f7cc7',
					data:<?php echo json_encode($incomes); ?>,
					// data: [3000, 2000, 9000, 5000, 4000, 6000, 4000, 6000, 7000, 8000, 7000, 4000],
				},
				{
					label: "طلبات",
					backgroundColor: '#c78f3f',
					// borderColor: '#c78f3f',
					data: <?php echo json_encode($outcomes); ?>,
					// data: [3000, 2000, 9000, 5000, 4000, 6000, 4000, 6000, 7000, 8000, 7000, 4000],
                }
		     ],
			},
			options: {
				responsive: true, 
				maintainAspectRatio: false,
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true
						}
					}]
				},
			}
		});

		var myPieChart = new Chart(pieChart, {
			type: 'pie',
			data: {
				datasets: [{
					data: [<?=$totalTalabat?>, <?=$totalLoan?> ,  <?=$finalBalance?> ],
					backgroundColor :["#3f7cc7","#c78f3f","#9dd3e2"],
					borderWidth: 0
				}],
				labels: ['قرضه', 'طلبات', 'بیلانس'] 
			},
			options : {
				responsive: true, 
				maintainAspectRatio: false,
				legend: {
					position : 'bottom',
					labels : {
						fontColor: 'rgb(154, 154, 154)',
						fontSize: 11,
						usePointStyle : true,
						padding: 20
					}
				},
				pieceLabel: {
					render: 'percentage',
					fontColor: 'white',
					fontSize: 14,
				},
				 tooltip: {
                enabled: true,
                callbacks: {
                    label: function (context) {
                        var label = context.label || '';
                        var value = context.raw || '';
                        var percentage = context.parsed * 100;
                        return label + ': ' + value + ' (' + percentage.toFixed(2) + '%)';
                    }
                }
            },
				layout: {
					padding: {
						left: 20,
						right: 20,
						top: 20,
						bottom: 20
					}
				}
			}
		});
	</script>
</body>
</html>