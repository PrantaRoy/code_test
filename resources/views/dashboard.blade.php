@extends('admin.layouts.master')
@section('content')
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4 text-success">Current Balance  : {{number_format(auth()->user()->balance ,2)}}</h1>
        <ol class="breadcrumb mb-4">
            <h5 class="breadcrumb-item active"><strong> Account Type  : {{ucfirst(auth()->user()->account_type)}}</strong></h5>
        </ol>
        <div class="row">
            <div class="col-xl-12">
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if(session()->has('warning'))
                    <div class="alert alert-danger">
                        {{ session()->get('warning') }}
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif

            </div>
            <div class="col-xl-6 col-md-6">
                <div class="card  bg-dark text-white mb-4">
                    <div class="card-body"><i class="fas fa-add"></i> Deposit Money</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" data-bs-toggle="modal" data-bs-target="#depoModal" data-bs-whatever="@mdo">Click Here For Deposit</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6">
                <div class="card bg-dark text-white mb-4">
                    <div class="card-body"><i class="fas fa-minus"></i> Withdraw Money</div>
                    <div class="card-footer d-flex align-items-center justify-content-between">
                        <a class="small text-white stretched-link" data-bs-toggle="modal" data-bs-target="#withdrawModal" data-bs-whatever="@mdo">Click Here For Withdraw</a>
                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
            </div>
            
        </div>


        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4 ">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-chart-area me-1"></i>
                       My Deposit History
                    </div>
                    <div class="card-body">
                        <table class="table table-stripe">
                            <thead >
                               
                                <th>Date</th>
                                <th>Amount</th>
                                
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->trnasactions->where('transaction_type','deposit') as $key=> $depo)
                                <tr>
                                   
                                    <td>{{ \Carbon\Carbon::parse($depo->created_at)->format('F j, Y, g:i A') }}</td>
                                    <td> + {{number_format($depo->amount,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header  bg-warning">
                        <i class="fas fa-chart-bar me-1"></i>
                        My Withdraw History
                    </div>
                    <div class="card-body">
                        <table class="table table-stripe">
                            <thead >
                                
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Fee</th>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->trnasactions->where('transaction_type','withdraw') as $key=> $withdr)
                                <tr>
                                    
                                    <td>{{ \Carbon\Carbon::parse($withdr->created_at)->format('F j, Y, g:i A') }}</td>
                                    <td>(-) {{number_format($withdr->amount,2)}}</td>
                                    <td>(-) {{number_format($withdr->fee,2)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>




    {{-- Deposit modal sectin here  --}}

 <div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{route('transaction.store')}}" method="post">
         @csrf 
         <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Withdraw Money </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="transaction_type" value="withdraw">
                <label for="recipient-name" class="col-form-label">Withdraw Amount</label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)" required name="amount">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="sumit" class="btn btn-primary">Withdraw Now</button>
              </div>
            </div>

    </form>
    </div>
</div>

<div class="modal fade" id="depoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="{{route('transaction.store')}}" method="post">
         @csrf 

         <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Deposit Money </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="transaction_type" value="deposit">
                  <div class="mb-3">
                    <label for="recipient-name" class="col-form-label">Deposit Amount</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">$</span>
                        </div>
                        <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)" required name="amount">
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="sumit" class="btn btn-primary">Deposit Now</button>
              </div>
            </div>

    </form>
    </div>
</div>


</main>


 



@endsection