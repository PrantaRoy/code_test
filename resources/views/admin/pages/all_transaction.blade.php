@extends('admin.layouts.master')

@section('content')
<main>
     <div class="container-fluid px-4">
         
         <h1 class="mt-4">{{$pt}}</h1>
         <ol class="breadcrumb mb-4">
             <li class="breadcrumb-item"><a href="#">Transaction</a></li>
             <li class="breadcrumb-item active">{{$pt}}</li>
         </ol>
        
         <div class="card mb-4">
            <div class="card-header">
               <div class="row">
                    <div class="col-lg-12">
                         <i class="fas fa-table me-1"></i>
                              {{$pt}}
                    </div>
                    
                  </div>
            </div>
             <div class="card-body">
                 <table id="datatablesSimple">
                     <thead>
                         <tr>
                              <th>Date</th>
                             <th>Trans Type</th>
                             <th>Amount</th>
                             <th>Fee</th>
                             <th>Curr Balance</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach($transactions as $transaction)
                         <tr>
                              <td>{{\Carbon\Carbon::parse($transaction->created_at)->format('F j, Y, g:i A')}}</td>
                             <td>@if($transaction->transaction_type == 'deposit') (+) @else  (- ) @endif {{@$transaction->transaction_type}}</td>
                             <td>{{number_format($transaction->amount,2)}}</td>
                             <td>{{number_format($transaction->fee,2)}}</td>
                             <td>{{number_format($transaction->user->balance,2)}}</td>
                         </tr>
                         @endforeach
                         
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </main>


@endsection