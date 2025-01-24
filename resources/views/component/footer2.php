<?php $bul = base_url(); ?>
</div><!-- /wrapper -->
	<!--   Core JS Files   -->
	<script src="<?php echo $bul; ?>assets/js/core/popper.min.js"></script>
    <script src="<?php echo $bul; ?>assets/js/core/custom.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
	<script src="<?php echo $bul; ?>assets/js/atlantis.min.js"></script>
	<script src="<?php echo $bul; ?>assets/plugin/select2/select2.full.min.js"></script>  
    <script src="<?php echo $bul; ?>assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    
    <!-- 
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/jquery.dataTables.js"></script> 
    <script src="<?php echo $bul; ?>assets/plugin/datatable/js/dataTables.bootstrap.js"></script> -->
    
    <script src="<?php echo $bul; ?>assets/plugin/responsive_datatable/js/dataTables.min.js"></script>
    <script src="<?php echo $bul; ?>assets/plugin/responsive_datatable/js/dataTables.responsive.js"></script>

    <!-- for persian date_picker -->
    <script type="text/javascript">
        $('#input1').change(function() {  var $this = $(this), value = $this.val();  alert(value);});
        $('#textbox1').change(function () {  var $this = $(this),  value = $this.val(); alert(value); });
        $('[data-name="disable-button"]').click(function() {
            $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', true);
        });
        $('[data-name="enable-button"]').click(function () {
            $('[data-mddatetimepicker="true"][data-targetselector="#input1"]').MdPersianDateTimePicker('disable', false);
        });
    </script>
    <script src="<?php echo $bul; ?>assets/datepicker/jalaali.js" type="text/javascript"></script>
    <script src="<?php echo $bul; ?>assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.js" type="text/javascript"></script>
<!-- / p date picker -->

    <script>
    $(function () {
        $(".select2").select2();
    });
</script>

<script>  
    document.addEventListener('DOMContentLoaded', function() {  
        const navItems = document.querySelectorAll('.nav-item');  

        // Function to set active class based on stored value  
        function setActiveClass() {  
            const activeItem = localStorage.getItem('activeNavItem');  
            if (activeItem) {  
                navItems.forEach(item => {  
                    if (item.querySelector('a').getAttribute('href') === activeItem) {  
                        item.classList.add('active');  
                    }  
                });  
            }  
        }  

        // Set active class on page load  
        setActiveClass();  

        navItems.forEach(item => {  
            item.addEventListener('click', function() {  
                // Remove 'active' class from all items  
                navItems.forEach(nav => nav.classList.remove('active'));  
                // Add 'active' class to the clicked item  
                this.classList.add('active');  

                // Store the href of the active item in localStorage  
                const activeHref = this.querySelector('a').getAttribute('href');  
                localStorage.setItem('activeNavItem', activeHref);  
            });  
        });  
    });  
</script>  

</body>
</html>