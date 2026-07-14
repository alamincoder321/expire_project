<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
         if($access == '' ){
            redirect("Login");
        }
        $this->load->model('Billing_model'); 
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
		$vars['branch_info'] = $this->Billing_model->company_branch_profile($this->brunch);
		$this->load->vars($vars);
    }
    public function index(){
        $data['title'] = "Product Sales";
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function supplierList(){
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Supplier List";
        $data['content'] = $this->load->view('Administrator/reports/supplierList', $data, true);
        $this->load->view("Administrator/index", $data);
    }

    public function price_listprint()  {
        $data['title'] = "Customer List";
		$select_one = $this->session->userdata('select_one');
		$category = $category = $this->session->userdata('category');
		$product = $product = $this->session->userdata('product');

		
		if($select_one =='All'){
			$data['products'] = $this->Product_model->get_all_product_price_list();
		}elseif($select_one == 'Category'){
			$data['category'] = $this->Other_model->get_single_category_info($category);
		}elseif($select_one == 'Product'){
			$data['products'] = $this->Product_model->get_singel_product_pice_list($product);
		}
        $this->load->view('Administrator/reports/price_listprint', $data);
    }
    public function employeelist()  {
        $data['title'] = "Employee List";
        $this->load->view('Administrator/reports/employeelist', $data);
    }
    public function price_list()  {
        $access = $this->mt->userAccess();
        if(!$access){
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
	
    public function campaignlist()
	{
		$data['title']  = 'Campaign Product List';
        $data['content'] = $this->load->view('Administrator/reports/campaignList', $data, true);
        $this->load->view('Administrator/index', $data);
	}
	
	public function cashStatment() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $datas['title'] = "Cash Statement"; 
        $data['content'] = $this->load->view('Administrator/reports/cashStatement', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }
	
    public function specialReport() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $datas['title'] = "Cash Statement"; 
        $data['content'] = $this->load->view('Administrator/reports/special', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function special_report_all_data()
    {
        $data = json_decode($this->input->raw_input_stream);
        $brunch = $this->session->userdata('BRANCHid');
        
        $dateFrom = $data->fromDate;
        $dateTo   = $data->toDate;

        $query = "
            select
            u.User_SlNo,
            u.FullName,
            u.FullName as AddBy,
            
            (select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0) from tbl_salesmaster sm
            where sm.Status = 'a' and sm.AddBy = u.FullName
            and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo') as totalsales,
            
            (select ifnull(sum(sm.SaleMaster_cashPaid), 0) from tbl_salesmaster sm
            where sm.Status = 'a' and sm.AddBy = u.FullName
            and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo') as cashamount,
            
            (select ifnull(sum(sm.SaleMaster_bankPaid), 0) from tbl_salesmaster sm
            where sm.Status = 'a' and sm.AddBy = u.FullName
            and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo') as bankamount,
            
            (select ifnull(sum(sm.returnAmount), 0) from tbl_salesmaster sm
            where sm.Status = 'a' and sm.AddBy = u.FullName
            and sm.SaleMaster_SaleDate between '$dateFrom' and '$dateTo') as changeamount,
            
            (select ifnull(sum(sr.SaleReturn_ReturnAmount), 0) from tbl_salereturn sr
            where sr.Status = 'a' and sr.AddBy = u.FullName 
            and sr.SaleReturn_ReturnDate between '$dateFrom' and '$dateTo') as returnamount,
            
            (select ifnull(sum(ex.total), 0) from tbl_exchange ex
            where ex.Status = 'a' and ex.AddBy = u.FullName 
            and ex.date between '$dateFrom' and '$dateTo') as exchangeamount
            
            from tbl_user u
            where u.status = 'a'
            and u.Brunch_ID = '$brunch'
            ".(!empty($data->userFullName) ? " and u.FullName = '$data->userFullName'" : "")."
            ";

        $totals = $this->db->query($query)->result();

        echo json_encode($totals);

    }


    public function balanceSheet() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $datas['title'] = "Balance In Out";
        $data['content'] = $this->load->view('Administrator/reports/balanceSheet', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }
		
    public function branch_stock_print(){
		$data['Branch_ID'] = $BranchID = $this->session->userdata('Branch_ID');
        $data['Branch_category'] = $category = $this->session->userdata('Branch_category');
        $this->session->set_userdata($data);
		if($category != 'All'){
			$this->db->SELECT("tbl_product.*, tbl_productcategory.*,tbl_unit.*,tbl_color.*,tbl_brand.* FROM tbl_product left join tbl_productcategory on tbl_productcategory.ProductCategory_SlNo= tbl_product.ProductCategory_ID left join tbl_unit on tbl_unit.Unit_SlNo=tbl_product.Unit_ID  LEFT JOIN tbl_color ON tbl_color.color_SiNo=tbl_product.color LEFT JOIN tbl_brand ON tbl_brand.brand_SiNo=tbl_product.brand LEFT JOIN tbl_country ON tbl_country.Country_SlNo=tbl_product.country where tbl_product.ProductCategory_ID = '$category' AND tbl_product.Product_branchid = '$BranchID'");
			$query = $this->db->get();
			$result = $query->result();
			$data['product'] = $result;
			$data['show'] = 1;
		}else{
			$this->db->SELECT('*');
			$this->db->from('tbl_productcategory');
			$this->db->where('category_branchid',$BranchID);
			$query = $this->db->get();
			$category = $query->result();
			
			foreach($category as $vcategory)
			{
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
	
	public function selectOne($value){
		if($value == 'Category')
		{
			$category = $this->Billing_model->select_category_by_branch($this->brunch);
			?>
			<select id="category"  data-placeholder="Choose a Category ....." class="chosen-select" style="width:200px">
				<option value=""></option>		
			<?php
			foreach($category as $vcategory)
			{
			?>
				<option value="<?php echo $vcategory->ProductCategory_SlNo; ?>"><?php echo $vcategory->ProductCategory_Name; ?></option>
			<?php
			}
			?>
			</select>
			<?php
		}else{
			$products = $this->Product_model->products_by_brunch();
			//echo "<pre>";print_r($product);exit;
			?>
			<select id="product"  data-placeholder="Choose a Product ....." class="chosen-select" style="width:200px">
				<option value=""></option>		
			<?php
			foreach($products as $product)
			{
			?>
				<option value="<?php echo $product->Product_SlNo; ?>"><?php echo $product->Product_Name; ?>-<?php echo $product->Product_Code; ?></option>
			<?php
			}
			?>
			</select>
			<?php
		}
	}
	
	public function price_list_report(){
		$session['select_one'] = $select_one = $this->input->post('select_one');
		$session['category'] = $category = $this->input->post('category');
		$session['product'] = $product = $this->input->post('product');
		$this->session->set_userdata($session);

		if($select_one =='All'){
			$data['products'] = $this->Product_model->get_all_product_price_list();
		}elseif($select_one == 'Category'){
			$data['category'] = $this->Other_model->get_single_category_info($category);
		}elseif($select_one == 'Product'){
			$data['products'] = $this->Product_model->get_singel_product_pice_list($product);
        }
        
		$this->load->view('Administrator/reports/price_list_search', $data);
	}
	
	public function profitLossPrint(){
		$data['searchtype'] = $this->session->userdata('searchtype');
		$data['ProductID'] = $this->session->userdata('ProductID');
		$data['startdate'] = $this->session->userdata('startdate');
		$data['enddate'] = $this->session->userdata('enddate');
		$this->load->view('Administrator/reports/profit_loss',$data);
    }
    
    public function reOrderList(){
        $data['title'] = "Re-Order List";
        $data['content'] = $this->load->view('Administrator/reports/reorder_list', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function dayBook(){
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $data['title'] = "Daily Book";
        $data['content'] = $this->load->view('Administrator/reports/day_book', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function balance_sheet() {
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
        $datas['title'] = "Balance Sheet";
        $data['content'] = $this->load->view('Administrator/reports/balance_sheet', $datas, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getBalanceSheet()
    {
        $res = [ 'success'   => false, 'message'  => 'Invalid' ];

        try {
            $branchId = $this->brunch;
            $data       = json_decode($this->input->raw_input_stream);
            $date       = null;

            if(isset($data->date) && $data->date != ''){
                $date = new DateTime($data->date);
                $date = $date->modify('+1 day')->format('Y-m-d');
            }
            
            $cash_balance = $this->mt->getTransactionSummary($date)->cash_balance;
            $bank_accounts = $this->mt->getBankTransactionSummary(null, $date);
            $loan_accounts = $this->mt->getLoanTransactionSummary(null, $date);
            $invest_accounts = $this->mt->getInvestmentTransactionSummary(null, $date);

            //assets
            $assets = $this->mt->assetsReport('', $date);

            $assets = array_reduce($assets, function($prev, $curr){ return $prev + $curr->approx_amount;});

            //customer prev due adjust
            $customer_prev_due = $this->db->query("
                SELECT ifnull(sum(previous_due), 0) as amount
                from tbl_customer
                where Customer_brunchid = '$this->brunch'
            ")->row()->amount;

            //customer dues
            $customer_dues = $this->mt->customerDue(" and c.status = 'a'", $date);
            $bad_debts = $this->mt->customerDue(" and c.status = 'd'", $date);

            $customer_dues = array_reduce($customer_dues, function($prev, $curr){ return $prev + $curr->dueAmount;});

            $bad_debts = array_reduce($bad_debts, function($prev, $curr){ return $prev + $curr->dueAmount;});

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
                            
                    (select (purchased_quantity + sales_returned_quantity + transferred_to_quantity) - (sold_quantity + purchase_returned_quantity + damaged_quantity + transferred_from_quantity)) as current_quantity,
                    (select p.Product_Purchase_Rate * current_quantity) as stock_value
                from tbl_product p
                where p.status = 'a' 
                and p.is_service = 'false'
            ")->result();

            $stock_value = array_sum(
                array_map(function($product){
                    return $product->stock_value;
                }, $stocks));

            //supplier prev due adjust
            $supplier_prev_due = $this->db->query("
                SELECT ifnull(sum(previous_due), 0) as amount
                from tbl_supplier
                where Supplier_brinchid = '$this->brunch'
            ")->row()->amount;

            //supplier due
            $supplier_dues = $this->mt->supplierDue("", $date);

            $supplier_dues = array_reduce($supplier_dues, function($prev, $curr){ return $prev + $curr->due;});

            //profit loss
            $sales = $this->db->query("
                select 
                    sm.*
                from tbl_salesmaster sm
                where sm.SaleMaster_branchid = ? 
                and sm.Status = 'a'
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
            ", $this->session->userdata('BRANCHid'))->result();

            foreach($sales as $sale){
                $sale->saleDetails = $this->db->query("
                    select
                        sd.*,
                        (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                        (select sd.SaleDetails_TotalAmount - purchased_amount) as profit_loss
                    from tbl_saledetails sd
                    where sd.SaleMaster_IDNo = ?
                ", $sale->SaleMaster_SlNo)->result();
            }

            $profits = array_reduce($sales, function($prev, $curr){ 
                return $prev + array_reduce($curr->saleDetails, function($p, $c){
                    return $p + $c->profit_loss;
                });
            });

            $total_transport_cost = array_reduce($sales, function($prev, $curr){ 
                return $prev + $curr->SaleMaster_Freight;
            });
            
            $total_discount = array_reduce($sales, function($prev, $curr){ 
                return $prev + $curr->SaleMaster_TotalDiscountAmount;
            });

            $total_vat = array_reduce($sales, function($prev, $curr){ 
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
                ) as returned_amount
            ")->row();

            $net_profit = ($profits + $total_transport_cost + $other_income_expense->income + $total_vat) - ($total_discount + $other_income_expense->returned_amount + $other_income_expense->damaged_amount + $other_income_expense->expense + $other_income_expense->employee_payment + $other_income_expense->profit_distribute + $other_income_expense->loan_interest + $other_income_expense->assets_sales_profit_loss );

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
            ];

            $res = [ 'success'   => true, 'statements'  => $statements ];

        } catch (Exception $ex){
            $res = ['success'=>false, 'message'=>$ex->getMessage()];
        }

        echo json_encode($res);
    }
}
