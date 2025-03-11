<div class="col-12">
    <div class="row">
      @foreach($branches as $branch)
        <div class="col-sm-4 col-lg-4">
            <div class="card p-3" style="background:linear-gradient(40deg, #ffffff 86%, #0c7eff 77%);border-top:2px solid #0c7eff">
                <div class="d-flex align-items-center" style="margin:30px;font-size: 20px; justify-content:center;">
                   @if($branch_id == $branch->id)
                     <i class="fas fa-check"></i> &nbsp;
                   @endif
                  {{ $branch->name }} 
                </div>
                @if($branch_id == $branch->id)
                <button class="btn btn-round btn-sm" style="background:linear-gradient(44deg, #f28639 80%, #0c7eff 51%); color:#fff;border:2px solid #fff;">ورود به این شعبه</button>
                @else
                <button class="btn btn-round btn-sm" onclick="changeBranch({{ $branch->id }})" style="background:linear-gradient(44deg, #f28639 80%, #0c7eff 51%); color:#fff;border:2px solid #fff;">ورود به این شعبه</button>
                @endif
            </div>
        </div>
      @endforeach
    </div>
</div>
