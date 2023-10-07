<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    function index($type){
        $data['pt'] =  ucfirst($type).' Transaction';
        $data['type'] = $type;
        $transactions = Transaction::query();

        $transactions->when(($type !='all'), function ($query) use ($type) {
            $query->where('transaction_type', $type);
        });

        $data['transactions'] =  $transactions->where('user_id',auth()->id())->get();
        return view('admin.pages.all_transaction')->with($data);
    }

    function withdrawFeeCalculate($user_id , float $with_amount){
        $user = User::findOrFail($user_id);
        $ac_type  = $user->account_type; 
        //initiate withdraw charge
        $withdraw_rate = ($ac_type == 'individual') ? 0.015 : 0.025 ;
        $fee = 0;
        $isFriday = Carbon::today()->dayOfWeek == Carbon::FRIDAY;

        //individual account 
        if($ac_type == 'individual'){
            $totalWithdraw = $user->currentMonthWithdraw();
            //check monthly quota from 5K
            $rest_from_five = 5000 - $totalWithdraw;
            //friday charge is free
            if($isFriday){
                $fee = 0;
            }
            //monthly 5K withdraw limit
            elseif($totalWithdraw <= 5000){
                if($rest_from_five  <= $with_amount){
                    $fee =  ((($with_amount - $rest_from_five) * 0.015) / 100);
                }
                else{
                    $fee =  0 ;
                }
            }
            else{
                if($with_amount >= 1000){ 
                    $fee = ((($with_amount - 1000 )) * 0.015) / 100;
                }
                else{
                    $fee = ($with_amount * 0.015) / 100; 
                }
            }
            
        }
        elseif ($ac_type == 'business') {
            //check total 50K withdraw check 
            $totalWithdraw = $user->totalWithdraw();

           //when user cross 50K
            if($totalWithdraw >= 50000){
                 $fee = ($with_amount * 0.015) / 100; 
            }
            else{
                 //check rest from 50K
                $rest_from_fifty = 50000 - $totalWithdraw;
               
                if($rest_from_fifty  <= $with_amount){
                    $fee = (($rest_from_fifty * 0.025) / 100) + ((($with_amount - $rest_from_fifty) * 0.015) / 100);
 
                }
                else{
                    $fee =  ($with_amount * 0.025) / 100 ;
                }
               
            }
        } 
        return $fee ;
    }


    public function store(TransactionRequest $request){

        try{
            DB::beginTransaction();
            $user = Auth::user();
            $input = $request->all();
            $input['user_id'] =  $user->id;
            $input['date'] =  date('Y-m-d');
           
            //request for deposit money
            if($input['transaction_type'] == 'deposit'){
                $transaction = Transaction::create($input);
                if($transaction){
                    $user->balance += $transaction->amount;
                    $user->save();
                }
                DB::commit();
                return redirect()->back()->with('success', 'Deposit Successfully');
            }
            //request for withdraw 
            elseif($input['transaction_type'] == 'withdraw'){
                $req_withdraw_amount = $input['amount'];
                //calculate the withdrwar fee
                $withdraw_fee = $this->withdrawFeeCalculate($user->id, $input['amount']);
                //request withdraw amount & fee
                $totalWithCharge = $req_withdraw_amount + $withdraw_fee;
                //check user balance with request withdraw and charge 
                if($user->balance >= $totalWithCharge){
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->transaction_type = 'withdraw';
                    $transaction->amount = $req_withdraw_amount;
                    $transaction->fee = $withdraw_fee;
                    $transaction->date = Carbon::now()->format('Y-m-d');;
                    $transaction->save();
                    
                    if($transaction){
                        $user->balance -= $totalWithCharge;
                        $user->save();
                    }
                    
                    DB::commit();
                    return redirect()->back()->with('message', 'WithDraw Successfully');
                }
                //if account have not sufficent balance  
                else
                {
                    return redirect()->back()->with('warning', 'Insufficient Balance ');
                }
            }
            return redirect()->back();
        }
        catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
           }
    }
}
