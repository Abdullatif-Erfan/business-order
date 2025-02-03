<form id="accountEditForm">
   @csrf
   <input type="hidden" name="id" value="{{ $account->id }}">
    <div class="col-xs-12">
          <div class="table-responsive">
           <table class="table table-bordered">
              <tr>
                <th>نوع حساب</th><td>{{ $account->accountType->name }}</td>
                <th>نام حساب</th><td>{{ $account->name }}</td>
              </tr>

              <tr>
                <th> شماره تماس </th><td>{{ $account->phone }}</td>
                <th> آدرس </th><td>{{ $account->address }}</td>
              </tr>

           </table>
          </div>
          
            
            <div class="col-12">
              <hr />
               <h3>رسید حساب سابقه</h3>
            </div>
           

                <!-- form repeater -->
                <div id="formContainer" class="col-12" style="padding:10px;">
                    <table class="table table-bordered">
                        <tr>
                            <th>شماره</th>
                            <th>مبلغ</th>
                            <th>طلب / باقی</th>
                            <th>  واحد پولی </th>
                        </tr>
                    @foreach($journals as $index => $item) <!-- Loop through the data -->
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['amount'] }}</td>
                            <td>{{ $item['transaction_type'] == 1 ? 'افزایش در حساب (طلب)' : 'کاهش از حساب (باقی)' }}</td>
                            <td>{{ $item->currency->name }}</td>
                        </tr>
                    @endforeach
                    </table>
                    </div>
                </div>
                <!-- /form repeater -->


    </div>

    
</form>

