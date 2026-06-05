<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\FinanceVoucher;
use App\Models\GrnDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ExpiryUpdateController extends Controller
{
    public function expiry_update_view()
    {
        $products = DB::table('products')
            ->orderBy('ProductName', 'asc')
            ->get(['ProductID', 'ProductName']);

        $data['products'] = $products;
        return view('expiry_update.expiry_update', $data);
    }

    public function get_grn_details()
    {
        $query = DB::table('grn_details')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->join('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
            ->leftJoin('sup_cus_details', 'grn.SCID', '=', 'sup_cus_details.SCID')
            ->where('grn_details.ProductStatus', 1)
            ->select(
                'grn_details.GDID',
                'grn_details.ProductID',
                'products.ProductName',
                'grn_details.batch_no',
                'grn_details.expiry_date',
                'grn_details.Quantity',
                'grn.Dated',
                'sup_cus_details.Name as SupplierName'
            );

        // Filter by product if provided
        if (request()->product_id) {
            $query->where('grn_details.ProductID', request()->product_id);
        }

        // Filter by batch number if provided
        if (request()->batch_no) {
            $query->where('grn_details.batch_no', 'like', '%' . request()->batch_no . '%');
        }

        // Filter by expiry status
        if (request()->expiry_status) {
            $currentDate = date('Y-m-d');
            $status = request()->expiry_status;

            if ($status == 'expired') {
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date < '$currentDate'");
            } elseif ($status == 'expiring_soon') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date >= '$currentDate'")
                    ->whereRaw("grn_details.expiry_date <= '$thirtyDaysLater'");
            } elseif ($status == 'near_expiry') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $ninetyDaysLater = date('Y-m-d', strtotime('+90 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date > '$thirtyDaysLater'")
                    ->whereRaw("grn_details.expiry_date <= '$ninetyDaysLater'");
            } elseif ($status == 'valid') {
                $ninetyDaysLater = date('Y-m-d', strtotime('+90 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date > '$ninetyDaysLater'");
            }
        }

        $query->orderBy('grn_details.expiry_date', 'asc');

        return DataTables::of($query)
            ->addColumn('days_until_expiry', function ($row) {
                if (!$row->expiry_date) {
                    return '<span class="text-muted">N/A</span>';
                }

                $currentDate = date('Y-m-d');
                $expiryDate = $row->expiry_date;
                $daysUntilExpiry = floor((strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24));

                if ($daysUntilExpiry < 0) {
                    $daysExpired = abs($daysUntilExpiry);
                    return '<span class="text-danger fw-bold">Expired ' . $daysExpired . ' days ago</span>';
                } elseif ($daysUntilExpiry == 0) {
                    return '<span class="text-danger fw-bold">Expires Today</span>';
                } elseif ($daysUntilExpiry == 1) {
                    return '<span class="text-danger fw-bold">Expires Tomorrow</span>';
                } elseif ($daysUntilExpiry <= 30) {
                    return '<span class="text-warning fw-bold">' . $daysUntilExpiry . ' days left</span>';
                } elseif ($daysUntilExpiry <= 90) {
                    return '<span class="text-info fw-bold">' . $daysUntilExpiry . ' days left</span>';
                } else {
                    return '<span class="text-success">' . $daysUntilExpiry . ' days left</span>';
                }
            })
            ->addColumn('status', function ($row) {
                if (!$row->expiry_date) {
                    return '<span class="badge bg-secondary">No Expiry</span>';
                }

                $currentDate = date('Y-m-d');
                $expiryDate = $row->expiry_date;
                $daysUntilExpiry = (strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24);

                if ($daysUntilExpiry < 0) {
                    return '<span class="badge bg-danger">Expired</span>';
                } elseif ($daysUntilExpiry <= 30) {
                    return '<span class="badge bg-warning">Expiring Soon</span>';
                } elseif ($daysUntilExpiry <= 90) {
                    return '<span class="badge bg-info">Near Expiry</span>';
                } else {
                    return '<span class="badge bg-success">Valid</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                $html = '<button class="btn btn-warning btn-icon btn-sm edit_expiry" 
                            data-id="' . $row->GDID . '" 
                            data-expiry="' . $row->expiry_date . '" 
                            data-product="' . $row->ProductName . '" 
                            data-batch="' . $row->batch_no . '" 
                            type="button">
                            <i class="bx bx-edit tf-icons"></i>
                        </button>';
                return $html;
            })
            ->editColumn('expiry_date', function ($row) {
                return $row->expiry_date ? date('d-M-Y', strtotime($row->expiry_date)) : 'N/A';
            })
            ->editColumn('Dated', function ($row) {
                return $row->Dated ? date('d-M-Y', strtotime($row->Dated)) : 'N/A';
            })
            ->editColumn('SupplierName', function ($row) {
                return $row->SupplierName ?? 'N/A';
            })
            ->addIndexColumn()
            ->rawColumns(['actions', 'status', 'days_until_expiry'])
            ->make(true);
    }

    public function print_expiry_report(Request $request)
    {
        $query = DB::table('grn_details')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->join('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
            ->leftJoin('sup_cus_details', 'grn.SCID', '=', 'sup_cus_details.SCID')
            ->where('grn_details.ProductStatus', 1)
            ->select(
                'grn_details.GDID',
                'products.ProductName',
                'grn_details.batch_no',
                'grn_details.expiry_date',
                'grn_details.Quantity',
                'grn.Dated',
                'sup_cus_details.Name as SupplierName'
            );

        // Apply filters
        if ($request->product_id) {
            $query->where('grn_details.ProductID', $request->product_id);
        }

        if ($request->batch_no) {
            $query->where('grn_details.batch_no', 'like', '%' . $request->batch_no . '%');
        }

        if ($request->expiry_status) {
            $currentDate = date('Y-m-d');
            $status = $request->expiry_status;

            if ($status == 'expired') {
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date < '$currentDate'");
            } elseif ($status == 'expiring_soon') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date >= '$currentDate'")
                    ->whereRaw("grn_details.expiry_date <= '$thirtyDaysLater'");
            } elseif ($status == 'near_expiry') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $ninetyDaysLater = date('Y-m-d', strtotime('+90 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date > '$thirtyDaysLater'")
                    ->whereRaw("grn_details.expiry_date <= '$ninetyDaysLater'");
            } elseif ($status == 'valid') {
                $ninetyDaysLater = date('Y-m-d', strtotime('+90 days'));
                $query->whereNotNull('grn_details.expiry_date')
                    ->whereRaw("grn_details.expiry_date > '$ninetyDaysLater'");
            }
        }

        $data['items'] = $query->orderBy('grn_details.expiry_date', 'asc')->get();
        $data['status_filter'] = $request->expiry_status;
        $data['report_date'] = date('d-M-Y');

        return view('expiry_update.print_expiry_report', $data);
    }

    public function update_expiry_date(Request $request)
    {
        $request->validate([
            'grn_details_id' => 'required',
            'expiry_date' => 'required|date'
        ]);

        try {
            DB::table('grn_details')
                ->where('GDID', $request->grn_details_id)
                ->update([
                    'expiry_date' => $request->expiry_date
                ]);

            return response()->json([
                'status' => true,
                'message' => 'Expiry date updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating expiry date: ' . $e->getMessage()
            ], 500);
        }
    }

    public function return_expired_items_view()
    {
        // Get suppliers - only those who have supplied items with expiry dates
        $suppliers = DB::table('sup_cus_details')
            ->join('grn', 'sup_cus_details.SCID', '=', 'grn.SCID')
            ->join('grn_details', 'grn.GRNID', '=', 'grn_details.GRNID')
            ->where('sup_cus_details.Type', '1')
            ->where('grn_details.RemainingQuantity', '>', 0)
            ->whereNotNull('grn_details.expiry_date')
            ->select('sup_cus_details.SCID', 'sup_cus_details.Name')
            ->groupBy('sup_cus_details.SCID', 'sup_cus_details.Name')
            ->orderBy('sup_cus_details.Name', 'asc')
            ->get();

        // Get products - only those with expiry dates and remaining quantity
        $products = DB::table('products')
            ->join('grn_details', 'products.ProductID', '=', 'grn_details.ProductID')
            ->where('grn_details.RemainingQuantity', '>', 0)
            ->whereNotNull('grn_details.expiry_date')
            ->select('products.ProductID', 'products.ProductName')
            ->groupBy('products.ProductID', 'products.ProductName')
            ->orderBy('products.ProductName', 'asc')
            ->get();

        return view('expiry_update.return_expired_items', [
            'suppliers' => $suppliers,
            'products' => $products
        ]);
    }

    public function get_returnable_items()
    {
        $currentDate = date('Y-m-d');
        $hundredDaysLater = date('Y-m-d', strtotime('+100 days'));

        $query = DB::table('grn_details')
            ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
            ->join('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
            ->leftJoin('sup_cus_details', 'grn.SCID', '=', 'sup_cus_details.SCID')
            ->where('grn_details.ProductStatus', 1)
            ->where('grn_details.RemainingQuantity', '>', 0)
            ->whereNotNull('grn_details.expiry_date')
            ->select(
                'grn_details.GDID',
                'grn_details.ProductID',
                'grn_details.GRNID',
                'products.ProductName',
                'grn_details.batch_no',
                'grn_details.expiry_date',
                'grn_details.RemainingQuantity',
                'grn_details.UnitPrice',
                'grn.SCID',
                'sup_cus_details.Name as SupplierName',
                DB::raw("DATEDIFF(grn_details.expiry_date, '$currentDate') as days_until_expiry")
            );

        // Filter by supplier if provided
        if (request()->supplier_id) {
            $query->where('grn.SCID', request()->supplier_id);
        }

        // Filter by product if provided
        if (request()->product_id) {
            $query->where('grn_details.ProductID', request()->product_id);
        }

        // Filter by expiry status
        if (request()->expiry_status) {
            $status = request()->expiry_status;

            if ($status == 'expired') {
                $query->whereRaw("grn_details.expiry_date < '$currentDate'");
            } elseif ($status == 'expiring_soon') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $query->whereRaw("grn_details.expiry_date >= '$currentDate'")
                    ->whereRaw("grn_details.expiry_date <= '$thirtyDaysLater'");
            } elseif ($status == 'near_expiry') {
                $thirtyDaysLater = date('Y-m-d', strtotime('+30 days'));
                $query->whereRaw("grn_details.expiry_date > '$thirtyDaysLater'")
                    ->whereRaw("grn_details.expiry_date <= '$hundredDaysLater'");
            }
        }

        // Exclude specific GDIDs if provided
        if (request()->exclude_gdids) {
            $excludeGdids = request()->exclude_gdids;
            if (is_array($excludeGdids) && count($excludeGdids) > 0) {
                $query->whereNotIn('grn_details.GDID', $excludeGdids);
            }
        }

        // Order by expiry date - expired and near expiry items first
        $query->orderByRaw("CASE 
            WHEN grn_details.expiry_date < '$currentDate' THEN 1
            WHEN grn_details.expiry_date <= '$hundredDaysLater' THEN 2
            ELSE 3
        END")
            ->orderBy('grn_details.expiry_date', 'asc');

        return DataTables::of($query)
            ->addColumn('days_until_expiry', function ($row) {
                $currentDate = date('Y-m-d');
                $expiryDate = $row->expiry_date;
                $daysUntilExpiry = floor((strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24));

                if ($daysUntilExpiry < 0) {
                    $daysExpired = abs($daysUntilExpiry);
                    return '<span class="text-danger fw-bold">Expired ' . $daysExpired . ' days ago</span>';
                } elseif ($daysUntilExpiry == 0) {
                    return '<span class="text-danger fw-bold">Expires Today</span>';
                } elseif ($daysUntilExpiry <= 30) {
                    return '<span class="text-warning fw-bold">' . $daysUntilExpiry . ' days left</span>';
                } else {
                    return '<span class="text-info fw-bold">' . $daysUntilExpiry . ' days left</span>';
                }
            })
            ->addColumn('status', function ($row) {
                $currentDate = date('Y-m-d');
                $expiryDate = $row->expiry_date;
                $daysUntilExpiry = (strtotime($expiryDate) - strtotime($currentDate)) / (60 * 60 * 24);

                if ($daysUntilExpiry < 0) {
                    return '<span class="badge bg-danger">Expired</span>';
                } elseif ($daysUntilExpiry <= 30) {
                    return '<span class="badge bg-warning">Expiring Soon</span>';
                } else {
                    return '<span class="badge bg-info">Near Expiry</span>';
                }
            })
            ->addColumn('select_item', function ($row) {
                return '<input type="checkbox" class="form-check-input select-item" 
                            data-id="' . $row->GDID . '"
                            data-product-id="' . $row->ProductID . '"
                            data-product-name="' . $row->ProductName . '"
                            data-batch="' . $row->batch_no . '"
                            data-expiry="' . $row->expiry_date . '"
                            data-qty="' . $row->RemainingQuantity . '"
                            data-price="' . $row->UnitPrice . '"
                            data-supplier-id="' . $row->SCID . '"
                            data-supplier-name="' . $row->SupplierName . '">';
            })
            ->editColumn('expiry_date', function ($row) {
                return date('d-M-Y', strtotime($row->expiry_date));
            })
            ->editColumn('SupplierName', function ($row) {
                return $row->SupplierName ?? 'N/A';
            })
            ->addIndexColumn()
            ->rawColumns(['select_item', 'status', 'days_until_expiry'])
            ->make(true);
    }

    public function save_return_to_supplier(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.gdid' => 'required',
            'items.*.product_id' => 'required',
            'items.*.return_qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.supplier_id' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $items = $request->items;
            $firstItem = $items[0];
            $supplierId = $firstItem['supplier_id'];
            $currentDate = date('Y-m-d');
            $currentDateTime = date('Y-m-d H:i:s');
            $userId = auth()->user()->id ?? 1;

            // Calculate total amount (after line discount)
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $this->calcGrnReturnLineTotal(
                    (float) $item['return_qty'],
                    (float) $item['unit_price'],
                    $item['discount_percentage'] ?? $item['discount'] ?? 0
                );
            }

            // Insert into grn_returns table
            $returnId = DB::table('grn_returns')->insertGetId([
                'SCID' => $supplierId,
                'ReturnDate' => $currentDate,
                'TotalAmount' => $totalAmount,
                'Status' => 'Pending',
                'CreatedBy' => $userId,
                'CreatedAt' => $currentDateTime,
                'Remarks' => 'Expired/Short Expiry Items Return'
            ]);

            // Insert into grn_return_details and update grn_details
            foreach ($items as $item) {
                $discountPct = $this->normalizeGrnReturnDiscount(
                    $item['discount_percentage'] ?? $item['discount'] ?? 0
                );
                $lineTotal = $this->calcGrnReturnLineTotal(
                    (float) $item['return_qty'],
                    (float) $item['unit_price'],
                    $discountPct
                );

                DB::table('grn_return_details')->insert([
                    'ReturnID' => $returnId,
                    'GDID' => $item['gdid'],
                    'ProductID' => $item['product_id'],
                    'BatchNo' => $item['batch_no'] ?? null,
                    'ExpiryDate' => $item['expiry_date'] ?? null,
                    'ReturnQuantity' => $item['return_qty'],
                    'UnitPrice' => $item['unit_price'],
                    'discount' => $discountPct,
                    'TotalAmount' => $lineTotal,
                    'CreatedAt' => $currentDateTime
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return processed successfully!',
                'return_id' => $returnId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error processing return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function grn_returns_list()
    {
        // Get suppliers who have returns
        $suppliers = DB::table('sup_cus_details')
            ->join('grn_returns', 'sup_cus_details.SCID', '=', 'grn_returns.SCID')
            ->select('sup_cus_details.SCID', 'sup_cus_details.Name')
            ->groupBy('sup_cus_details.SCID', 'sup_cus_details.Name')
            ->orderBy('sup_cus_details.Name', 'asc')
            ->get();

        return view('expiry_update.grn_returns_list', [
            'suppliers' => $suppliers
        ]);
    }

    public function get_grn_returns_list()
    {
        $query = DB::table('grn_returns')
            ->leftJoin('sup_cus_details', 'grn_returns.SCID', '=', 'sup_cus_details.SCID')
            ->leftJoin('users', 'grn_returns.CreatedBy', '=', 'users.id')
            ->select(
                'grn_returns.ReturnID',
                'grn_returns.SCID',
                'grn_returns.ReturnDate',
                'grn_returns.TotalAmount',
                'grn_returns.Status',
                'grn_returns.CreatedBy',
                'grn_returns.CreatedAt',
                'grn_returns.Remarks',
                'sup_cus_details.Name as SupplierName',
                'users.name as CreatedByName'
            );

        // Filter by supplier
        if (request()->supplier_id) {
            $query->where('grn_returns.SCID', request()->supplier_id);
        }

        // Filter by status
        if (request()->status) {
            $query->where('grn_returns.Status', request()->status);
        }

        // Filter by date range
        if (request()->from_date) {
            $query->where('grn_returns.ReturnDate', '>=', request()->from_date);
        }

        if (request()->to_date) {
            $query->where('grn_returns.ReturnDate', '<=', request()->to_date);
        }

        return DataTables::of($query)
            ->editColumn('ReturnDate', function ($row) {
                return date('d-M-Y', strtotime($row->ReturnDate));
            })
            ->editColumn('TotalAmount', function ($row) {
                return 'Rs. ' . number_format($row->TotalAmount, 2);
            })
            ->editColumn('CreatedAt', function ($row) {
                return $row->CreatedAt ? date('d-M-Y h:i A', strtotime($row->CreatedAt)) : 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                $statusClass = [
                    'Pending' => 'status-pending',
                    'Approved' => 'status-approved',
                    'Rejected' => 'status-rejected',
                    'Completed' => 'status-completed'
                ];

                $class = $statusClass[$row->Status] ?? 'status-pending';
                return '<span class="status-badge ' . $class . '">' . $row->Status . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $actions = '<div class="d-flex flex-wrap">';

                // View button
                $actions .= '<button class="btn btn-sm btn-info action-btn btn-view" data-id="' . $row->ReturnID . '" title="View Details">
                    <i class="bx bx-show"></i>
                </button>';

                // Print button
                $actions .= '<button class="btn btn-sm btn-secondary action-btn btn-print" data-id="' . $row->ReturnID . '" title="Print">
                    <i class="bx bx-printer"></i>
                </button>';

                // Edit button (only for Pending status)
                if ($row->Status == 'Pending') {
                    $actions .= '<button class="btn btn-sm btn-primary action-btn btn-edit" data-id="' . $row->ReturnID . '" title="Edit">
                        <i class="bx bx-edit"></i>
                    </button>';

                    // Approve button
                    $actions .= '<button class="btn btn-sm btn-success action-btn btn-approve" data-id="' . $row->ReturnID . '" title="Approve">
                        <i class="bx bx-check"></i>
                    </button>';

                    // Reject button
                    $actions .= '<button class="btn btn-sm btn-warning action-btn btn-reject" data-id="' . $row->ReturnID . '" title="Reject">
                        <i class="bx bx-x"></i>
                    </button>';

                    // Delete button
                    $actions .= '<button class="btn btn-sm btn-danger action-btn btn-delete" data-id="' . $row->ReturnID . '" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->addIndexColumn()
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    public function approve_grn_return(Request $request)
    {
        $request->validate([
            'return_id' => 'required|exists:grn_returns,ReturnID'
        ]);

        DB::beginTransaction();
        try {
            $return = DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->first();
                
                $supplier = DB::table('sup_cus_details')
                ->where('SCID', $return->SCID)
                ->first();

            if (!$return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return request not found'
                ], 404);
            }

            if ($return->Status != 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending returns can be approved'
                ], 400);
            }

            $userId = auth()->user()->id ?? 1;
            $currentDateTime = date('Y-m-d H:i:s');

            // Update return status
            DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->update([
                    'Status' => 'Approved',
                    'ApprovedBy' => $userId,
                    'ApprovedAt' => $currentDateTime,
                    'Remarks' => $request->remarks ? $return->Remarks . "\nApproval: " . $request->remarks : $return->Remarks
                ]);

            $returnDetails = DB::table('grn_return_details')
                ->where('ReturnID', $request->return_id)
                ->get();

            $productsToRefresh = [];
            foreach ($returnDetails as $detail) {
                $this->deductGrnStockForReturn(
                    (int) $detail->ProductID,
                    (float) $detail->ReturnQuantity,
                    (int) $return->SCID
                );
                $productsToRefresh[(int) $detail->ProductID] = true;
            }

            $stockController = new StockController();
            foreach (array_keys($productsToRefresh) as $productId) {
                $stockController->avaliableQuantity($productId);
            }



            $voucher = generateVoucherNumber("pharmacy_return",auth()->user()->id);

            $voucher_data = [
                "voucher_number" =>$voucher,
                "user_id"   =>  auth()->user()->id,
                "voucher_type"   => "pharmacy_return",
                "voucher_date"   => date("Y-m-d"),
                "total_amount"   => $return->TotalAmount,
                "remarks"        =>  "Grn approved by  ".auth()->user()->name,
                "created_by"    => auth()->user()->id,
                "approved_by"   => auth()->user()->id,
                "approved_by"   => auth()->user()->id,
                "approved_at"   => date("Y-m-d H:i:s"),
            ];
            $voucher = FinanceVoucher::create($voucher_data);
            $voucher_id = $voucher->id;
    
            $amount = ($return->TotalAmount);
            $remarks = 'Pharmacy Return to Supplier. grn_return approved by '.auth()->user()->name;
    
            // pharmacy purchase debit   Supplier account credit
            make_entry($voucher_id,financeHeadId('pharmacy_purchase'),0,$amount,"grn",$return->ReturnID,auth()->user()->id,$remarks);
            make_entry($voucher_id,$supplier->finance_head_id,$amount,0,"grn_return",$return->ReturnID,auth()->user()->id,$remarks);


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return request approved successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error approving return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deduct return quantity from active GRN batches (FIFO by GDID).
     * Example: return 10 with batches 2, 4, 8 → leaves third batch at 4.
     */
    private function deductGrnStockForReturn(int $productId, float $returnQuantity, int $supplierId): void
    {
        $remainingToReturn = $returnQuantity;

        $grnDetails = GrnDetails::query()
            ->join('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
            ->where('grn_details.ProductID', $productId)
            ->where('grn_details.ProductStatus', 1)
            
            ->where('grn_details.RemainingQuantity', '>', 0)
            ->orderBy('grn_details.GDID', 'asc')
            ->select('grn_details.*')
            ->get();

        foreach ($grnDetails as $detail) {
            if ($remainingToReturn <= 0) {
                break;
            }

            $available = (float) $detail->RemainingQuantity;

            if ($remainingToReturn <= $available) {
                $deductQty = $remainingToReturn;
                $newRemaining = $available - $deductQty;
                $update = [
                    'RemainingQuantity' => $newRemaining,
                    'TotalReturn' => ((float) $detail->TotalReturn) + $deductQty,
                ];
                if ($newRemaining == 0) {
                    $update['ProductStatus'] = 0;
                }
                GrnDetails::where('GDID', $detail->GDID)->update($update);
                $remainingToReturn = 0;
            } else {
                $deductQty = $available;
                GrnDetails::where('GDID', $detail->GDID)->update([
                    'RemainingQuantity' => 0,
                    'TotalReturn' => ((float) $detail->TotalReturn) + $deductQty,
                    'ProductStatus' => 0,
                ]);
                $remainingToReturn -= $deductQty;
            }
        }

        if ($remainingToReturn > 0) {
            throw new \RuntimeException(
                "Insufficient stock for product ID {$productId}. Short by {$remainingToReturn}."
            );
        }
    }

    public function reject_grn_return(Request $request)
    {
        $request->validate([
            'return_id' => 'required|exists:grn_returns,ReturnID',
            'remarks' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $return = DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->first();

            if (!$return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return request not found'
                ], 404);
            }

            if ($return->Status != 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending returns can be rejected'
                ], 400);
            }

            $userId = auth()->user()->id ?? 1;
            $currentDateTime = date('Y-m-d H:i:s');


            // Update return status
            DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->update([
                    'Status' => 'Rejected',
                    'ApprovedBy' => $userId,
                    'ApprovedAt' => $currentDateTime,
                    'Remarks' => $return->Remarks . "\nRejection: " . $request->remarks
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return request rejected successfully! Quantities have been restored.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error rejecting return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete_grn_return(Request $request)
    {
        $request->validate([
            'return_id' => 'required|exists:grn_returns,ReturnID'
        ]);

        DB::beginTransaction();
        try {
            $return = DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->first();

            if (!$return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return request not found'
                ], 404);
            }

            if ($return->Status != 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending returns can be deleted'
                ], 400);
            }



            // Delete return (cascade will delete details)
            DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->delete();
            DB::table('grn_return_details')
                ->where('ReturnID', $request->return_id)
                ->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return request deleted successfully! Quantities have been restored.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error deleting return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view_grn_return($id)
    {
        // Get return header
        $return = DB::table('grn_returns')
            ->leftJoin('sup_cus_details', 'grn_returns.SCID', '=', 'sup_cus_details.SCID')
            ->leftJoin('users as created_user', 'grn_returns.CreatedBy', '=', 'created_user.id')
            ->leftJoin('users as approved_user', 'grn_returns.ApprovedBy', '=', 'approved_user.id')
            ->where('grn_returns.ReturnID', $id)
            ->select(
                'grn_returns.*',
                'sup_cus_details.Name as SupplierName',
                'created_user.name as CreatedByName',
                'approved_user.name as ApprovedByName'
            )
            ->first();

        if (!$return) {
            abort(404, 'Return request not found');
        }

        // Get return details
        $returnDetails = DB::table('grn_return_details')
            ->join('products', 'grn_return_details.ProductID', '=', 'products.ProductID')
            ->where('grn_return_details.ReturnID', $id)
            ->select(
                'grn_return_details.*',
                'products.ProductName'
            )
            ->get();

        return view('expiry_update.view_grn_return', [
            'return' => $return,
            'returnDetails' => $returnDetails
        ]);
    }

    public function edit_grn_return($id)
    {
        // Get return header
        $return = DB::table('grn_returns')
            ->leftJoin('sup_cus_details', 'grn_returns.SCID', '=', 'sup_cus_details.SCID')
            ->where('grn_returns.ReturnID', $id)
            ->select(
                'grn_returns.*',
                'sup_cus_details.Name as SupplierName'
            )
            ->first();

        if (!$return) {
            abort(404, 'Return request not found');
        }

        if ($return->Status != 'Pending') {
            return redirect()->route('expiry.view_grn_return', $id)
                ->with('error', 'Only pending returns can be edited');
        }

        // Get return details with product info
        $returnDetails = DB::table('grn_return_details')
            ->join('products', 'grn_return_details.ProductID', '=', 'products.ProductID')
            ->join('grn_details', 'grn_return_details.GDID', '=', 'grn_details.GDID')
            ->where('grn_return_details.ReturnID', $id)
            ->select(
                'grn_return_details.*',
                'products.ProductName',
                'grn_details.RemainingQuantity',
                'grn_details.batch_no as BatchNo',
                'grn_details.expiry_date as ExpiryDate',
                DB::raw('(grn_details.RemainingQuantity + grn_return_details.ReturnQuantity) as MaxAvailableQty')
            )
            ->get();

        return view('expiry_update.edit_grn_return', [
            'return' => $return,
            'returnDetails' => $returnDetails
        ]);
    }

    public function update_grn_return(Request $request)
    {
        $request->validate([
            'return_id' => 'required|exists:grn_returns,ReturnID',
            'items' => 'array',
            'items.*.detail_id' => 'required',
            'items.*.gdid' => 'required',
            'items.*.return_qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'new_items' => 'array',
            'new_items.*.gdid' => 'required',
            'new_items.*.return_qty' => 'required|numeric|min:0.01',
            'new_items.*.unit_price' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            // Get the return
            $return = DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->first();

            if (!$return) {
                return response()->json([
                    'status' => false,
                    'message' => 'Return request not found'
                ], 404);
            }

            if ($return->Status != 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => 'Only pending returns can be edited'
                ], 400);
            }

            $userId = auth()->user()->id ?? 1;
            $currentDateTime = date('Y-m-d H:i:s');
            $grandTotal = 0;

            // Get current return details
            $currentDetails = DB::table('grn_return_details')
                ->where('ReturnID', $request->return_id)
                ->get()
                ->keyBy('ReturnDetailID');

            // Process removed items - restore quantities
            if (isset($request->removed_items) && is_array($request->removed_items)) {
                foreach ($request->removed_items as $removedItem) {
                    $detailId = $removedItem['detail_id'];
                    $gdid = $removedItem['gdid'];
                    $originalQty = $removedItem['original_qty'];

                    // Delete the detail record
                    DB::table('grn_return_details')
                        ->where('ReturnDetailID', $detailId)
                        ->delete();
                }
            }

            // Process updated/existing items
            if (isset($request->items) && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $newQty = (float) $item['return_qty'];
                    $unitPrice = (float) $item['unit_price'];
                    $discountPct = $this->normalizeGrnReturnDiscount(
                        $item['discount_percentage'] ?? $item['discount'] ?? 0
                    );
                    $lineTotal = $this->calcGrnReturnLineTotal($newQty, $unitPrice, $discountPct);
                    $grandTotal += $lineTotal;

                    $detailId = $item['detail_id'];
                    $gdid = $item['gdid'];

                    // Check if this is an existing detail
                    if (isset($currentDetails[$detailId])) {
                        // Update the detail record
                        DB::table('grn_return_details')
                            ->where('ReturnDetailID', $detailId)
                            ->update([
                                'ReturnQuantity' => $newQty,
                                'UnitPrice' => $unitPrice,
                                'discount' => $discountPct,
                                'TotalAmount' => $lineTotal
                            ]);
                    }
                }
            }

            // Process new items
            if (isset($request->new_items) && is_array($request->new_items)) {
                foreach ($request->new_items as $newItem) {
                    $returnQty = (float) $newItem['return_qty'];
                    $unitPrice = (float) $newItem['unit_price'];
                    $discountPct = $this->normalizeGrnReturnDiscount(
                        $newItem['discount_percentage'] ?? $newItem['discount'] ?? 0
                    );
                    $lineTotal = $this->calcGrnReturnLineTotal($returnQty, $unitPrice, $discountPct);
                    $grandTotal += $lineTotal;

                    $gdid = $newItem['gdid'];
                    $productId = $newItem['product_id'];

                    // Verify available quantity
                    $grnDetail = DB::table('grn_details')
                        ->where('GDID', $gdid)
                        ->first();

                    if (!$grnDetail) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid item selected'
                        ], 400);
                    }

                    if ($grnDetail->RemainingQuantity < $returnQty) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => 'Insufficient quantity available for new item'
                        ], 400);
                    }

                    // Insert new detail record
                    DB::table('grn_return_details')->insert([
                        'ReturnID' => $request->return_id,
                        'GDID' => $gdid,
                        'ProductID' => $productId,
                        'ReturnQuantity' => $returnQty,
                        'UnitPrice' => $unitPrice,
                        'discount' => $discountPct,
                        'TotalAmount' => $lineTotal,
                        'CreatedAt' => $currentDateTime
                    ]);
                }
            }

            // Update return header
            DB::table('grn_returns')
                ->where('ReturnID', $request->return_id)
                ->update([
                    'TotalAmount' => $grandTotal,
                    'Remarks' => $return->Remarks . "\nEdited on: " . $currentDateTime . " by User ID: " . $userId
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Return updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error updating return: ' . $e->getMessage()
            ], 500);
        }
    }

    public function print_grn_return($id)
    {
        $return = DB::table('grn_returns')
            ->join('sup_cus_details', 'grn_returns.SCID', '=', 'sup_cus_details.SCID')
            ->leftJoin('users', 'grn_returns.CreatedBy', '=', 'users.id')
            ->where('grn_returns.ReturnID', $id)
            ->select(
                'grn_returns.*',
                'sup_cus_details.Name as SupplierName',
                'sup_cus_details.Address as SupplierAddress',
                'users.name as CreatedByName'
            )
            ->first();

        if (!$return) {
            abort(404, 'Return not found');
        }

        $returnDetails = DB::table('grn_return_details')
            ->join('products', 'grn_return_details.ProductID', '=', 'products.ProductID')
            ->join('grn_details', 'grn_return_details.GDID', '=', 'grn_details.GDID')
            ->where('grn_return_details.ReturnID', $id)
            ->select(
                'grn_return_details.*',
                'products.ProductName',
                'grn_details.batch_no as BatchNo',
                'grn_details.expiry_date as ExpiryDate'
            )
            ->get();

        return view('expiry_update.print_grn_return', compact('return', 'returnDetails'));
    }

    private function normalizeGrnReturnDiscount($discount): float
    {
        $discountPct = (float) $discount;
        if ($discountPct < 0) {
            return 0;
        }
        if ($discountPct > 100) {
            return 100;
        }

        return $discountPct;
    }

    private function calcGrnReturnLineTotal(float $qty, float $unitPrice, $discount): float
    {
        $discountPct = $this->normalizeGrnReturnDiscount($discount);
        $lineGross = $qty * $unitPrice;

        return $lineGross - ($lineGross * $discountPct / 100);
    }
}
