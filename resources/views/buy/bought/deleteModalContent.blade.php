
    <div class="row m-t-10 m-b-10 p-10">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <input type="hidden" id="delete_amount" name="delete_amount" value={{ $boughtItemDetailsAmount  }} >
            <input type="hidden" id="delete_id" name="delete_id" value={{ $boughtItemDetailsId  }} >
            <input type="hidden" id="preListId" name="preListId" value={{ $preListId  }} >
            <input type="hidden" id="times" name="times" value={{ $times  }} >


            <div class="alert alert-success"> به تعداد {{ $boughtItemDetailsAmount }} جنس از کدام گدام میخواهید کم شود ؟</div>
        </div>
        @foreach($warehouseItems as $item)
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="name"> گدام </label>
            <input  name="warehouse_id[]" id="wid" type="hidden" value={{$item->warehouse_id}} >
            <input class="form-control" name="name" id="name" type="text" readonly value={{ $item->warehouseRelation->name }} >
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="available_amount"> تعداد موجود </label>
            <input class="form-control" name="available_amount" id="available_amount" type="number" step="0.01" readonly
            value={{ $item->available_amount }} >
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <label for="decrement"> تعداد کاهش  </label>
            <input class="form-control" name="decrement[]" id="decrement" type="number" step="0.01"  placeholder="تعداد که میخواهید حذف نماییید" >
        </div>
        @endforeach

    </div>

<script>
    $(document).ready(function() {
        let deleteAmount = parseFloat($('#delete_amount').val());
        let $deleteButton = $('#delete_button');

        function validateDecrements() {
            let totalDecrement = 0;
            let isValid = true;

            $('input[name="decrement[]"]').each(function() {
                let decrementValue = parseFloat($(this).val()) || 0;
                let availableAmount = parseFloat($(this).closest('.col-xs-6').prev().find('input[name="available_amount"]').val()) || 0;

                if (decrementValue > availableAmount) {
                    alert('⚠️ مقدار کاهش نمی‌تواند بیشتر از مقدار موجود باشد.');
                    $(this).val(availableAmount); // Auto-correct value
                    isValid = false;
                }

                totalDecrement += decrementValue;
            });

            if (totalDecrement !== deleteAmount) {
                $deleteButton.hide(); // Hide button if total is not equal
            } else {
                $deleteButton.show(); // Show button when total is correct
            }
        }

        $('input[name="decrement[]"]').on('input', validateDecrements);
    });
</script>


