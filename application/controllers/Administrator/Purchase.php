<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->model('Model_table', "mt", TRUE);
    }

    public function getPurchases()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata('BRANCHid');

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        $purchaseIdClause = "";
        if (isset($data->purchaseId) && $data->purchaseId != null) {
            $purchaseIdClause = " and pm.PurchaseMaster_SlNo = '$data->purchaseId'";

            $res['purchaseDetails'] = $this->db->query("
                select
                    pd.*,
                    p.Product_Name,
                    p.Product_Code,
                    p.ProductCategory_ID,
                    p.Product_SellingPrice,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_purchasedetails pd 
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where pd.PurchaseMaster_IDNo = '$data->purchaseId'
            ")->result();
        }
        $purchases = $this->db->query("
            select
            concat(pm.PurchaseMaster_InvoiceNo, ' - ', ifnull(s.Supplier_Name, pm.supplierName)) as invoice_text,
            pm.*,
            ifnull(s.Supplier_Code, 'General Supplier') as Supplier_Code,
            ifnull(s.Supplier_Name, pm.supplierName) as Supplier_Name,
            ifnull(s.Supplier_Mobile, pm.supplierMobile) as Supplier_Mobile,
            ifnull(s.Supplier_Address, pm.supplierMobile) as Supplier_Address,
            ifnull(s.Supplier_Type, pm.supplierType) as Supplier_Type,
            s.Supplier_Email
            from tbl_purchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pm.PurchaseMaster_BranchID = '$branchId' 
            and pm.status = 'a'
            $purchaseIdClause $clauses
            order by pm.PurchaseMaster_SlNo desc
        ")->result();

        $res['purchases'] = $purchases;
        echo json_encode($res);
    }

    public function getPurchaseDetailsForReturn()
    {
        $data = json_decode($this->input->raw_input_stream);
        $purchaseDetails = $this->db->query("
            select 
                pd.*,
                pd.PurchaseDetails_Rate as return_rate,
                p.Product_Name,
                pc.ProductCategory_Name,
                (
                    select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                    from tbl_purchasereturndetails prd
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where pr.PurchaseMaster_InvoiceNo = pm.PurchaseMaster_InvoiceNo
                    and prd.PurchaseReturnDetailsProduct_SlNo = pd.Product_IDNo
                ) as returned_quantity,
                (
                    select ifnull(sum(prd.PurchaseReturnDetails_ReturnAmount), 0) 
                    from tbl_purchasereturndetails prd
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    where pr.PurchaseMaster_InvoiceNo = pm.PurchaseMaster_InvoiceNo
                    and prd.PurchaseReturnDetailsProduct_SlNo = pd.Product_IDNo
                ) as returned_amount
            from tbl_purchasedetails pd
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            where pm.PurchaseMaster_SlNo = ?
        ", $data->purchaseId)->result();

        echo json_encode($purchaseDetails);
    }

    public function addPurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchaseReturn = array(
                'PurchaseMaster_InvoiceNo' => $data->invoice->PurchaseMaster_InvoiceNo,
                'Supplier_IDdNo' => $data->invoice->Supplier_SlNo,
                'PurchaseReturn_ReturnDate' => $data->purchaseReturn->returnDate,
                'PurchaseReturn_ReturnAmount' => $data->purchaseReturn->total,
                'PurchaseReturn_Description' => $data->purchaseReturn->note,
                'Status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'PurchaseReturn_brunchID' => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_purchasereturn', $purchaseReturn);
            $purchaseReturnId = $this->db->insert_id();

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'PurchaseReturn_SlNo'                  => $purchaseReturnId,
                    'PurchaseReturnDetailsProduct_SlNo'    => $product->Product_IDNo,
                    'exp_date'                             => $product->exp_date,
                    'PurchaseReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'PurchaseReturnDetails_ReturnAmount'   => $product->return_amount,
                    'Status'                               => 'a',
                    'AddBy'                                => $this->session->userdata("FullName"),
                    'AddTime'                              => date('Y-m-d H:i:s'),
                    'PurchaseReturnDetails_brachid'        => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $data->invoice->Supplier_SlNo)->row();
            if (empty($supplierInfo)) {
                $customerPayment = array(
                    'SPayment_date' => $data->purchaseReturn->returnDate,
                    'SPayment_invoice' => $data->invoice->PurchaseMaster_InvoiceNo,
                    'SPayment_customerID' => NULL,
                    'SPayment_TransactionType' => 'CR',
                    'SPayment_amount' => $totalReturnAmount,
                    'SPayment_Paymentby' => 'cash',
                    'SPayment_brunchid' => $this->session->userdata("BRANCHid"),
                    'SPayment_Addby' => $this->session->userdata("FullName"),
                    'SPayment_AddDAte' => date('Y-m-d H:i:s'),
                    'SPayment_status' => 'a'
                );

                $this->db->insert('tbl_supplier_payment', $customerPayment);
            }

            $res = ['success' => true, 'message' => 'Purchase return success', 'id' => $purchaseReturnId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updatePurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchaseReturnId = $data->purchaseReturn->returnId;

            $oldReturn = $this->db->query("select * from tbl_purchasereturn where PurchaseReturn_SlNo = ?", $purchaseReturnId)->row();

            $purchaseReturn = array(
                'PurchaseMaster_InvoiceNo' => $data->invoice->PurchaseMaster_InvoiceNo,
                'Supplier_IDdNo' => $data->invoice->Supplier_SlNo,
                'PurchaseReturn_ReturnDate' => $data->purchaseReturn->returnDate,
                'PurchaseReturn_ReturnAmount' => $data->purchaseReturn->total,
                'PurchaseReturn_Description' => $data->purchaseReturn->note,
                'Status' => 'a',
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'PurchaseReturn_brunchID' => $this->session->userdata('BRANCHid')
            );

            $this->db->where('PurchaseReturn_SlNo', $purchaseReturnId)->update('tbl_purchasereturn', $purchaseReturn);

            $oldDetails = $this->db->query("select * from tbl_purchasereturndetails prd where prd.PurchaseReturn_SlNo = ?", $purchaseReturnId)->result();

            foreach ($oldDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseReturnDetails_ReturnQuantity, $product->PurchaseReturnDetailsProduct_SlNo, $this->session->userdata('BRANCHid')]);
            }

            $this->db->query("delete from tbl_purchasereturndetails where PurchaseReturn_SlNo = ?", $purchaseReturnId);
            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'PurchaseReturn_SlNo' => $purchaseReturnId,
                    'PurchaseReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'exp_date' => $product->exp_date,
                    'PurchaseReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'PurchaseReturnDetails_ReturnAmount' => $product->return_amount,
                    'Status' => 'a',
                    'UpdateBy' => $this->session->userdata("FullName"),
                    'UpdateTime' => date('Y-m-d H:i:s'),
                    'PurchaseReturnDetails_brachid' => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_return_quantity = purchase_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $data->invoice->Supplier_SlNo)->row();
            if (empty($supplierInfo)) {

                $this->db->query("
                    delete from tbl_supplier_payment 
                    where SPayment_invoice = ? 
                    and SPayment_customerID is null
                    and SPayment_amount = ?
                    limit 1
                ", [
                    $data->invoice->PurchaseMaster_InvoiceNo,
                    $oldReturn->PurchaseReturn_ReturnAmount
                ]);

                $customerPayment = array(
                    'SPayment_date' => $data->purchaseReturn->returnDate,
                    'SPayment_invoice' => $data->invoice->PurchaseMaster_InvoiceNo,
                    'SPayment_customerID' => NULL,
                    'SPayment_TransactionType' => 'CR',
                    'SPayment_amount' => $totalReturnAmount,
                    'SPayment_Paymentby' => 'cash',
                    'SPayment_brunchid' => $this->session->userdata("BRANCHid"),
                    'SPayment_Addby' => $this->session->userdata("FullName"),
                    'SPayment_AddDAte' => date('Y-m-d H:i:s'),
                    'SPayment_status' => 'a'
                );

                $this->db->insert('tbl_supplier_payment', $customerPayment);
            }

            $res = ['success' => true, 'message' => 'Purchase return updated', 'id' => $purchaseReturnId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getPurchaseReturnDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pr.PurchaseReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pr.Supplier_IDdNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and prd.PurchaseReturnDetailsProduct_SlNo = '$data->productId'";
        }

        $returnDetails = $this->db->query("
            select 
                prd.*,
                p.Product_Code,
                p.Product_Name,
                pr.PurchaseMaster_InvoiceNo,
                pr.PurchaseReturn_ReturnDate,
                pr.Supplier_IDdNo,
                pr.PurchaseReturn_Description,
                s.Supplier_Code,
                s.Supplier_Name
            from tbl_purchasereturndetails prd
            join tbl_product p on p.Product_SlNo = prd.PurchaseReturnDetailsProduct_SlNo
            join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
            left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where pr.PurchaseReturn_brunchID = ?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($returnDetails);
    }

    public function order()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Entry";

        $invoice = $this->mt->generatePurchaseInvoice();

        $data['purchaseId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseEdit($purchaseId)
    {
        $data['title'] = "Purchase Update";
        $data['purchaseId'] = $purchaseId;
        $data['invoice'] = $this->db->query("select PurchaseMaster_InvoiceNo from tbl_purchasemaster where PurchaseMaster_SlNo = ?", $purchaseId)->row()->PurchaseMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function returns()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['returnId'] = 0;
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseReturnEdit($returnId)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['returnId'] = $returnId;
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function damage_entry()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Damage Entry";
        $data['damageCode'] = $this->mt->generateDamageCode();
        $data['content'] = $this->load->view('Administrator/purchase/damage_entry', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function addPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->purchase->invoice;
            $invoiceCount = $this->db->query("select * from tbl_purchasemaster where PurchaseMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generatePurchaseInvoice();
            }

            $supplierId = $data->purchase->supplierId;
            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);

                if ($data->supplier->Supplier_Type == 'N') {
                    $supplier['Supplier_Code']     = $this->mt->generateSupplierCode();
                    $supplier['Status']            = 'a';
                    $supplier['AddBy']             = $this->session->userdata("FullName");
                    $supplier['AddTime']           = date('Y-m-d H:i:s');
                    $supplier['Supplier_brinchid'] = $this->session->userdata('BRANCHid');

                    $this->db->insert('tbl_supplier', $supplier);
                    $supplierId = $this->db->insert_id();
                }
            }

            $purchase = array(
                'PurchaseMaster_InvoiceNo' => $invoice,
                'PurchaseMaster_OrderDate' => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor' => $data->purchase->purchaseFor,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_PaidAmount' => $data->purchase->paid,
                'PurchaseMaster_DueAmount' => $data->purchase->due,
                'previous_due' => $data->purchase->previousDue,
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'PurchaseMaster_BranchID' => $this->session->userdata('BRANCHid')
            );

            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']    = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType'] = 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
            }

            $this->db->insert('tbl_purchasemaster', $purchase);
            $purchaseId = $this->db->insert_id();

            foreach ($data->cartProducts as $product) {
                $barcode = date('Ymd', strtotime($product->exp_date)) . str_pad($product->productId, 5, '0', STR_PAD_LEFT);
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'exp_date'                      => $product->exp_date,
                    'short_date_month'              => $product->short_date_month,
                    'short_date'                    => date('Y-m-d', strtotime($product->exp_date . '-' . $product->short_date_month . 'months')),
                    'barcode'                       => $barcode,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'isFree'                        => $product->isFree,
                    'Status'                        => 'a',
                    'AddBy'                         => $this->session->userdata("FullName"),
                    'AddTime'                       => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasedetails', $purchaseDetails);
                $previousStock = $this->mt->productStock($product->productId);

                $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                if ($inventoryCount == 0) {
                    $inventory = array(
                        'product_id' => $product->productId,
                        'purchase_quantity' => $product->quantity,
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity + ? 
                        where product_id = ? 
                        and branch_id = ?
                    ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                }


                if ($previousStock > 0) {
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = (((Product_Purchase_Rate * ?) + ?) / ?), 
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $previousStock,
                        $product->total,
                        ($previousStock + $product->quantity),
                        $product->salesRate,
                        $product->productId
                    ]);
                } else {
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = ?,
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $product->purchaseRate,
                        $product->salesRate,
                        $product->productId
                    ]);
                }
            }

            // update purchase order status
            if (isset($data->purchase->orderId) && $data->purchase->orderId != null) {
                $this->db->query("
                    update tbl_purchase_order set 
                    status = 'a' 
                    where PurchaseMaster_SlNo = ?
                ", [$data->purchase->orderId]);

                $this->db->query("
                    update tbl_purchase_orderdetails set 
                    Status = 'a'
                    where PurchaseMaster_IDNo = ?", [$data->purchase->orderId]);
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updatePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $purchaseId = $data->purchase->purchaseId;

            $supplierId = $data->purchase->supplierId;
            if (isset($data->supplier)) {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);

                if ($data->supplier->Supplier_Type == 'N') {
                    $supplier['Supplier_Code']     = $this->mt->generateSupplierCode();
                    $supplier['Status']            = 'a';
                    $supplier['AddBy']             = $this->session->userdata("FullName");
                    $supplier['AddTime']           = date('Y-m-d H:i:s');
                    $supplier['Supplier_brinchid'] = $this->session->userdata('BRANCHid');

                    $this->db->insert('tbl_supplier', $supplier);
                    $supplierId = $this->db->insert_id();
                }
            }

            $purchase = array(
                'Supplier_SlNo' => $supplierId,
                'PurchaseMaster_InvoiceNo' => $data->purchase->invoice,
                'PurchaseMaster_OrderDate' => $data->purchase->purchaseDate,
                'PurchaseMaster_PurchaseFor' => $data->purchase->purchaseFor,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_PaidAmount' => $data->purchase->paid,
                'PurchaseMaster_DueAmount' => $data->purchase->due,
                'previous_due' => $data->purchase->previousDue,
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'a',
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'PurchaseMaster_BranchID' => $this->session->userdata('BRANCHid')
            );

            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']    = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType'] = 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
            }

            $this->db->where('PurchaseMaster_SlNo', $purchaseId);
            $this->db->update('tbl_purchasemaster', $purchase);

            $oldPurchaseDetails = $this->db->query("select * from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId)->result();
            $this->db->query("delete from tbl_purchasedetails where PurchaseMaster_IDNo = ?", $purchaseId);

            foreach ($oldPurchaseDetails as $product) {
                $previousStock = $this->mt->productStock($product->Product_IDNo);

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_quantity = purchase_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);

                $this->db->query("
                    update tbl_product set 
                    Product_Purchase_Rate = (((Product_Purchase_Rate * ?) - ?) / ?)
                    where Product_SlNo = ?
                ", [
                    $previousStock,
                    $product->PurchaseDetails_TotalAmount,
                    ($previousStock - $product->PurchaseDetails_TotalQuantity),
                    $product->Product_IDNo
                ]);
            }

            foreach ($data->cartProducts as $product) {
                $barcode = date('Ymd', strtotime($product->exp_date)) . str_pad($product->productId, 5, '0', STR_PAD_LEFT);
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'exp_date'                      => $product->exp_date,
                    'short_date_month'              => $product->short_date_month,
                    'short_date'                    => date('Y-m-d', strtotime($product->exp_date . '-' . $product->short_date_month . 'months')),
                    'barcode'                       => $barcode,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'isFree'                        => $product->isFree,
                    'Status'                        => 'a',
                    'UpdateBy'                      => $this->session->userdata("FullName"),
                    'UpdateTime'                    => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchasedetails', $purchaseDetails);
                $previousStock = $this->mt->productStock($product->productId);

                $inventoryCount = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$product->productId, $this->session->userdata('BRANCHid')])->num_rows();
                if ($inventoryCount == 0) {
                    $inventory = array(
                        'product_id' => $product->productId,
                        'purchase_quantity' => $product->quantity,
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("
                        update tbl_currentinventory 
                        set purchase_quantity = purchase_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$product->quantity, $product->productId, $this->session->userdata('BRANCHid')]);
                }

                if ($previousStock > 0) {
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = (((Product_Purchase_Rate * ?) + ?) / ?), 
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $previousStock,
                        $product->total,
                        ($previousStock + $product->quantity),
                        $product->salesRate,
                        $product->productId
                    ]);
                } else {
                    $this->db->query("
                        update tbl_product set 
                        Product_Purchase_Rate = ?,
                        Product_SellingPrice = ? 
                        where Product_SlNo = ?
                    ", [
                        $product->purchaseRate,
                        $product->salesRate,
                        $product->productId
                    ]);
                }
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Purchase Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function purchase_bill()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Invoice";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_bill', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchase_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Record";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getPurchaseRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and pm.AddBy = '$data->userFullName'";
        }

        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pm.Supplier_SlNo = '$data->supplierId'";
        }

        $purchases = $this->db->query("
            select 
                pm.*,
                ifnull(s.Supplier_Code, 'General Supplier') as Supplier_Code,
                ifnull(s.Supplier_Name, pm.supplierName) as Supplier_Name,
                ifnull(s.Supplier_Mobile, pm.supplierMobile) as Supplier_Mobile,
                ifnull(s.Supplier_Address, pm.supplierMobile) as Supplier_Address,
                ifnull(s.Supplier_Type, pm.supplierType) as Supplier_Type,
                br.Brunch_name
            from tbl_purchasemaster pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_brunch br on br.brunch_id = pm.PurchaseMaster_BranchID
            where pm.PurchaseMaster_BranchID = '$branchId'
            and pm.status = 'a'
            $clauses
        ")->result();

        foreach ($purchases as $purchase) {
            $purchase->purchaseDetails = $this->db->query("
                select 
                    pd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_purchasedetails pd
                join tbl_product p on p.Product_SlNo = pd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where pd.PurchaseMaster_IDNo = ?
                and pd.Status != 'd'
            ", $purchase->PurchaseMaster_SlNo)->result();
        }

        echo json_encode($purchases);
    }

    public function getPurchaseDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and s.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pm.PurchaseMaster_OrderDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
            select 
                pd.*,
                p.Product_Name,
                pc.ProductCategory_Name,
                pm.PurchaseMaster_InvoiceNo,
                pm.PurchaseMaster_OrderDate,
                s.Supplier_Code,
                s.Supplier_Name
            from tbl_purchasedetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
            join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.Status != 'd'
            and pd.PurchaseDetails_branchID = '$this->brunch'
            $clauses
        ")->result();

        echo json_encode($saleDetails);
    }

    /*Delete Purchase Record*/
    public function  deletePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $purchase = $this->db->select('*')->where('PurchaseMaster_SlNo', $data->purchaseId)->get('tbl_purchasemaster')->row();
            if ($purchase->status != 'a') {
                $res = ['success' => false, 'message' => 'Purchase not found'];
                echo json_encode($res);
                exit;
            }

            $returnCount = $this->db->query("select * from tbl_purchasereturn pr where pr.PurchaseMaster_InvoiceNo = ? and pr.Status = 'a'", $purchase->PurchaseMaster_InvoiceNo)->num_rows();
            if ($returnCount != 0) {
                $res = ['success' => false, 'message' => 'Unable to delete. Purchase return found'];
                echo json_encode($res);
                exit;
            }

            /*Get Purchase Details Data*/
            $purchaseDetails = $this->db->select('Product_IDNo,PurchaseDetails_TotalQuantity,PurchaseDetails_TotalAmount')->where('PurchaseMaster_IDNo', $data->purchaseId)->get('tbl_purchasedetails')->result();

            foreach ($purchaseDetails as $detail) {
                $stock = $this->mt->productStock($detail->Product_IDNo);
                if ($detail->PurchaseDetails_TotalQuantity > $stock) {
                    $res = ['success' => false, 'message' => 'Product out of stock, Purchase can not be deleted'];
                    echo json_encode($res);
                    exit;
                }
            }

            foreach ($purchaseDetails as $product) {
                $previousStock = $this->mt->productStock($product->Product_IDNo);

                $this->db->query("
                    update tbl_currentinventory 
                    set purchase_quantity = purchase_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->PurchaseDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);

                $this->db->query("
                    update tbl_product set 
                    Product_Purchase_Rate = (((Product_Purchase_Rate * ?) - ?) / ?)
                    where Product_SlNo = ?
                ", [
                    $previousStock,
                    $product->PurchaseDetails_TotalAmount,
                    ($previousStock - $product->PurchaseDetails_TotalQuantity),
                    $product->Product_IDNo
                ]);
            }

            /*Delete Purchase Details*/
            $this->db->set('Status', 'd')->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_purchasedetails');

            /*Delete Purchase Master Data*/
            $this->db->set('status', 'd')->where('PurchaseMaster_SlNo', $data->purchaseId)->update('tbl_purchasemaster');

            $res = ['success' => true, 'message' => 'Successfully deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    function addDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $damage = array(
                'Damage_InvoiceNo' => $data->Damage_InvoiceNo,
                'Damage_Date' => $data->Damage_Date,
                'Damage_Description' => $data->Damage_Description,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'Damage_brunchid' => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_damage', $damage);
            $damageId = $this->db->insert_id();

            $damageDetails = array(
                'Damage_SlNo' => $damageId,
                'Product_SlNo' => $data->Product_SlNo,
                'DamageDetails_DamageQuantity' => $data->DamageDetails_DamageQuantity,
                'damage_rate' => $data->damage_rate,
                'damage_amount' => $data->damage_amount,
                'status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s')
            );

            $this->db->insert('tbl_damagedetails', $damageDetails);

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity + ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$data->DamageDetails_DamageQuantity, $data->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $res = ['success' => true, 'message' => 'Damage entry success', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->Damage_SlNo;

            $damage = array(
                'Damage_InvoiceNo' => $data->Damage_InvoiceNo,
                'Damage_Date' => $data->Damage_Date,
                'Damage_Description' => $data->Damage_Description,
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            );

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', $damage);

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->row();

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity - ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$oldProduct->DamageDetails_DamageQuantity, $oldProduct->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $damageDetails = array(
                'Product_SlNo' => $data->Product_SlNo,
                'DamageDetails_DamageQuantity' => $data->DamageDetails_DamageQuantity,
                'damage_rate' => $data->damage_rate,
                'damage_amount' => $data->damage_amount,
                'UpdateBy' => $this->session->userdata("FullName"),
                'UpdateTime' => date('Y-m-d H:i:s')
            );

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damagedetails', $damageDetails);

            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity + ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$data->DamageDetails_DamageQuantity, $data->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $res = ['success' => true, 'message' => 'Damage updated successfully', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getDamages()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->damageId) && $data->damageId != '') {
            $clauses .= " and d.Product_SlNo = '$data->damageId'";
        }
        $damages = $this->db->query("
            select
                dd.Product_SlNo,
                dd.DamageDetails_DamageQuantity,
                dd.damage_rate,
                dd.damage_amount,
                d.Damage_SlNo,
                d.Damage_InvoiceNo,
                d.Damage_Date,
                d.Damage_Description,
                p.Product_Code,
                p.Product_Name
            from tbl_damagedetails dd
            join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
            join tbl_product p on p.Product_SlNo = dd.Product_SlNo
            where d.status = 'a' and dd.status = 'a'
            $clauses
        ")->result();

        echo json_encode($damages);
    }

    public function deleteDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $damageId = $data->damageId;

            $oldProduct = $this->db->query("select * from tbl_damagedetails where Damage_SlNo = ?", $damageId)->row();
            $this->db->query("
                update tbl_currentinventory ci 
                set ci.damage_quantity = ci.damage_quantity - ? 
                where product_id = ? 
                and ci.branch_id = ?
            ", [$oldProduct->DamageDetails_DamageQuantity, $oldProduct->Product_SlNo, $this->session->userdata('BRANCHid')]);

            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damage', ['status' => 'd']);
            $this->db->where('Damage_SlNo', $damageId)->update('tbl_damagedetails', ['status' => 'd']);

            $res = ['success' => true, 'message' => 'Damage deleted successfully', 'newCode' => $this->mt->generateDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function damage_product_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product damage list";
        $data['products'] = $this->db->query("select * from tbl_product p where p.status = 'a' and p.is_service = 'false'")->result();
        $data['content'] = $this->load->view('Administrator/purchase/damage_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function damage_select_product()
    {
        $prod_id = $this->input->post('prod_id');
        if ($prod_id == 'All') {
            $data['records'] = $this->Product_model->all_damage_product_list();
        } else {
            $data['records'] = $this->Product_model->demage_poduct_list_by_product_id($prod_id);
        }
        $this->load->view('Administrator/purchase/damage_list_search', $data);
    }

    public function purchaseInvoicePrint($purchaseId)
    {
        $data['title'] = "Purchase Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_to_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function returns_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Return";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getPurchaseReturns()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and pr.Supplier_IDdNo = '$data->supplierId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and pr.PurchaseReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->id) && $data->id != '') {
            $clauses .= " and pr.PurchaseReturn_SlNo = '$data->id'";

            $res['returnDetails'] = $this->db->query("
                select 
                    prd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_purchasereturndetails prd
                join tbl_product p on p.Product_SlNo = prd.PurchaseReturnDetailsProduct_SlNo
                where prd.PurchaseReturn_SlNo = ?
                and prd.Status = 'a'
            ", $data->id)->result();
        }

        $returns = $this->db->query("
            select 
                pr.*,
                pm.PurchaseMaster_SlNo,
                s.Supplier_Code,
                s.Supplier_Name,
                s.Supplier_Mobile,
                s.Supplier_Address
            from tbl_purchasereturn pr 
            join tbl_purchasemaster pm on pm.PurchaseMaster_InvoiceNo = pr.PurchaseMaster_InvoiceNo
            join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
            where pr.Status = 'a'
            and pr.PurchaseReturn_brunchID = ?
            $clauses
            order by pr.PurchaseReturn_SlNo desc
        ", $this->brunch)->result();

        $res['returns'] = $returns;
        echo json_encode($res);
    }

    public function purchaseReturnInvoice($id)
    {
        $data['title'] = "Purchase return Invoice";
        $data['id'] = $id;
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function deletePurchaseReturn()
    {
        $res = ['success' => false, 'message' => ''];

        try {
            $data = json_decode($this->input->raw_input_stream);

            $oldReturn = $this->db->query("select * from tbl_purchasereturn where PurchaseReturn_SlNo = ?", $data->id)->row();

            $this->db->query("delete from tbl_purchasereturn where PurchaseReturn_SlNo = ?", $data->id);
            $returnDetails = $this->db->query("select * from tbl_purchasereturndetails where PurchaseReturn_SlNo = ?", $data->id)->result();

            foreach ($returnDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory set 
                    purchase_return_quantity = purchase_return_quantity - ? 
                    where product_id = ? 
                    and branch_id = ?
                ", [$product->PurchaseReturnDetails_ReturnQuantity, $product->PurchaseReturnDetailsProduct_SlNo, $this->brunch]);
            }

            $this->db->query("delete from tbl_purchasereturndetails where PurchaseReturn_SlNo = ?", $data->id);

            $supplierInfo = $this->db->query("select * from tbl_supplier where Supplier_SlNo = ?", $oldReturn->Supplier_IDdNo)->row();
            if ($supplierInfo->Supplier_Type == 'G') {

                $this->db->query("
                    delete from tbl_supplier_payment 
                    where SPayment_invoice = ? 
                    and SPayment_customerID = ?
                    and SPayment_amount = ?
                    limit 1
                ", [
                    $oldReturn->PurchaseMaster_InvoiceNo,
                    $oldReturn->Supplier_IDdNo,
                    $oldReturn->PurchaseReturn_ReturnAmount
                ]);
            }

            $res = ['success' => true, 'message' => 'Purchase return deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function purchaseReturnDetails()
    {
        $data['title'] = "Purchase return details";
        $data['content'] = $this->load->view('Administrator/purchase/purchase_return_details', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function checkPurchaseReturn($invoice)
    {
        $res = ['found' => false];

        $returnCount = $this->db->query("select * from tbl_purchasereturn where PurchaseMaster_InvoiceNo = ? and Status = 'a'", $invoice)->num_rows();

        if ($returnCount != 0) {
            $res = ['found' => true];
        }

        echo json_encode($res);
    }
}
