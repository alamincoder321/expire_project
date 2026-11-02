<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller
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
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
        $vars['branch_info'] = $this->Billing_model->company_branch_profile($this->brunch);
        $this->load->vars($vars);
    }
    public function index()
    {
        $data['title'] = "Product Sales";
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function supplierList()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Supplier List";
        $data['content'] = $this->load->view('Administrator/reports/supplierList', $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function price_listprint()
    {
        $data['title'] = "Customer List";
        $select_one = $this->session->userdata('select_one');
        $category = $category = $this->session->userdata('category');
        $product = $product = $this->session->userdata('product');


        if ($select_one == 'All') {
            $data['products'] = $this->Product_model->get_all_product_price_list();
        } elseif ($select_one == 'Category') {
            $data['category'] = $this->Other_model->get_single_category_info($category);
        } elseif ($select_one == 'Product') {
            $data['products'] = $this->Product_model->get_singel_product_pice_list($product);
        }
        $this->load->view('Administrator/reports/price_listprint', $data);
    }
    public function employeelist()
    {
        $data['title'] = "Employee List";
        $this->load->view('Administrator/reports/employeelist', $data);
    }
    public function price_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Price List";
        $data['content'] = $this->load->view('Administrator/reports/price_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function productlist()
    {
        $data['title']  = 'Product List';
        $data['content'] = $this->load->view('Administrator/reports/productList', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function deletedProductList()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title']  = 'Deleted Product List';
        $data['content'] = $this->load->view('Administrator/reports/deletedProductList', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function campaignlist()
    {
        $data['title']  = 'Campaign Product List';
        $data['content'] = $this->load->view('Administrator/reports/campaignList', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function cashStatment()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $datas['title'] = "Cash Statement";
        $data['content'] = $this->load->view('Administrator/reports/cashStatement', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function specialReport()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $datas['title'] = "Cash Statement";
        $data['content'] = $this->load->view('Administrator/reports/special', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function special_report_all_data()
    {
        $data = json_decode($this->input->raw_input_stream);

        $brunch  = $this->session->userdata('BRANCHid');
        $dateFrom =  date('Y-m-d', strtotime($data->fromDate . ' -1 day'));
        $dateTo   = $data->toDate;

        $userQuery = "
            SELECT
                u.User_SlNo,
                u.FullName,
                u.FullName AS AddBy
            FROM tbl_user u
            WHERE u.status = 'a'
            AND u.Brunch_ID = '$brunch'
            " . (!empty($data->userFullName) ? " AND u.FullName = " . $this->db->escape($data->userFullName) : "") . "
            ORDER BY u.FullName ASC
        ";

        $users = $this->db->query($userQuery)->result();

        $totals = [];

        foreach ($users as $user) {
            $reportFrom = $this->db
                ->query("select ds.close_date_time from tbl_dayclose ds 
                        where ds.status = 'a' 
                        and DATE_FORMAT(ds.close_date_time, '%Y-%m-%d') = ? 
                        and ds.user_id = ?", [$dateFrom, $user->User_SlNo])
                ->row()
                ->close_date_time ?? $data->fromDate . ' 00:00:00';

            $reportTo = $this->db
                ->query("select ds.close_date_time from tbl_dayclose ds 
                        where ds.status = 'a' 
                        and DATE_FORMAT(ds.close_date_time, '%Y-%m-%d') = ? 
                        and ds.user_id = ?", [$dateTo, $user->User_SlNo])
                ->row()
                ->close_date_time ?? $dateTo . ' 23:59:59';

            $sales = $this->db
                ->select("
                IFNULL(SUM(SaleMaster_TotalSaleAmount), 0) AS totalsales,
                IFNULL(SUM(SaleMaster_cashPaid), 0) AS cashamount,
                IFNULL(SUM(SaleMaster_bankPaid), 0) AS bankamount,
                IFNULL(SUM(SaleMaster_DueAmount), 0) AS dueamount,
                IFNULL(SUM(returnAmount), 0) AS changeamount")
                ->where('Status', 'a')
                ->where('AddBy', $user->FullName)
                ->where('AddTime >', $reportFrom)
                ->where('AddTime <=', $reportTo)
                ->get('tbl_salesmaster')
                ->row();


            $return = $this->db
                ->select("IFNULL(SUM(SaleReturn_ReturnAmount), 0) AS returnamount")
                ->where('Status', 'a')
                ->where('AddBy', $user->FullName)
                ->where('AddTime >', $reportFrom)
                ->where('AddTime <=', $reportTo)
                ->get('tbl_salereturn')
                ->row();
            
            $receiveamount = $this->db
                ->select("IFNULL(SUM(CPayment_amount), 0) AS receiveamount")
                ->where('CPayment_status', 'a')
                ->where('CPayment_TransactionType', 'CR')
                ->where('CPayment_Addby', $user->FullName)
                ->where('CPayment_AddDAte >', $reportFrom)
                ->where('CPayment_AddDAte <=', $reportTo)
                ->get('tbl_customer_payment')
                ->row();
            
            $paymentamount = $this->db
                ->select("IFNULL(SUM(CPayment_amount), 0) AS paymentamount")
                ->where('CPayment_status', 'a')
                ->where('CPayment_TransactionType', 'CP')
                ->where('CPayment_Addby', $user->FullName)
                ->where('CPayment_AddDAte >', $reportFrom)
                ->where('CPayment_AddDAte <=', $reportTo)
                ->get('tbl_customer_payment')
                ->row();

            $exchange = $this->db
                ->select("
                IFNULL(SUM(total), 0) AS exchangeamount")
                ->where('Status', 'a')
                ->where('AddBy', $user->FullName)
                ->where('AddTime >', $reportFrom)
                ->where('AddTime <=', $reportTo)
                ->get('tbl_exchange')
                ->row();


            $user->totalsales = $sales->totalsales ?? 0;
            $user->cashamount = $sales->cashamount ?? 0;
            $user->bankamount = $sales->bankamount ?? 0;
            $user->dueamount = $sales->dueamount ?? 0;
            $user->changeamount = $sales->changeamount ?? 0;
            $user->returnamount = $return->returnamount ?? 0;
            $user->exchangeamount = $exchange->exchangeamount ?? 0;
            $user->receiveamount = $receiveamount->receiveamount ?? 0;
            $user->paymentamount = $paymentamount->paymentamount ?? 0;

            $user->reportFrom = $reportFrom;
            $user->reportTo   = $reportTo;

            $totals[] = $user;
        }

        echo json_encode($totals);
    }


    public function balanceSheet()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $datas['title'] = "Balance In Out";
        $data['content'] = $this->load->view('Administrator/reports/balanceSheet', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function branch_stock_print()
    {
        $data['Branch_ID'] = $BranchID = $this->session->userdata('Branch_ID');
        $data['Branch_category'] = $category = $this->session->userdata('Branch_category');
        $this->session->set_userdata($data);
        if ($category != 'All') {
            $this->db->SELECT("tbl_product.*, tbl_productcategory.*,tbl_unit.*,tbl_color.*,tbl_brand.* FROM tbl_product left join tbl_productcategory on tbl_productcategory.ProductCategory_SlNo= tbl_product.ProductCategory_ID left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID  LEFT JOIN tbl_color ON tbl_color.color_SiNo=tbl_product.color LEFT JOIN tbl_brand ON tbl_brand.brand_SiNo=tbl_product.brand LEFT JOIN tbl_country ON tbl_country.Country_SlNo=tbl_product.country where tbl_product.ProductCategory_ID = '$category' AND tbl_product.Product_branchid = '$BranchID'");
            $query = $this->db->get();
            $result = $query->result();
            $data['product'] = $result;
            $data['show'] = 1;
        } else {
            $this->db->SELECT('*');
            $this->db->from('tbl_productcategory');
            $this->db->where('category_branchid', $BranchID);
            $query = $this->db->get();
            $category = $query->result();

            foreach ($category as $vcategory) {
                $categoryid = $vcategory->ProductCategory_SlNo;
                $this->db->SELECT("tbl_product.*, tbl_productcategory.*,tbl_unit.*,tbl_color.*,tbl_brand.* FROM tbl_product left join tbl_productcategory on tbl_productcategory.ProductCategory_SlNo= tbl_product.ProductCategory_ID left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID  LEFT JOIN tbl_color ON tbl_color.color_SiNo=tbl_product.color LEFT JOIN tbl_brand ON tbl_brand.brand_SiNo=tbl_product.brand LEFT JOIN tbl_country ON tbl_country.Country_SlNo=tbl_product.country where tbl_product.ProductCategory_ID = '$categoryid' AND tbl_product.Product_branchid = '$BranchID'");
                $query = $this->db->get();
                $productCat[] = $query->result();
            }

            $data['category'] = $category;
            $data['productCat'] = @$productCat;
            $data['show'] = 0;
        }
        $this->load->view('Administrator/reports/branch_stock_print', $data);
    }

    public function selectOne($value)
    {
        if ($value == 'Category') {
            $category = $this->Billing_model->select_category_by_branch($this->brunch);
?>
            <select id="category" data-placeholder="Choose a Category ....." class="chosen-select" style="width:200px">
                <option value=""></option>
                <?php
                foreach ($category as $vcategory) {
                ?>
                    <option value="<?php echo $vcategory->ProductCategory_SlNo; ?>"><?php echo $vcategory->ProductCategory_Name; ?></option>
                <?php
                }
                ?>
            </select>
        <?php
        } else {
            $products = $this->Product_model->products_by_brunch();
            //echo "<pre>";print_r($product);exit;
        ?>
            <select id="product" data-placeholder="Choose a Product ....." class="chosen-select" style="width:200px">
                <option value=""></option>
                <?php
                foreach ($products as $product) {
                ?>
                    <option value="<?php echo $product->Product_SlNo; ?>"><?php echo $product->Product_Name; ?>-<?php echo $product->Product_Code; ?></option>
                <?php
                }
                ?>
            </select>
<?php
        }
    }

    public function price_list_report()
    {
        $session['select_one'] = $select_one = $this->input->post('select_one');
        $session['category'] = $category = $this->input->post('category');
        $session['product'] = $product = $this->input->post('product');
        $this->session->set_userdata($session);

        if ($select_one == 'All') {
            $data['products'] = $this->Product_model->get_all_product_price_list();
        } elseif ($select_one == 'Category') {
            $data['category'] = $this->Other_model->get_single_category_info($category);
        } elseif ($select_one == 'Product') {
            $data['products'] = $this->Product_model->get_singel_product_pice_list($product);
        }

        $this->load->view('Administrator/reports/price_list_search', $data);
    }

    public function profitLossPrint()
    {
        $data['searchtype'] = $this->session->userdata('searchtype');
        $data['ProductID'] = $this->session->userdata('ProductID');
        $data['startdate'] = $this->session->userdata('startdate');
        $data['enddate'] = $this->session->userdata('enddate');
        $this->load->view('Administrator/reports/profit_loss', $data);
    }

    public function reOrderList()
    {
        $data['title'] = "Re-Order List";
        $data['content'] = $this->load->view('Administrator/reports/reorder_list', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function dayBook()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Daily Book";
        $data['content'] = $this->load->view('Administrator/reports/day_book', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function balance_sheet()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $datas['title'] = "Balance Sheet";
        $data['content'] = $this->load->view('Administrator/reports/balance_sheet', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getBalanceSheet()
    {
        $res = ['success'   => false, 'message'  => 'Invalid'];

        try {
            $branchId = $this->brunch;
            $data       = json_decode($this->input->raw_input_stream);
            $date       = null;

            if (isset($data->date) && $data->date != '') {
                $date = new DateTime($data->date);
                $date = $date->modify('+1 day')->format('Y-m-d');
            }

            $cash_balance = $this->mt->getTransactionSummary($date)->cash_balance;
            $bank_accounts = $this->mt->getBankTransactionSummary(null, $date);
            $loan_accounts = $this->mt->getLoanTransactionSummary(null, $date);
            $invest_accounts = $this->mt->getInvestmentTransactionSummary(null, $date);

            //assets
            $assets = $this->mt->assetsReport('', $date);

            $assets = array_reduce($assets, function ($prev, $curr) {
                return $prev + $curr->approx_amount;
            });

            //customer prev due adjust
            $customer_prev_due = $this->db->query("
                SELECT ifnull(sum(previous_due), 0) as amount
                from tbl_customer
                where Customer_brunchid = '$this->brunch'
            ")->row()->amount;

            //customer dues
            $customer_dues = $this->mt->customerDue(" and c.status = 'a'", $date);
            $bad_debts = $this->mt->customerDue(" and c.status = 'd'", $date);

            $customer_dues = array_reduce($customer_dues, function ($prev, $curr) {
                return $prev + $curr->dueAmount;
            });

            $bad_debts = array_reduce($bad_debts, function ($prev, $curr) {
                return $prev + $curr->dueAmount;
            });

            //stock values
            $stocks = $this->db->query("
                select
                    (select ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) 
                        from tbl_purchasedetails pd 
                        join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                        where pd.Product_IDNo = p.Product_SlNo
                        and pd.PurchaseDetails_branchID = '$branchId'
                        and pd.Status = 'a'
                        " . (isset($date) && $date != null ? " and pm.PurchaseMaster_OrderDate < '$date'" : "") . "
                    ) as purchased_quantity,
                            
                    (select ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) 
                        from tbl_purchasereturndetails prd 
                        join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                        where prd.PurchaseReturnDetailsProduct_SlNo = p.Product_SlNo
                        and prd.PurchaseReturnDetails_brachid = '$branchId'
                        " . (isset($date) && $date != null ? " and pr.PurchaseReturn_ReturnDate < '$date'" : "") . "
                    ) as purchase_returned_quantity,
                            
                    (select ifnull(sum(sd.SaleDetails_TotalQuantity), 0) 
                        from tbl_saledetails sd
                        join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                        where sd.Product_IDNo = p.Product_SlNo
                        and sd.SaleDetails_BranchId  = '$branchId'
                        and sd.Status = 'a'
                        " . (isset($date) && $date != null ? " and sm.SaleMaster_SaleDate < '$date'" : "") . "
                    ) as sold_quantity,
                            
                    (select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                        from tbl_salereturndetails srd 
                        join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                        where srd.SaleReturnDetailsProduct_SlNo = p.Product_SlNo
                        and srd.SaleReturnDetails_brunchID = '$branchId'
                        and sr.Status = 'a'
                        " . (isset($date) && $date != null ? " and sr.SaleReturn_ReturnDate < '$date'" : "") . "
                    ) as sales_returned_quantity,
                            
                    (select ifnull(sum(dmd.DamageDetails_DamageQuantity), 0) 
                        from tbl_damagedetails dmd
                        join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                        where dmd.Product_SlNo = p.Product_SlNo
                        and dmd.status = 'a'
                        and dm.Damage_brunchid = '$branchId'
                        " . (isset($date) && $date != null ? " and dm.Damage_Date < '$date'" : "") . "
                    ) as damaged_quantity,
                
                    (select ifnull(sum(trd.quantity), 0)
                        from tbl_transferdetails trd
                        join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                        where trd.product_id = p.Product_SlNo
                        and tm.transfer_from = '$branchId'
                        " . (isset($date) && $date != null ? " and tm.transfer_date < '$date'" : "") . "
                    ) as transferred_from_quantity,

                    (select ifnull(sum(trd.quantity), 0)
                        from tbl_transferdetails trd
                        join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                        where trd.product_id = p.Product_SlNo
                        and tm.transfer_to = '$branchId'
                        " . (isset($date) && $date != null ? " and tm.transfer_date < '$date'" : "") . "
                    ) as transferred_to_quantity,
                            
                    (select ifnull(sum(exd.quantity), 0)
                        from tbl_exchange_detail exd
                        join tbl_exchange ex on ex.id = exd.exchange_id
                        where exd.product_id = p.Product_SlNo
                        and ex.branchId = '$branchId'
                        and ex.Status = 'a'
                        and exd.Status = 'a'
                        " . (isset($date) && $date != null ? " and ex.date < '$date'" : "") . "
                    ) as exchange_out_quantity,

                    (select ifnull(sum(sd.SaleDetails_TotalQuantity), 0)
                        from (select distinct exd.exchange_id, exd.sale_detail_id from tbl_exchange_detail exd where exd.Status = 'a') exr
                        join tbl_exchange ex on ex.id = exr.exchange_id
                        join tbl_saledetails sd on sd.SaleDetails_SlNo = exr.sale_detail_id
                        where sd.Product_IDNo = p.Product_SlNo
                        and ex.branchId = '$branchId'
                        and ex.Status = 'a'
                        " . (isset($date) && $date != null ? " and ex.date < '$date'" : "") . "
                    ) as exchange_in_quantity,
                            
                    (select (purchased_quantity + sales_returned_quantity + transferred_to_quantity + exchange_in_quantity) - (sold_quantity + purchase_returned_quantity + damaged_quantity + transferred_from_quantity + exchange_out_quantity)) as current_quantity,
                    (select p.Product_Purchase_Rate * current_quantity) as stock_value
                from tbl_product p
                where p.status = 'a' 
                and p.is_service = 'false'
            ")->result();

            $stock_value = array_sum(
                array_map(function ($product) {
                    return $product->stock_value;
                }, $stocks)
            );

            //stock at book cost (same cost figures used in profit/loss & supplier due)
            $book_stock = $this->bookStockSummary($branchId, $date)->total;

            //difference between stock at current avg rate and stock at book cost
            $stock_valuation_adjustment = $stock_value - $book_stock;

            //supplier prev due adjust
            $supplier_prev_due = $this->db->query("
                SELECT ifnull(sum(previous_due), 0) as amount
                from tbl_supplier
                where Supplier_brinchid = '$this->brunch'
            ")->row()->amount;

            //supplier due
            $supplier_dues = $this->mt->supplierDue("", $date);

            $supplier_dues = array_reduce($supplier_dues, function ($prev, $curr) {
                return $prev + $curr->due;
            });

            //profit loss
            $sales = $this->db->query("
                select 
                    sm.*
                from tbl_salesmaster sm
                where sm.SaleMaster_branchid = ? 
                and sm.Status = 'a'
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
            ", $this->session->userdata('BRANCHid'))->result();

            foreach ($sales as $sale) {
                $sale->saleDetails = $this->db->query("
                    select
                        sd.*,
                        (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                        (sd.SaleDetails_Rate * sd.SaleDetails_TotalQuantity) as SaleDetails_TotalAmount,
                        (select SaleDetails_TotalAmount - purchased_amount) as profit_loss
                    from tbl_saledetails sd
                    where sd.SaleMaster_IDNo = ?
                ", $sale->SaleMaster_SlNo)->result();
            }

            $profits = array_reduce($sales, function ($prev, $curr) {
                return $prev + $curr->SaleMaster_SubTotalAmount - array_reduce($curr->saleDetails, function ($p, $c) {
                    return $p + $c->purchased_amount;
                });
            });

            $total_transport_cost = array_reduce($sales, function ($prev, $curr) {
                return $prev + $curr->SaleMaster_Freight;
            });

            $total_discount = array_reduce($sales, function ($prev, $curr) {
                return $prev + $curr->SaleMaster_TotalDiscountAmount;
            });

            $total_vat = array_reduce($sales, function ($prev, $curr) {
                return $prev + $curr->SaleMaster_TaxAmount;
            });


            $other_income_expense = $this->db->query("
                select
                (
                    select ifnull(sum(ct.In_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.Tr_branchid = '" . $this->session->userdata('BRANCHid') . "'
                    and ct.status = 'a'
                    " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
                ) as income,
            
                (
                    select ifnull(sum(ct.Out_Amount), 0)
                    from tbl_cashtransaction ct
                    where ct.Tr_branchid = '" . $this->session->userdata('BRANCHid') . "'
                    and ct.status = 'a'
                    " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
                ) as expense,

                (
                    select ifnull(sum(it.amount), 0)
                    from tbl_investment_transactions it
                    where it.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                    and it.transaction_type = 'Profit'
                    and it.status = 1
                    " . ($date == null ? "" : " and it.transaction_date < '$date'") . "
                ) as profit_distribute,

                (
                    select ifnull(sum(lt.amount), 0)
                    from tbl_loan_transactions lt
                    where lt.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                    and lt.transaction_type = 'Interest'
                    and lt.status = 1
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as loan_interest,

                (
                    select ifnull(sum(a.valuation - a.as_amount), 0)
                    from tbl_assets a
                    where a.branchid = '" . $this->session->userdata('BRANCHid') . "'
                    and a.buy_or_sale = 'sale'
                    and a.status = 'a'
                    " . ($date == null ? "" : " and a.as_date < '$date'") . "
                ) as assets_sales_profit_loss,
            
                (
                    select ifnull(sum(ep.total_payment_amount), 0)
                    from tbl_employee_payment ep
                    where ep.branch_id = '" . $this->session->userdata('BRANCHid') . "'
                    and ep.status = 'a'
                    " . ($date == null ? "" : " and ep.payment_date < '$date'") . "
                ) as employee_payment,

                (
                    select ifnull(sum(pm.PurchaseMaster_DiscountAmount), 0)
                    from tbl_purchasemaster pm
                    where pm.PurchaseMaster_BranchID = '" . $this->session->userdata('BRANCHid') . "'
                    and pm.status = 'a'
                    " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
                ) as purchase_discount,

                (
                    select ifnull(sum(pm.PurchaseMaster_Tax), 0) 
                    from tbl_purchasemaster pm
                    where pm.PurchaseMaster_BranchID = '" . $this->session->userdata('BRANCHid') . "'
                    and pm.status = 'a'
                    " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
                ) as purchase_vat,

                (
                    select ifnull(sum(pm.PurchaseMaster_Freight), 0) 
                    from tbl_purchasemaster pm
                    where pm.PurchaseMaster_BranchID = '" . $this->session->userdata('BRANCHid') . "'
                    and pm.status = 'a'
                    " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
                ) as purchase_transport_cost,

                (
                    select ifnull(sum(dd.damage_amount), 0) 
                    from tbl_damagedetails dd
                    join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
                    where d.Damage_brunchid = '" . $this->session->userdata('BRANCHid') . "'
                    and dd.status = 'a'
                    " . ($date == null ? "" : " and d.Damage_Date  < '$date'") . "
                ) as damaged_amount,

                (
                    select ifnull(sum(rd.SaleReturnDetails_ReturnAmount) - sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails rd
                    join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                    join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                    join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                    where r.Status = 'a'
                    and r.SaleReturn_brunchId = '" . $this->session->userdata('BRANCHid') . "'
                    " . ($date == null ? "" : " and r.SaleReturn_ReturnDate  < '$date'") . "
                ) as returned_amount,

                (
                    select ifnull(sum(smb.bank_charge), 0)
                    from tbl_sales_bank smb
                    join tbl_salesmaster sm on sm.SaleMaster_SlNo = smb.sale_id
                    where smb.Status = 'a'
                    and sm.Status = 'a'
                    and smb.branchId = '" . $this->session->userdata('BRANCHid') . "'
                    " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
                ) as bank_charge,

                (
                    select ifnull(sum(ex.total), 0)
                    from tbl_exchange ex
                    where ex.Status = 'a'
                    and ex.branchId = '" . $this->session->userdata('BRANCHid') . "'
                    " . ($date == null ? "" : " and ex.date < '$date'") . "
                ) as exchange_amount,

                (
                    select ifnull(sum(exd.purchase_rate * exd.quantity), 0)
                    from tbl_exchange_detail exd
                    join tbl_exchange ex on ex.id = exd.exchange_id
                    where ex.Status = 'a'
                    and exd.Status = 'a'
                    and ex.branchId = '" . $this->session->userdata('BRANCHid') . "'
                    " . ($date == null ? "" : " and ex.date < '$date'") . "
                ) as exchange_out_cost,

                (
                    select ifnull(sum(sd.Purchase_Rate * sd.SaleDetails_TotalQuantity), 0)
                    from (select distinct exd.exchange_id, exd.sale_detail_id from tbl_exchange_detail exd where exd.Status = 'a') exr
                    join tbl_exchange ex on ex.id = exr.exchange_id
                    join tbl_saledetails sd on sd.SaleDetails_SlNo = exr.sale_detail_id
                    where ex.Status = 'a'
                    and ex.branchId = '" . $this->session->userdata('BRANCHid') . "'
                    " . ($date == null ? "" : " and ex.date < '$date'") . "
                ) as exchange_in_cost
            ")->row();

            $exchange_profit = $other_income_expense->exchange_amount - ($other_income_expense->exchange_out_cost - $other_income_expense->exchange_in_cost);

            $net_profit = ($profits + $exchange_profit + $total_transport_cost + $other_income_expense->income + $other_income_expense->purchase_discount + $total_vat) - ($total_discount + $other_income_expense->bank_charge + $other_income_expense->returned_amount + $other_income_expense->purchase_transport_cost + $other_income_expense->purchase_vat + $other_income_expense->damaged_amount + $other_income_expense->expense + $other_income_expense->employee_payment + $other_income_expense->profit_distribute + $other_income_expense->loan_interest + $other_income_expense->assets_sales_profit_loss);

            $statements = [
                'assets'            => $assets,
                'cash_balance'      => $cash_balance,
                'bank_accounts'     => $bank_accounts,
                'loan_accounts'     => $loan_accounts,
                'invest_accounts'   => $invest_accounts,
                'customer_dues'     => $customer_dues,
                'supplier_dues'     => $supplier_dues,
                'bad_debts'         => $bad_debts,
                'supplier_prev_due' => $supplier_prev_due,
                'customer_prev_due' => $customer_prev_due,
                'stock_value'       => $stock_value,
                'net_profit'        => $net_profit,
                'stock_valuation_adjustment' => $stock_valuation_adjustment,
            ];

            $res = ['success'   => true, 'statements'  => $statements];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    /*
     * Stock at book cost, split into the same cost figures used in profit/loss & supplier due.
     * $date is exclusive (balance sheet passes selected date + 1 day).
     */
    private function bookStockSummary($branchId, $date = null)
    {
        $dateClause = function ($field) use ($date) {
            return $date == null ? "" : " and $field < '$date'";
        };

        $summary = $this->db->query("
            select
            (
                select ifnull(sum(pm.PurchaseMaster_SubTotalAmount), 0)
                from tbl_purchasemaster pm
                where pm.PurchaseMaster_BranchID = '$branchId'
                and pm.status = 'a'
                " . $dateClause('pm.PurchaseMaster_OrderDate') . "
            ) as purchase_amount,

            (
                select ifnull(sum(sd.Purchase_Rate * sd.SaleDetails_TotalQuantity), 0)
                from tbl_saledetails sd
                join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                where sm.SaleMaster_branchid = '$branchId'
                and sm.Status = 'a'
                " . $dateClause('sm.SaleMaster_SaleDate') . "
            ) as sale_cost,

            (
                select ifnull(sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity), 0)
                from tbl_salereturndetails rd
                join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                where r.Status = 'a'
                and r.SaleReturn_brunchId = '$branchId'
                " . $dateClause('r.SaleReturn_ReturnDate') . "
            ) as sale_return_cost,

            (
                select ifnull(sum(pr.PurchaseReturn_ReturnAmount), 0)
                from tbl_purchasereturn pr
                join tbl_purchasemaster rpm on rpm.PurchaseMaster_InvoiceNo = pr.PurchaseMaster_InvoiceNo
                join tbl_supplier s on s.Supplier_SlNo = rpm.Supplier_SlNo
                where s.Supplier_brinchid = '$branchId'
                " . $dateClause('pr.PurchaseReturn_ReturnDate') . "
            ) as purchase_return_amount,

            (
                select ifnull(sum(dd.damage_amount), 0)
                from tbl_damagedetails dd
                join tbl_damage d on d.Damage_SlNo = dd.Damage_SlNo
                where d.Damage_brunchid = '$branchId'
                and dd.status = 'a'
                " . $dateClause('d.Damage_Date') . "
            ) as damage_amount,

            (
                select ifnull(sum(exd.purchase_rate * exd.quantity), 0)
                from tbl_exchange_detail exd
                join tbl_exchange ex on ex.id = exd.exchange_id
                where ex.Status = 'a'
                and exd.Status = 'a'
                and ex.branchId = '$branchId'
                " . $dateClause('ex.date') . "
            ) as exchange_out_cost,

            (
                select ifnull(sum(sd.Purchase_Rate * sd.SaleDetails_TotalQuantity), 0)
                from (select distinct exd.exchange_id, exd.sale_detail_id from tbl_exchange_detail exd where exd.Status = 'a') exr
                join tbl_exchange ex on ex.id = exr.exchange_id
                join tbl_saledetails sd on sd.SaleDetails_SlNo = exr.sale_detail_id
                where ex.Status = 'a'
                and ex.branchId = '$branchId'
                " . $dateClause('ex.date') . "
            ) as exchange_in_cost
        ")->row();

        $summary->total = $summary->purchase_amount - $summary->sale_cost + $summary->sale_return_cost
            - $summary->purchase_return_amount - $summary->damage_amount
            - $summary->exchange_out_cost + $summary->exchange_in_cost;

        return $summary;
    }

    /*
     * Per product: stock value at current avg purchase rate vs stock at book cost.
     * Quantity filters mirror the balance sheet stock query, cost filters mirror bookStockSummary().
     */
    private function stockValuationProducts($branchId, $date = null)
    {
        $dateClause = function ($field) use ($date) {
            return $date == null ? "" : " and $field < '$date'";
        };

        $products = $this->db->query("
            select
                p.Product_SlNo as product_id,
                p.Product_Code as product_code,
                p.Product_Name as product_name,
                p.status as product_status,
                p.is_service,
                p.Product_Purchase_Rate as current_rate,
                ifnull(pu.qty, 0) as purchase_qty,
                ifnull(pu.amount, 0) as purchase_amount,
                ifnull(sa.qty, 0) as sale_qty,
                ifnull(sa.cost, 0) as sale_cost,
                ifnull(sr.qty, 0) as sale_return_qty,
                ifnull(src.cost, 0) as sale_return_cost,
                ifnull(pr.qty, 0) as purchase_return_qty,
                ifnull(pr.amount, 0) as purchase_return_amount,
                ifnull(dm.qty, 0) as damage_qty,
                ifnull(dm.amount, 0) as damage_amount,
                ifnull(exo.qty, 0) as exchange_out_qty,
                ifnull(exo.cost, 0) as exchange_out_cost,
                ifnull(exi.qty, 0) as exchange_in_qty,
                ifnull(exi.cost, 0) as exchange_in_cost,
                ifnull(tf.qty, 0) as transfer_out_qty,
                ifnull(tt.qty, 0) as transfer_in_qty
            from tbl_product p
            left join (
                select pd.Product_IDNo as product_id,
                    sum(pd.PurchaseDetails_TotalQuantity) as qty,
                    sum(if(pm.status = 'a', pd.PurchaseDetails_TotalAmount, 0)) as amount
                from tbl_purchasedetails pd
                join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                where pd.PurchaseDetails_branchID = '$branchId'
                and pd.Status = 'a'
                " . $dateClause('pm.PurchaseMaster_OrderDate') . "
                group by pd.Product_IDNo
            ) pu on pu.product_id = p.Product_SlNo
            left join (
                select sd.Product_IDNo as product_id,
                    sum(sd.SaleDetails_TotalQuantity) as qty,
                    sum(if(sm.Status = 'a', sd.Purchase_Rate * sd.SaleDetails_TotalQuantity, 0)) as cost
                from tbl_saledetails sd
                join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                where sd.SaleDetails_BranchId = '$branchId'
                and sd.Status = 'a'
                " . $dateClause('sm.SaleMaster_SaleDate') . "
                group by sd.Product_IDNo
            ) sa on sa.product_id = p.Product_SlNo
            left join (
                select srd.SaleReturnDetailsProduct_SlNo as product_id,
                    sum(srd.SaleReturnDetails_ReturnQuantity) as qty
                from tbl_salereturndetails srd
                join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                where srd.SaleReturnDetails_brunchID = '$branchId'
                and sr.Status = 'a'
                " . $dateClause('sr.SaleReturn_ReturnDate') . "
                group by srd.SaleReturnDetailsProduct_SlNo
            ) sr on sr.product_id = p.Product_SlNo
            left join (
                select rd.SaleReturnDetailsProduct_SlNo as product_id,
                    sum(sd.Purchase_Rate * rd.SaleReturnDetails_ReturnQuantity) as cost
                from tbl_salereturndetails rd
                join tbl_salereturn r on r.SaleReturn_SlNo = rd.SaleReturn_IdNo
                join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = r.SaleMaster_InvoiceNo
                join tbl_saledetails sd on sd.Product_IDNo = rd.SaleReturnDetailsProduct_SlNo and sd.SaleMaster_IDNo = sm.SaleMaster_SlNo
                where r.Status = 'a'
                and r.SaleReturn_brunchId = '$branchId'
                " . $dateClause('r.SaleReturn_ReturnDate') . "
                group by rd.SaleReturnDetailsProduct_SlNo
            ) src on src.product_id = p.Product_SlNo
            left join (
                select prd.PurchaseReturnDetailsProduct_SlNo as product_id,
                    sum(prd.PurchaseReturnDetails_ReturnQuantity) as qty,
                    sum(prd.PurchaseReturnDetails_ReturnAmount) as amount
                from tbl_purchasereturndetails prd
                join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                where prd.PurchaseReturnDetails_brachid = '$branchId'
                " . $dateClause('pr.PurchaseReturn_ReturnDate') . "
                group by prd.PurchaseReturnDetailsProduct_SlNo
            ) pr on pr.product_id = p.Product_SlNo
            left join (
                select dmd.Product_SlNo as product_id,
                    sum(dmd.DamageDetails_DamageQuantity) as qty,
                    sum(dmd.damage_amount) as amount
                from tbl_damagedetails dmd
                join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                where dmd.status = 'a'
                and dm.Damage_brunchid = '$branchId'
                " . $dateClause('dm.Damage_Date') . "
                group by dmd.Product_SlNo
            ) dm on dm.product_id = p.Product_SlNo
            left join (
                select exd.product_id,
                    sum(exd.quantity) as qty,
                    sum(exd.purchase_rate * exd.quantity) as cost
                from tbl_exchange_detail exd
                join tbl_exchange ex on ex.id = exd.exchange_id
                where ex.branchId = '$branchId'
                and ex.Status = 'a'
                and exd.Status = 'a'
                " . $dateClause('ex.date') . "
                group by exd.product_id
            ) exo on exo.product_id = p.Product_SlNo
            left join (
                select sd.Product_IDNo as product_id,
                    sum(sd.SaleDetails_TotalQuantity) as qty,
                    sum(sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as cost
                from (select distinct exd.exchange_id, exd.sale_detail_id from tbl_exchange_detail exd where exd.Status = 'a') exr
                join tbl_exchange ex on ex.id = exr.exchange_id
                join tbl_saledetails sd on sd.SaleDetails_SlNo = exr.sale_detail_id
                where ex.branchId = '$branchId'
                and ex.Status = 'a'
                " . $dateClause('ex.date') . "
                group by sd.Product_IDNo
            ) exi on exi.product_id = p.Product_SlNo
            left join (
                select trd.product_id, sum(trd.quantity) as qty
                from tbl_transferdetails trd
                join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                where tm.transfer_from = '$branchId'
                " . $dateClause('tm.transfer_date') . "
                group by trd.product_id
            ) tf on tf.product_id = p.Product_SlNo
            left join (
                select trd.product_id, sum(trd.quantity) as qty
                from tbl_transferdetails trd
                join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                where tm.transfer_to = '$branchId'
                " . $dateClause('tm.transfer_date') . "
                group by trd.product_id
            ) tt on tt.product_id = p.Product_SlNo
        ")->result();

        $rows = [];
        $totalStockValue = 0;
        $totalBookValue = 0;
        foreach ($products as $product) {
            $product->current_qty = ($product->purchase_qty + $product->sale_return_qty + $product->transfer_in_qty + $product->exchange_in_qty)
                - ($product->sale_qty + $product->purchase_return_qty + $product->damage_qty + $product->transfer_out_qty + $product->exchange_out_qty);

            $counted = $product->product_status == 'a' && $product->is_service == 'false';
            $product->stock_value = $counted ? $product->current_rate * $product->current_qty : 0;

            $product->book_value = $product->purchase_amount - $product->sale_cost + $product->sale_return_cost
                - $product->purchase_return_amount - $product->damage_amount
                - $product->exchange_out_cost + $product->exchange_in_cost;

            $product->difference = $product->stock_value - $product->book_value;

            $totalStockValue += $product->stock_value;
            $totalBookValue += $product->book_value;

            if (abs($product->difference) < 0.005) {
                continue;
            }

            if (!$counted) {
                $product->reason = 'Inactive product: stock not counted in stock value, but its cost is still in books';
            } else if (abs($product->current_qty) < 0.0001) {
                $product->reason = 'No stock left, but cost of sales did not use up the purchase cost exactly';
            } else if ($product->current_qty < 0) {
                $product->reason = 'Negative stock: sold more than purchased';
            } else {
                $product->reason = 'Cost recorded on sales differs from the running average purchase rate';
            }

            $rows[] = $product;
        }

        usort($rows, function ($a, $b) {
            return abs($b->difference) <=> abs($a->difference);
        });

        return (object)[
            'rows'        => $rows,
            'stock_value' => $totalStockValue,
            'book_value'  => $totalBookValue,
        ];
    }

    public function getStockValuationDetails()
    {
        $res = ['success' => false, 'message' => 'Invalid'];

        try {
            $branchId = $this->brunch;
            $data = json_decode($this->input->raw_input_stream);
            $date = null;

            if (isset($data->date) && $data->date != '') {
                $date = (new DateTime($data->date))->modify('+1 day')->format('Y-m-d');
            }

            $summary  = $this->bookStockSummary($branchId, $date);
            $products = $this->stockValuationProducts($branchId, $date);

            $res = [
                'success'      => true,
                'stock_value'  => $products->stock_value,
                'book_summary' => $summary,
                // invoice header totals (used in books) vs sum of product lines
                'header_detail_difference' => $products->book_value - $summary->total,
                'adjustment'   => $products->stock_value - $summary->total,
                'products'     => $products->rows,
            ];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getStockValuationLedger()
    {
        $res = ['success' => false, 'message' => 'Invalid'];

        try {
            $branchId  = $this->brunch;
            $data      = json_decode($this->input->raw_input_stream);
            $productId = intval($data->productId ?? 0);
            $date      = null;

            if (isset($data->date) && $data->date != '') {
                $date = (new DateTime($data->date))->modify('+1 day')->format('Y-m-d');
            }

            $dateClause = function ($field) use ($date) {
                return $date == null ? "" : " and $field < '$date'";
            };

            $product = $this->db->query("
                select Product_SlNo as product_id, Product_Code as product_code, Product_Name as product_name,
                    Product_Purchase_Rate as current_rate, status as product_status, is_service
                from tbl_product where Product_SlNo = ?
            ", $productId)->row();

            if (!$product) {
                throw new Exception('Product not found');
            }

            // qty_in / qty_out move stock, amount_in / amount_out move book cost
            $entries = $this->db->query("
                select * from (
                    select 1 as sort, pm.PurchaseMaster_OrderDate as date, pm.AddTime as add_time, 'Purchase' as type,
                        pm.PurchaseMaster_InvoiceNo as invoice, ifnull(s.Supplier_Name, pm.supplierName) as party,
                        pd.PurchaseDetails_TotalQuantity as qty_in, 0 as qty_out, pd.PurchaseDetails_Rate as rate,
                        if(pm.status = 'a', pd.PurchaseDetails_TotalAmount, 0) as amount_in, 0 as amount_out, 0 as uses_recorded_cost
                    from tbl_purchasedetails pd
                    join tbl_purchasemaster pm on pm.PurchaseMaster_SlNo = pd.PurchaseMaster_IDNo
                    left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
                    where pd.Product_IDNo = $productId
                    and pd.PurchaseDetails_branchID = '$branchId'
                    and pd.Status = 'a'
                    " . $dateClause('pm.PurchaseMaster_OrderDate') . "

                    union all
                    select 2, pr.PurchaseReturn_ReturnDate, pr.AddTime, 'Purchase Return',
                        pr.PurchaseMaster_InvoiceNo, s.Supplier_Name,
                        0, prd.PurchaseReturnDetails_ReturnQuantity,
                        prd.PurchaseReturnDetails_ReturnAmount / nullif(prd.PurchaseReturnDetails_ReturnQuantity, 0),
                        0, prd.PurchaseReturnDetails_ReturnAmount, 0
                    from tbl_purchasereturndetails prd
                    join tbl_purchasereturn pr on pr.PurchaseReturn_SlNo = prd.PurchaseReturn_SlNo
                    left join tbl_supplier s on s.Supplier_SlNo = pr.Supplier_IDdNo
                    where prd.PurchaseReturnDetailsProduct_SlNo = $productId
                    and prd.PurchaseReturnDetails_brachid = '$branchId'
                    " . $dateClause('pr.PurchaseReturn_ReturnDate') . "

                    union all
                    select 3, sm.SaleMaster_SaleDate, sm.AddTime, 'Sale',
                        sm.SaleMaster_InvoiceNo, ifnull(c.Customer_Name, ifnull(sm.customerName, 'General Customer')),
                        0, sd.SaleDetails_TotalQuantity, sd.Purchase_Rate,
                        0, if(sm.Status = 'a', sd.Purchase_Rate * sd.SaleDetails_TotalQuantity, 0), 1
                    from tbl_saledetails sd
                    join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
                    left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                    where sd.Product_IDNo = $productId
                    and sd.SaleDetails_BranchId = '$branchId'
                    and sd.Status = 'a'
                    " . $dateClause('sm.SaleMaster_SaleDate') . "

                    union all
                    select 4, sr.SaleReturn_ReturnDate, sr.AddTime, 'Sale Return',
                        sr.SaleMaster_InvoiceNo, ifnull(c.Customer_Name, ifnull(sm.customerName, 'General Customer')),
                        srd.SaleReturnDetails_ReturnQuantity, 0,
                        (select sd.Purchase_Rate from tbl_saledetails sd where sd.SaleMaster_IDNo = sm.SaleMaster_SlNo and sd.Product_IDNo = $productId limit 1),
                        ifnull((select sum(sd.Purchase_Rate) from tbl_saledetails sd where sd.SaleMaster_IDNo = sm.SaleMaster_SlNo and sd.Product_IDNo = $productId), 0) * srd.SaleReturnDetails_ReturnQuantity,
                        0, 0
                    from tbl_salereturndetails srd
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    left join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
                    left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                    where srd.SaleReturnDetailsProduct_SlNo = $productId
                    and srd.SaleReturnDetails_brunchID = '$branchId'
                    and sr.Status = 'a'
                    " . $dateClause('sr.SaleReturn_ReturnDate') . "

                    union all
                    select 5, dm.Damage_Date, dm.AddTime, 'Damage',
                        dm.Damage_InvoiceNo, '',
                        0, dmd.DamageDetails_DamageQuantity, dmd.damage_rate,
                        0, dmd.damage_amount, 0
                    from tbl_damagedetails dmd
                    join tbl_damage dm on dm.Damage_SlNo = dmd.Damage_SlNo
                    where dmd.Product_SlNo = $productId
                    and dmd.status = 'a'
                    and dm.Damage_brunchid = '$branchId'
                    " . $dateClause('dm.Damage_Date') . "

                    union all
                    select 6, ex.date, ex.AddTime, 'Exchange Out',
                        ex.invoice, ifnull(c.Customer_Name, ifnull(sm.customerName, 'General Customer')),
                        0, exd.quantity, exd.purchase_rate,
                        0, exd.purchase_rate * exd.quantity, 1
                    from tbl_exchange_detail exd
                    join tbl_exchange ex on ex.id = exd.exchange_id
                    left join tbl_salesmaster sm on sm.SaleMaster_SlNo = ex.sale_id
                    left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                    where exd.product_id = $productId
                    and ex.branchId = '$branchId'
                    and ex.Status = 'a'
                    and exd.Status = 'a'
                    " . $dateClause('ex.date') . "

                    union all
                    select 7, ex.date, ex.AddTime, 'Exchange In',
                        ex.invoice, ifnull(c.Customer_Name, ifnull(sm.customerName, 'General Customer')),
                        sd.SaleDetails_TotalQuantity, 0, sd.Purchase_Rate,
                        sd.Purchase_Rate * sd.SaleDetails_TotalQuantity, 0, 0
                    from (select distinct exd.exchange_id, exd.sale_detail_id from tbl_exchange_detail exd where exd.Status = 'a') exr
                    join tbl_exchange ex on ex.id = exr.exchange_id
                    join tbl_saledetails sd on sd.SaleDetails_SlNo = exr.sale_detail_id
                    left join tbl_salesmaster sm on sm.SaleMaster_SlNo = ex.sale_id
                    left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                    where sd.Product_IDNo = $productId
                    and ex.branchId = '$branchId'
                    and ex.Status = 'a'
                    " . $dateClause('ex.date') . "

                    union all
                    select 8, tm.transfer_date, null, if(tm.transfer_from = '$branchId', 'Transfer Out', 'Transfer In'),
                        tm.transfer_id, '',
                        if(tm.transfer_to = '$branchId', trd.quantity, 0), if(tm.transfer_from = '$branchId', trd.quantity, 0), 0,
                        0, 0, 0
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = $productId
                    and (tm.transfer_from = '$branchId' or tm.transfer_to = '$branchId')
                    " . $dateClause('tm.transfer_date') . "
                ) ledger
                order by date, add_time, sort
            ")->result();

            // running stock & book cost; for sales/exchanges compare recorded cost with running average
            $qty = 0;
            $book = 0;
            $mismatchEffect = 0;
            foreach ($entries as $entry) {
                $averageBefore = $qty > 0.0001 ? $book / $qty : null;

                $entry->average_before = $averageBefore;
                $entry->effect = null;
                if ($entry->uses_recorded_cost == 1 && $averageBefore !== null && $entry->amount_out != 0) {
                    // positive = recorded cost higher than average, book cost drops more than it should
                    $entry->effect = ($entry->rate - $averageBefore) * $entry->qty_out;
                    $mismatchEffect += $entry->effect;
                }

                $qty += $entry->qty_in - $entry->qty_out;
                $book += $entry->amount_in - $entry->amount_out;

                $entry->balance_qty = $qty;
                $entry->balance_book = $book;
            }

            $counted = $product->product_status == 'a' && $product->is_service == 'false';
            $stockValue = $counted ? $product->current_rate * $qty : 0;
            $difference = $stockValue - $book;

            $res = [
                'success'  => true,
                'product'  => $product,
                'entries'  => $entries,
                'closing'  => [
                    'qty'             => $qty,
                    'book_value'      => $book,
                    'average_rate'    => $qty > 0.0001 ? $book / $qty : null,
                    'stock_value'     => $stockValue,
                    'counted'         => $counted,
                    'difference'      => $difference,
                    'mismatch_effect' => $mismatchEffect,
                    'other_effect'    => $difference - $mismatchEffect,
                ],
            ];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}
