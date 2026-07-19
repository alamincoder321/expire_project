<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class PurchaseOrder extends CI_Controller
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
                    p.is_mrp,
                    pc.ProductCategory_Name,
                    u.Unit_Name
                from tbl_purchase_orderdetails pd 
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
            from tbl_purchase_order pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pm.PurchaseMaster_BranchID = '$branchId' 
            ".(!empty($data->status) ? "and pm.status = '$data->status'" : "and pm.status != 'd'")."
            $purchaseIdClause $clauses
            order by pm.PurchaseMaster_SlNo desc
        ")->result();

        $res['purchases'] = $purchases;
        echo json_encode($res);
    }

    public function order()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Order";

        $invoice = $this->mt->generatePurchaseOrderInvoice();

        $data['purchaseId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/purchase_order/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseEdit($purchaseId)
    {
        $data['title'] = "Purchase Order";
        $data['purchaseId'] = $purchaseId;
        $data['invoice'] = $this->db->query("select PurchaseMaster_InvoiceNo from tbl_purchase_order where PurchaseMaster_SlNo = ?", $purchaseId)->row()->PurchaseMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/purchase_order/purchase_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->purchase->invoice;
            $invoiceCount = $this->db->query("select * from tbl_purchase_order where PurchaseMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generatePurchaseOrderInvoice();
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
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'p',
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

            $this->db->insert('tbl_purchase_order', $purchase);
            $purchaseId = $this->db->insert_id();

            foreach ($data->cartProducts as $product) {
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'Status'                        => 'p',
                    'AddBy'                         => $this->session->userdata("FullName"),
                    'AddTime'                       => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchase_orderdetails', $purchaseDetails);
            }

            $res = ['success' => true, 'message' => 'Purchase Order Success', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updatePurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
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
                'PurchaseMaster_SubTotalAmount' => $data->purchase->subTotal,
                'PurchaseMaster_DiscountAmount' => $data->purchase->discount,
                'PurchaseMaster_Tax' => $data->purchase->vat,
                'PurchaseMaster_Freight' => $data->purchase->freight,
                'PurchaseMaster_TotalAmount' => $data->purchase->total,
                'PurchaseMaster_Description' => $data->purchase->note,
                'status' => 'p',
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
            $this->db->update('tbl_purchase_order', $purchase);

            $this->db->query("delete from tbl_purchase_orderdetails where PurchaseMaster_IDNo = ?", $purchaseId);


            foreach ($data->cartProducts as $product) {
                $purchaseDetails = array(
                    'PurchaseMaster_IDNo'           => $purchaseId,
                    'Product_IDNo'                  => $product->productId,
                    'PurchaseDetails_TotalQuantity' => $product->quantity,
                    'PurchaseDetails_Rate'          => $product->purchaseRate,
                    'PurchaseDetails_TotalAmount'   => $product->total,
                    'Status'                        => 'p',
                    'UpdateBy'                      => $this->session->userdata("FullName"),
                    'UpdateTime'                    => date('Y-m-d H:i:s'),
                    'PurchaseDetails_branchID'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_purchase_orderdetails', $purchaseDetails);
            }

            $res = ['success' => true, 'message' => 'Purchase Order Updated Successfully', 'purchaseId' => $purchaseId];
        } catch (Exception $ex) {
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
        $data['title'] = "Purchase Order Invoice";
        $data['content'] = $this->load->view('Administrator/purchase_order/purchase_bill', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchaseInvoicePrint($purchaseId)
    {
        $data['title'] = "Purchase Order Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view('Administrator/purchase_order/purchase_to_report', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function purchase_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Purchase Order Record";
        $data['content'] = $this->load->view('Administrator/purchase_order/purchase_record', $data, TRUE);
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
            from tbl_purchase_order pm
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            left join tbl_brunch br on br.brunch_id = pm.PurchaseMaster_BranchID
            where pm.PurchaseMaster_BranchID = '$branchId'
            and pm.status != 'd'
            $clauses
        ")->result();

        foreach ($purchases as $purchase) {
            $purchase->purchaseDetails = $this->db->query("
                select 
                    pd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_purchase_orderdetails pd
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
            from tbl_purchase_orderdetails pd
            join tbl_product p on p.Product_SlNo = pd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_purchase_order pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
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
            $purchase = $this->db->select('*')->where('PurchaseMaster_SlNo', $data->purchaseId)->get('tbl_purchase_order')->row();
            if ($purchase->status != 'a') {
                $res = ['success' => false, 'message' => 'Purchase Order not found'];
                echo json_encode($res);
                exit;
            }

            /*Delete Purchase Details*/
            $this->db->set('Status', 'd')->where('PurchaseMaster_IDNo', $data->purchaseId)->update('tbl_purchase_orderdetails');

            /*Delete Purchase Master Data*/
            $this->db->set('status', 'd')->where('PurchaseMaster_SlNo', $data->purchaseId)->update('tbl_purchase_order');

            $res = ['success' => true, 'message' => 'Purchase Order Successfully deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}
