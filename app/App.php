<?php

declare(strict_types = 1);

// Your Code
function getTransactionFiles(string $dirPath): array {
    $files = [];
    
    foreach(scandir($dirPath) as $file) {
        if (is_dir($file)) {
            continue;
        }
        $files[] = $dirPath . $file;
    }

    return $files;
}

function getTransacions(string $fileName, ?callable $transactionHandler = null): array {
    if (!file_exists($fileName)) {
        trigger_error("File not found: $fileName", E_USER_WARNING);
    }
    $file = fopen($fileName, 'r');

    $transactions = [];

    fgetcsv($file, escape: '');
    while(($transaction = fgetcsv($file, escape: '')) !== false) {
        if ($transactionHandler !== null) {
            $transaction = $transactionHandler($transaction);
        }
        $transactions[] = extractTransaction($transaction);
    }

    return $transactions;
}


function extractTransaction(array $transactionRow): array {
    [$date, $checkNumber, $description, $amount] = $transactionRow;

    $amount = (float) str_replace(['$', ','], '', $amount);

    return [
        'date' => $date,
        'checkNumber' => $checkNumber,
        'description' => $description,
        'amount' => $amount
    ];
}

function calculateTotal(array $transactions): array {
    $total =['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach($transactions as $transaction) {
        $total['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $total['totalIncome'] += $transaction['amount'];
        } else {
            $total['totalExpense'] += $transaction['amount'];
        }
    }

    return $total;
}