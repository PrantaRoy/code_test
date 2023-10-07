<?php
namespace App\Enums;

enum TransactionTypeEnum:string{
     case Deposit = 'deposit';
     case Withdraw = 'withdraw';
}