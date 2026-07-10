@extends('layouts.app') 

@section('styles')
  <style>
    .content-wrapper {
      padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    }
  </style>
@endsection

@section('content')
  <!-- Main content -->
  <div class="main-panel">
    <div class="content">
      <div class="page-inner">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="card center p-t-20 p-b-20">
              <div class="content-wrapper">
                <section class="content-header">
                  <h1>{{__('wh.no_permission')}}</h1>
                  <h4>{{__('wh.not_allowed_to_access')}}</h4>
                </section>
                <br/>
                <img src="{{ asset('assets/img/access/access.png') }}" alt="Access Denied Image" style="width: 200px;" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /Main content -->
@endsection
