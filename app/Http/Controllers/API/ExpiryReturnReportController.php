<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnVouchers;
use App\Models\Profiler;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpiryReturnReportController extends Controller
{
    // Get all suppliers from return_vouchers
    public function getSuppliers(Request $request)
    {
        try {
            $suppliers = ReturnVouchers::select(
                'return_vouchers.supplier_id',
                'profilers.account_title as name'
            )
            ->leftJoin('profilers', 'return_vouchers.supplier_id', '=', 'profilers.id')
            ->whereNotNull('return_vouchers.supplier_id')
            ->groupBy('return_vouchers.supplier_id', 'profilers.account_title')
            ->orderBy('profilers.account_title')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers->map(function($supplier) {
                    return [
                        'id' => $supplier->supplier_id,
                        'name' => $supplier->name ?? 'Unknown Supplier'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching suppliers: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get expired return products by supplier
    public function getExpiryReturnReport(Request $request)
    {
        try {
            $supplierId = $request->input('supplier_id');
            
            if (!$supplierId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier ID is required'
                ], 400);
            }

            // Get return vouchers for specific supplier
            $returnVouchers = ReturnVouchers::select(
                'return_vouchers.*',
                'profilers.account_title as supplier_name'
            )
            ->leftJoin('profilers', 'return_vouchers.supplier_id', '=', 'profilers.id')
            ->where('return_vouchers.supplier_id', $supplierId)
            ->orderBy('return_vouchers.bill_no')
            ->orderBy('return_vouchers.exp_date', 'desc')
            ->get();

            $groupedBills = [];
            $supplierName = '';
            $totalProducts = 0;
            
            foreach ($returnVouchers as $voucher) {
                if (empty($supplierName)) {
                    $supplierName = $voucher->supplier_name ?? 'Unknown Supplier';
                }
                
                $billNo = $voucher->bill_no;
                
                if (!isset($groupedBills[$billNo])) {
                    $groupedBills[$billNo] = [
                        'bill_no' => $billNo,
                        'bill_date' => $voucher->bill_date,
                        'total_products' => 0,
                        'total_value' => 0,
                        'products' => []
                    ];
                }
                
                $productValue = $voucher->ret_quantity * $voucher->purchase_price;
                
                $groupedBills[$billNo]['products'][] = [
                    'id' => $voucher->id,
                    'product_name' => $voucher->product_name,
                    'batch_no' => $voucher->batch_no,
                    'expiry_date' => Carbon::parse($voucher->exp_date)->format('d-m-Y'),
                    'qty' => $voucher->ret_quantity,
                    'purchase_price' => $voucher->purchase_price,
                    'total_value' => $productValue
                ];
                
                $groupedBills[$billNo]['total_products']++;
                $groupedBills[$billNo]['total_value'] += $productValue;
                $totalProducts++;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'supplier_name' => $supplierName,
                    'bills' => array_values($groupedBills)
                ],
                'total_products' => $totalProducts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching expiry return report: ' . $e->getMessage()
            ], 500);
        }
    }
}