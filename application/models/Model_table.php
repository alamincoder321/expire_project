<?php
class Model_Table extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function userAccess()
    {
        $currentUrl = $this->uri->uri_string();

        $userAccessQuery = $this->db->where('user_id', $this->session->userdata('userId'))->get('tbl_user_access');
        $access = [];
        if ($userAccessQuery->num_rows() != 0) {
            $userAccess = $userAccessQuery->row();
            $access = json_decode($userAccess->access);
        }

        $accountType = $this->session->userdata('accountType');
        if (array_search($currentUrl, $access) > -1 || $accountType == 'm' || $accountType == 'a') {
            return true;
        } else {
            return false;
        }
    }

    /*---------------------------  Save Update Data  ------------------------------*/

    public function generateSalesInvoice()
    {
        $branchId = $this->session->userdata('BRANCHid');
        $branchNo = strlen($branchId) < 10 ? '0' . $branchId : $branchId;
        $invoice = date('y') . $branchNo . "00001";
        $year = date('y');
        $sales = $this->db->query("select * from tbl_salesmaster sm where sm.SaleMaster_InvoiceNo like '$year%' and SaleMaster_branchid = ?", $branchId);
        if ($sales->num_rows() != 0) {
            $newSalesId = $sales->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000');
            $invoice = date('y') . $branchNo . (strlen($newSalesId) > count($zeros) ? $newSalesId : $zeros[count($zeros) - strlen($newSalesId)] . $newSalesId);
        }

        return $invoice;
    }


    public function generateQuotationInvoice()
    {
        $invoice = 'Q-' . date('Y') . "00001";
        $year = date('Y');
        $quotations = $this->db->query("select * from tbl_quotation_master qm where qm.SaleMaster_InvoiceNo like 'Q-$year%'");
        if ($quotations->num_rows() != 0) {
            $newQuotationId = $quotations->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000');
            $invoice = 'Q-' . date('Y') . (strlen($newQuotationId) > count($zeros) ? $newQuotationId : $zeros[count($zeros) - strlen($newQuotationId)] . $newQuotationId);
        }

        return $invoice;
    }

    public function generatePurchaseOrderInvoice()
    {
        $invoice = date('Y') . "000001";
        $year = date('Y');
        $purchases = $this->db->query("select * from tbl_purchase_order pm where pm.PurchaseMaster_InvoiceNo like '$year%'");
        if ($purchases->num_rows() != 0) {
            $newPurchaseId = $purchases->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000', '00000');
            $invoice = date('Y') . (strlen($newPurchaseId) > count($zeros) ? $newPurchaseId : $zeros[count($zeros) - strlen($newPurchaseId)] . $newPurchaseId);
        }

        return $invoice;
    }

    public function generatePurchaseInvoice()
    {
        $invoice = date('Y') . "000001";
        $year = date('Y');
        $purchases = $this->db->query("select * from tbl_purchasemaster pm where pm.PurchaseMaster_InvoiceNo like '$year%'");
        if ($purchases->num_rows() != 0) {
            $newPurchaseId = $purchases->num_rows() + 1;
            $zeros = array('0', '00', '000', '0000', '00000');
            $invoice = date('Y') . (strlen($newPurchaseId) > count($zeros) ? $newPurchaseId : $zeros[count($zeros) - strlen($newPurchaseId)] . $newPurchaseId);
        }

        return $invoice;
    }

    public function generateUserCode()
    {
        $userCode = "U0001";

        $lastUserId = $this->db->query("select * from tbl_user order by User_SlNo desc limit 1");
        if ($lastUserId->num_rows() != 0) {
            $newUserId = $lastUserId->row()->User_SlNo + 1;
            $zeros = array('0', '00', '000');
            $userCode = 'U' . (strlen($newUserId) > count($zeros) ? $newUserId : $zeros[count($zeros) - strlen($newUserId)] . $newUserId);
        }

        return $userCode;
    }

    public function generateCustomerCode()
    {
        $customerCode = "C00001";

        $lastCustomer = $this->db->query("select * from tbl_customer order by Customer_SlNo desc limit 1");
        if ($lastCustomer->num_rows() != 0) {
            $newCustomerId = $lastCustomer->row()->Customer_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $customerCode = 'C' . (strlen($newCustomerId) > count($zeros) ? $newCustomerId : $zeros[count($zeros) - strlen($newCustomerId)] . $newCustomerId);
        }

        return $customerCode;
    }

    public function generateProductCode()
    {
        $productCode = "P00001";

        $lastProduct = $this->db->query("select * from tbl_product order by Product_SlNo desc limit 1");
        if ($lastProduct->num_rows() != 0) {
            $newProductId = $lastProduct->row()->Product_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $productCode = 'P' . (strlen($newProductId) > count($zeros) ? $newProductId : $zeros[count($zeros) - strlen($newProductId)] . $newProductId);
        }

        return $productCode;
    }

    public function generateSupplierCode()
    {
        $supplierCode = "S00001";

        $lastSupplier = $this->db->query("select * from tbl_supplier order by Supplier_SlNo desc limit 1");
        if ($lastSupplier->num_rows() != 0) {
            $newSupplierId = $lastSupplier->row()->Supplier_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $supplierCode = 'S' . (strlen($newSupplierId) > count($zeros) ? $newSupplierId : $zeros[count($zeros) - strlen($newSupplierId)] . $newSupplierId);
        }

        return $supplierCode;
    }

    public function generateCustomerPaymentCode()
    {
        $paymentCode = "TR00001";

        $lastPayment = $this->db->query("select * from tbl_customer_payment order by CPayment_id desc limit 1");
        if ($lastPayment->num_rows() != 0) {
            $newPaymentId = $lastPayment->row()->CPayment_id + 1;
            $zeros = array('0', '00', '000', '0000');
            $paymentCode = 'TR' . (strlen($newPaymentId) > count($zeros) ? $newPaymentId : $zeros[count($zeros) - strlen($newPaymentId)] . $newPaymentId);
        }

        return $paymentCode;
    }

    public function generateSupplierPaymentCode()
    {
        $paymentCode = "TR00001";

        $lastPayment = $this->db->query("select * from tbl_supplier_payment order by SPayment_id desc limit 1");
        if ($lastPayment->num_rows() != 0) {
            $newPaymentId = $lastPayment->row()->SPayment_id + 1;
            $zeros = array('0', '00', '000', '0000');
            $paymentCode = 'TR' . (strlen($newPaymentId) > count($zeros) ? $newPaymentId : $zeros[count($zeros) - strlen($newPaymentId)] . $newPaymentId);
        }

        return $paymentCode;
    }

    public function generateCashTransactionCode()
    {
        $transactionCode = "TR00001";

        $lastTransaction = $this->db->query("select * from tbl_cashtransaction order by Tr_SlNo desc limit 1");
        if ($lastTransaction->num_rows() != 0) {
            $newTransactionId = $lastTransaction->row()->Tr_SlNo + 1;
            $zeros = array('0', '00', '000', '0000');
            $transactionCode = 'TR' . (strlen($newTransactionId) > count($zeros) ? $newTransactionId : $zeros[count($zeros) - strlen($newTransactionId)] . $newTransactionId);
        }

        return $transactionCode;
    }

    public function generateDamageCode()
    {
        $code = "D0001";

        $lastDamage = $this->db->query("select * from tbl_damage order by Damage_SlNo desc limit 1");
        if ($lastDamage->num_rows() != 0) {
            $newDamageCode = $lastDamage->row()->Damage_SlNo + 1;
            $zeros = array('0', '00', '000');
            $code = 'D' . (strlen($newDamageCode) > count($zeros) ? $newDamageCode : $zeros[count($zeros) - strlen($newDamageCode)] . $newDamageCode);
        }

        return $code;
    }

    public function generateAccountCode()
    {
        $code = "A0001";

        $lastRow = $this->db->query("select * from tbl_account order by Acc_SlNo desc limit 1");
        if ($lastRow->num_rows() != 0) {
            $newCode = $lastRow->row()->Acc_SlNo + 1;
            $zeros = array('0', '00', '000');
            $code = 'A' . (strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode);
        }

        return $code;
    }

    public function getTransactionSummary($date = null)
    {
        $transactionSummary = $this->db->query("
            select
            /* Received */
            (
                select ifnull(sum(sm.SaleMaster_PaidAmount - sm.returnAmount), 0) from tbl_salesmaster sm
                where sm.SaleMaster_branchid= " . $this->session->userdata('BRANCHid') . "
                and sm.Status = 'a'
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
            ) as received_sales,
            (
                select ifnull(sum(ex.cashPaid), 0) from tbl_exchange ex
                where ex.Status = 'a'
                and ex.branchId = " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and ex.date < '$date'") . "
            ) as exchange_sales,
            (
                select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                where cp.CPayment_TransactionType = 'CR'
                and cp.CPayment_status = 'a'
                and cp.CPayment_Paymentby != 'bank'
                and cp.CPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
            ) as received_customer,
            (
                select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                where sp.SPayment_TransactionType = 'CR'
                and sp.SPayment_status = 'a'
                and sp.SPayment_Paymentby != 'bank'
                and sp.SPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
            ) as received_supplier,
            (
                select ifnull(sum(ct.In_Amount), 0) from tbl_cashtransaction ct
                where ct.Tr_Type = 'In Cash'
                and ct.status = 'a'
                and ct.Tr_branchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
            ) as received_cash,
            (
                select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                where bt.transaction_type = 'withdraw'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as bank_withdraw,
            (
                select ifnull(sum(bt.amount), 0) from tbl_loan_transactions bt
                where bt.transaction_type = 'Receive'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as loan_received,
            (
                select ifnull(sum(la.initial_balance), 0) from tbl_loan_accounts la
                where la.status = 1
                and la.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and la.save_date < '$date'") . "
            ) as loan_initial_balance,
            (
                select ifnull(sum(bt.amount), 0) from tbl_investment_transactions bt
                where bt.transaction_type = 'Receive'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as invest_received,
            (
                select ifnull(sum(ass.as_amount), 0) from tbl_assets ass
                where ass.branchid = " . $this->session->userdata('BRANCHid') . "
                and ass.status = 'a'
                and ass.buy_or_sale = 'sale'
                " . ($date == null ? "" : " and ass.as_date < '$date'") . "
            ) as sale_asset,

            /* paid */
            (
                select ifnull(sum(pm.PurchaseMaster_PaidAmount), 0) from tbl_purchasemaster pm
                where pm.status = 'a'
                and pm.PurchaseMaster_BranchID= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
            ) as paid_purchase,
            (
                select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                where sp.SPayment_TransactionType = 'CP'
                and sp.SPayment_status = 'a'
                and sp.SPayment_Paymentby != 'bank'
                and sp.SPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
            ) as paid_supplier,
            (
                select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                where cp.CPayment_TransactionType = 'CP'
                and cp.CPayment_status = 'a'
                and cp.CPayment_Paymentby != 'bank'
                and cp.CPayment_brunchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
            ) as paid_customer,
            (
                select ifnull(sum(ct.Out_Amount), 0) from tbl_cashtransaction ct
                where ct.Tr_Type = 'Out Cash'
                and ct.status = 'a'
                and ct.Tr_branchid= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and ct.Tr_date < '$date'") . "
            ) as paid_cash,
            (
                select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                where bt.transaction_type = 'deposit'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as bank_deposit,
            (
                select ifnull(sum(ep.total_payment_amount), 0) from tbl_employee_payment ep
                where ep.branch_id = " . $this->session->userdata('BRANCHid') . "
                and ep.status = 'a'
                " . ($date == null ? "" : " and ep.payment_date < '$date'") . "
            ) as employee_payment,
            (
                select ifnull(sum(bt.amount), 0) from tbl_loan_transactions bt
                where bt.transaction_type = 'Payment'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as loan_payment,
            (
                select ifnull(sum(bt.amount), 0) from tbl_investment_transactions bt
                where bt.transaction_type = 'Payment'
                and bt.status = 1
                and bt.branch_id= " . $this->session->userdata('BRANCHid') . "
                " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
            ) as invest_payment,
            (
                select ifnull(sum(ass.as_amount), 0) from tbl_assets ass
                where ass.branchid = " . $this->session->userdata('BRANCHid') . "
                and ass.status = 'a'
                and ass.buy_or_sale = 'buy'
                " . ($date == null ? "" : " and ass.as_date < '$date'") . "
            ) as buy_asset,
            /* total */
            (
                select received_sales + exchange_sales + received_customer + received_supplier + received_cash + bank_withdraw + loan_received + loan_initial_balance + invest_received + sale_asset
            ) as total_in,
            (
                select paid_purchase + paid_customer + paid_supplier + paid_cash + bank_deposit + employee_payment + loan_payment + invest_payment + buy_asset
            ) as total_out,
            (
                select total_in - total_out
            ) as cash_balance
        ")->row();

        return $transactionSummary;
    }

    public function getBankTransactionSummary($accountId = null, $date = null)
    {
        $bankTransactionSummary = $this->db->query("
            select 
                ba.*,
                (
                    select ifnull(sum(ex.bankPaid), 0) from tbl_exchange ex
                    where ex.bank_id = ba.account_id
                    and ex.Status = 'a'
                    and ex.branchId = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and ex.date < '$date'") . "
                ) as exchange_sales,
                
                (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.account_id = ba.account_id
                    and bt.transaction_type = 'deposit'
                    and bt.status = 1
                    and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as total_deposit,
                (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.account_id = ba.account_id
                    and bt.transaction_type = 'withdraw'
                    and bt.status = 1
                    and bt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as total_withdraw,
                 (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.account_id = ba.account_id
                    and bt.transaction_type = 'transfer'
                    and bt.status = 1
                    " . (!empty($branchId) ? "and bt.branch_id = '$branchId'" : "") . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as bank_transfer_out,
                (
                    select ifnull(sum(bt.amount), 0) from tbl_bank_transactions bt
                    where bt.transfer_account_id = ba.account_id
                    and bt.transaction_type = 'transfer'
                    and bt.status = 1
                    " . (!empty($branchId) ? "and bt.branch_id = '$branchId'" : "") . "
                    " . ($date == null ? "" : " and bt.transaction_date < '$date'") . "
                ) as bank_transfer_in,
                (
                    select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                    where cp.account_id = ba.account_id
                    and cp.CPayment_status = 'a'
                    and cp.CPayment_TransactionType = 'CR'
                    and cp.CPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                ) as total_received_from_customer,
                (
                    select ifnull(sum(cp.CPayment_amount), 0) from tbl_customer_payment cp
                    where cp.account_id = ba.account_id
                    and cp.CPayment_status = 'a'
                    and cp.CPayment_TransactionType = 'CP'
                    and cp.CPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                ) as total_paid_to_customer,
                (
                    select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                    where sp.account_id = ba.account_id
                    and sp.SPayment_status = 'a'
                    and sp.SPayment_TransactionType = 'CP'
                    and sp.SPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
                ) as total_paid_to_supplier,
                (
                    select ifnull(sum(sp.SPayment_amount), 0) from tbl_supplier_payment sp
                    where sp.account_id = ba.account_id
                    and sp.SPayment_status = 'a'
                    and sp.SPayment_TransactionType = 'CR'
                    and sp.SPayment_brunchid = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
                ) as total_received_from_supplier,
                (
                    select (ba.initial_balance + total_deposit + bank_transfer_in + exchange_sales + total_received_from_customer + total_received_from_supplier) - (total_withdraw + bank_transfer_out + total_paid_to_customer + total_paid_to_supplier)
                ) as balance
            from tbl_bank_accounts ba
            where ba.branch_id = " . $this->session->userdata('BRANCHid') . "
            " . ($accountId == null ? "" : " and ba.account_id = '$accountId'") . "
        ")->result();

        return $bankTransactionSummary;
    }

    public function generateInvestmentAccountCode()
    {
        $code = "I0001";

        $lastRow = $this->db->query("select * from tbl_investment_account order by Acc_SlNo desc limit 1");
        if ($lastRow->num_rows() != 0) {
            $newCode = $lastRow->row()->Acc_SlNo + 1;
            $zeros = array('0', '00', '000');
            $code = 'I' . (strlen($newCode) > count($zeros) ? $newCode : $zeros[count($zeros) - strlen($newCode)] . $newCode);
        }

        return $code;
    }

    public function getLoanTransactionSummary($accountId = null, $date = null)
    {
        $loanTransactionSummary = $this->db->query("
            select 
                la.*,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_loan_transactions lt
                    where lt.account_id = la.account_id
                    and lt.transaction_type = 'Payment'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_payment,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_loan_transactions lt
                    where lt.account_id = la.account_id
                    and lt.transaction_type = 'Receive'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_received,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_loan_transactions lt
                    where lt.account_id = la.account_id
                    and lt.transaction_type = 'Interest'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_interest,
                (
                    select (la.initial_balance + total_received + total_interest) - (total_payment)

                ) as balance

            from tbl_loan_accounts la
            where la.branch_id = " . $this->session->userdata('BRANCHid') . "
            " . ($accountId == null ? "" : " and la.account_id = '$accountId'") . "
        ")->result();

        return $loanTransactionSummary;
    }

    public function getInvestmentTransactionSummary($accountId = null, $date = null)
    {
        $investmentTransactionSummary = $this->db->query("
            select 
                la.*,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_investment_transactions lt
                    where lt.account_id = la.Acc_SlNo
                    and lt.transaction_type = 'Payment'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_payment,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_investment_transactions lt
                    where lt.account_id = la.Acc_SlNo
                    and lt.transaction_type = 'Receive'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_received,
                (
                    select ifnull(sum(lt.amount), 0) from tbl_investment_transactions lt
                    where lt.account_id = la.Acc_SlNo
                    and lt.transaction_type = 'Profit'
                    and lt.status = 1
                    and lt.branch_id = " . $this->session->userdata('BRANCHid') . "
                    " . ($date == null ? "" : " and lt.transaction_date < '$date'") . "
                ) as total_profit,
                (
                    select (total_received + total_profit) - (total_payment)

                ) as balance

            from tbl_investment_account la
            where la.branch_id = " . $this->session->userdata('BRANCHid') . "
            and la.status = 'a'
            " . ($accountId == null ? "" : " and la.Acc_SlNo = '$accountId'") . "
        ")->result();

        return $investmentTransactionSummary;
    }

    public function assetsReport($clauses = '', $date = null)
    {
        $branchId = $this->session->userdata('BRANCHid');

        $assets = $this->db->query("
            SELECT a.as_name as group_name,
            ( SELECT ifnull( sum(as_qty) , 0) 
                from tbl_assets
                where as_name = a.as_name
                and buy_or_sale = 'buy'
                and status = 'a'
                and branchid = '$branchId'
                " . ($date == null ? "" : " and as_date < '$date'") . "
            ) as purchase_qty,

            ( SELECT ifnull( sum(as_qty) , 0) 
                from tbl_assets
                where as_name = a.as_name
                and buy_or_sale = 'sale'
                and status = 'a'
                and branchid = '$branchId'
                " . ($date == null ? "" : " and as_date < '$date'") . "
            ) as sold_qty,

            ( SELECT ifnull( sum(as_amount) , 0) 
                from tbl_assets
                where as_name = a.as_name
                and buy_or_sale = 'buy'
                and status = 'a'
                and branchid = '$branchId'
                " . ($date == null ? "" : " and as_date < '$date'") . "
            ) as purchase_amount,

            ( SELECT ifnull( sum(as_amount) , 0) 
                from tbl_assets
                where as_name = a.as_name
                and buy_or_sale = 'sale'
                and status = 'a'
                and branchid = '$branchId'
                " . ($date == null ? "" : " and as_date < '$date'") . "
            ) as sold_amount,

            ( SELECT ifnull( sum(valuation) , 0) 
                from tbl_assets
                where as_name = a.as_name
                and buy_or_sale = 'sale'
                and status = 'a'
                and branchid = '$branchId'
                " . ($date == null ? "" : " and as_date < '$date'") . "
            ) as valuation_amount,

            ( SELECT (purchase_qty - sold_qty) ) as available_qty,
            ( SELECT (purchase_amount - valuation_amount) ) as approx_amount

            from tbl_assets as a
            where a.status = 'a'
            and a.branchid = '$branchId'
            $clauses
            group by as_name
        ")->result();

        return $assets;
    }

    public function currentStock($clauses = '')
    {
        $stock = $this->db->query("
            select * from(
                select
                    ci.*,
                    (select (ci.purchase_quantity + ci.sales_return_quantity + ci.transfer_to_quantity) - (ci.sales_quantity + ci.purchase_return_quantity + ci.damage_quantity + ci.transfer_from_quantity)) as current_quantity,
                    p.Product_Name,
                    p.Product_Code,
                    p.Product_ReOrederLevel,
                    p.Product_Purchase_Rate,
                    (select (p.Product_Purchase_Rate * current_quantity)) as stock_value,
                    pc.ProductCategory_Name,
                    b.brand_name,
                    u.Unit_Name
                from tbl_currentinventory ci
                join tbl_product p on p.Product_SlNo = ci.product_id
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_brand b on b.brand_SiNo = p.brand
                left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                where p.status = 'a'
                and p.is_service = 'false'
                and ci.branch_id = ?
            ) as tbl
            where 1 = 1
            $clauses
        ", $this->session->userdata("BRANCHid"))->result();

        foreach ($stock as $item) {
            $item->expire_stocks = $this->expStock($item->product_id, true, $this->session->userdata("BRANCHid"));
        }

        return $stock;
    }

    public function productStock($productId)
    {
        $stockQuery = $this->db->query("select * from tbl_currentinventory where product_id = ? and branch_id = ?", [$productId, $this->session->userdata("BRANCHid")]);
        $stockCount = $stockQuery->num_rows();
        $stock = 0;
        if ($stockCount != 0) {
            $stockRow = $stockQuery->row();
            $stock = ($stockRow->purchase_quantity + $stockRow->transfer_to_quantity + $stockRow->sales_return_quantity)
                - ($stockRow->sales_quantity + $stockRow->purchase_return_quantity + $stockRow->damage_quantity + $stockRow->transfer_from_quantity);
        }

        return $stock;
    }

    public function expStock($productId, $withoutZero = false, $branchId = null)
    {
        if ($branchId == null) {
            $branchId = $this->session->userdata("BRANCHid");
        }

        $stock = $this->db
            ->query("
                select
                ifnull(exp_date, 'N/A') as exp_date,
                ifnull(sum(in_quantity), 0) as in_quantity, 
                ifnull(sum(out_quantity), 0) as out_quantity,
                ifnull(sum(in_quantity), 0) - ifnull(sum(out_quantity), 0) as stock
                from(
                    select
                    'purchase' as sequence,
                    pd.exp_date,
                    ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_purchasedetails pd
                    where pd.Product_IDNo = '$productId'
                    and pd.Status = 'a'
                    and pd.PurchaseDetails_branchID = '$branchId'
                    group by pd.exp_date
                    
                    UNION
                    select
                    'purchase_return' as sequence,
                    prd.exp_date,
                    0 in_quantity,
                    ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) as out_quantity
                    from tbl_purchasereturndetails prd
                    where prd.PurchaseReturnDetailsProduct_SlNo = '$productId'
                    and prd.Status = 'a'
                    and prd.PurchaseReturnDetails_brachid = '$branchId'
                    group by prd.exp_date

                    UNION
                    select
                    'sale' as sequence,
                    sd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as out_quantity
                    from tbl_saledetails sd
                    where sd.Product_IDNo = '$productId'
                    and sd.Status = 'a'
                    and sd.SaleDetails_BranchId = '$branchId'
                    group by sd.exp_date
                    
                    UNION
                    select
                    'sale_return' as sequence,
                    srd.exp_date,
                    ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_salereturndetails srd
                    where srd.SaleReturnDetailsProduct_SlNo = '$productId'
                    and srd.Status = 'a'
                    and srd.SaleReturnDetails_brunchID = '$branchId'
                    group by srd.exp_date
                    
                    UNION
                    select
                    'damage' as sequence,
                    dd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(dd.DamageDetails_DamageQuantity), 0) as out_quantity
                    from tbl_damagedetails dd
                    join tbl_damage dm on dm.Damage_SlNo = dd.Damage_SlNo
                    where dd.Product_SlNo = '$productId'
                    and dd.status = 'a'
                    and dm.Damage_brunchid = '$branchId'
                    group by dd.exp_date
                    
                    UNION
                    select
                    'transfer_in' as sequence,
                    trd.exp_date,
                    ifnull(sum(trd.quantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '$productId'
                    and tm.transfer_to = '$branchId'
                    group by trd.exp_date
                    
                    UNION
                    select
                    'transfer_out' as sequence,
                    trd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(trd.quantity), 0) as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '$productId'
                    and tm.transfer_from = '$branchId'
                    group by trd.exp_date
                    ) as tbl
                    group by exp_date
                    " . ($withoutZero == true ? "" : "having stock > 0") . "
                    order by exp_date, sequence asc")->result();

        if ($productId != null) {
            foreach ($stock as $item) {
                $item->barcode = $this->db->select('barcode')->where('exp_date', $item->exp_date)->where('Product_IDNo', $productId)->get("tbl_purchasedetails")->row()->barcode;
            }
        }

        return $stock;
    }

    public function supplierDue($clauses = "", $date = null)
    {
        $branchId = $this->session->userdata('BRANCHid');

        $supplierDues = $this->db->query("
            select
            s.Supplier_SlNo,
            s.Supplier_Code,
            s.Supplier_Name,
            s.Supplier_Mobile,
            s.Supplier_Address,
            s.contact_person,
            (select (ifnull(sum(pm.PurchaseMaster_TotalAmount), 0.00) + ifnull(s.previous_due, 0.00)) from tbl_purchasemaster pm
                where pm.Supplier_SlNo = s.Supplier_SlNo
                " . ($date == null ? "" : " and pm.PurchaseMaster_OrderDate < '$date'") . "
                and pm.status = 'a'
            ) as bill,

            (select ifnull(sum(pm2.PurchaseMaster_PaidAmount), 0.00) from tbl_purchasemaster pm2
                where pm2.Supplier_SlNo = s.Supplier_SlNo
                " . ($date == null ? "" : " and pm2.PurchaseMaster_OrderDate < '$date'") . "
                and pm2.status = 'a'
            ) as invoicePaid,

            (select ifnull(sum(sp.SPayment_amount), 0.00) from tbl_supplier_payment sp 
                where sp.SPayment_customerID = s.Supplier_SlNo 
                and sp.SPayment_TransactionType = 'CP'
                " . ($date == null ? "" : " and sp.SPayment_date < '$date'") . "
                and sp.SPayment_status = 'a'
            ) as cashPaid,
                
            (select ifnull(sum(sp2.SPayment_amount), 0.00) from tbl_supplier_payment sp2 
                where sp2.SPayment_customerID = s.Supplier_SlNo 
                and sp2.SPayment_TransactionType = 'CR'
                " . ($date == null ? "" : " and sp2.SPayment_date < '$date'") . "
                and sp2.SPayment_status = 'a'
            ) as cashReceived,

            (select ifnull(sum(pr.PurchaseReturn_ReturnAmount), 0.00) from tbl_purchasereturn pr
                join tbl_purchasemaster rpm on rpm.PurchaseMaster_InvoiceNo = pr.PurchaseMaster_InvoiceNo
                where rpm.Supplier_SlNo = s.Supplier_SlNo
                " . ($date == null ? "" : " and pr.PurchaseReturn_ReturnDate < '$date'") . "
            ) as returned,
            
            (select invoicePaid + cashPaid) as paid,
            
            (select (bill + cashReceived) - (paid + returned)) as due

            from tbl_supplier s
            where s.Supplier_brinchid = '$branchId' $clauses
        ")->result();

        return $supplierDues;
    }

    public function customerDue($clauses = "", $date = null)
    {
        $branchId = $this->session->userdata('BRANCHid');
        $dueResult = $this->db->query("
            select
            c.Customer_SlNo,
            c.Customer_Name,
            c.Customer_Code,
            c.Customer_Address,
            c.Customer_Mobile,
            c.owner_name,
            (select ifnull(sum(sm.SaleMaster_TotalSaleAmount), 0.00) + ifnull(c.previous_due, 0.00) 
                from tbl_salesmaster sm 
                where sm.SalseCustomer_IDNo = c.Customer_SlNo
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
                and sm.Status = 'a') as billAmount,

            (select ifnull(sum(sm.SaleMaster_PaidAmount - sm.returnAmount), 0.00)
                from tbl_salesmaster sm
                where sm.SalseCustomer_IDNo = c.Customer_SlNo
                " . ($date == null ? "" : " and sm.SaleMaster_SaleDate < '$date'") . "
                and sm.Status = 'a') as invoicePaid,

            (select ifnull(sum(cp.CPayment_amount), 0.00) 
                from tbl_customer_payment cp 
                where cp.CPayment_customerID = c.Customer_SlNo 
                and cp.CPayment_TransactionType = 'CR'
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                and cp.CPayment_status = 'a') as cashReceived,

            (select ifnull(sum(cp.CPayment_amount), 0.00) 
                from tbl_customer_payment cp 
                where cp.CPayment_customerID = c.Customer_SlNo 
                and cp.CPayment_TransactionType = 'CP'
                " . ($date == null ? "" : " and cp.CPayment_date < '$date'") . "
                and cp.CPayment_status = 'a') as paidOutAmount,
                
            (select ifnull(sum(sr.SaleReturn_ReturnAmount), 0.00) 
                from tbl_salereturn sr 
                join tbl_salesmaster smr on smr.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo 
                where smr.SalseCustomer_IDNo = c.Customer_SlNo 
                " . ($date == null ? "" : " and sr.SaleReturn_ReturnDate < '$date'") . "
                and sr.Status = 'a'
            ) as returnedAmount,

            (select invoicePaid + cashReceived) as paidAmount,

            (select (billAmount + paidOutAmount) - (paidAmount + returnedAmount)) as dueAmount
            
            from tbl_customer c
            where c.Customer_brunchid = '$branchId' $clauses
        ")->result();

        return $dueResult;
    }

    public function save_data($table, $data)
    {
        $result = $this->db->insert($table, $data);
        if ($result) {
            $this->Id = $this->db->insert_id();
            return TRUE;
        }
        $this->Err = mysql_error();
        return FALSE;
    }

    public function insert_payment($table, $data)
    {
        $this->db->insert($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;
    }


    public function save_date_id($table, $data)
    {
        $this->db->insert($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;
    }
    public function update_customer_data($table, $data, $id)
    {
        $this->db->where("fld_id", $id);
        $result = $this->db->update($table, $data);
        $id = $this->db->insert_id();
        return (isset($id)) ? $id : FALSE;
    }
    public function update_data($table, $data, $id, $fld)
    {
        $this->db->where($fld, $id);
        $result = $this->db->update($table, $data);
        if (!$result) {
            return FALSE;
        }
        return TRUE;
    }

    public function delete_data($table, $id, $fld)
    {
        $data['status'] = 'd';
        $this->db->where($fld, $id);
        // $result= $this->db->delete($table);
        $result = $this->db->update($table, $data);
        if (!$result) {
            return FALSE;
        }
        return TRUE;
    }

    public function select_by_Booking_id($id)
    {
        $sql = mysql_query("SELECT tbl_booking_bill.*,tbl_booking_bill.fld_id as ordID, tbl_booking_customer.*, tbl_booking_customer.fld_id as cusID, tbl_cash_receive.*, tbl_cash_receive.fld_id as cashR_ID FROM tbl_booking_bill LEFT JOIN tbl_booking_customer ON tbl_booking_customer.fld_id=tbl_booking_bill.fld_customer_id left join tbl_cash_receive on tbl_booking_bill.fld_id =tbl_cash_receive.fld_order_id  where tbl_booking_bill.fld_id = '" . $id . "'");
        while ($d = mysql_fetch_array($sql)) {
            return $d;
        }
    }
    public function edit_by_id($query)
    {
        $sql = mysql_query($query);
        while ($d = mysql_fetch_array($sql)) {
            return $d;
        }
    }

    public function select_by_id($table, $id, $fld)
    {
        $sql = $this->db->query("SELECT * from {$table} where {$fld} = '" . $id . "'")->row();
        return (array)$sql;
    }

    public function view_data($table)
    {
        $a = array();
        $sql = mysql_query($table);
        while ($d = mysql_fetch_array($sql)) {
            $a[] = $d;
        }
        return $a;
    }


    public function ccdata($data)
    {
        $a = array();
        $sql = mysql_query($data);
        while ($d = mysql_fetch_array($sql)) {
            $a[] = $d;
        }
        return $a;
    }


    public function getBrunchNameById($id)
    {
        $q = $this->db->where('brunch_id', $id)->get('tbl_brunch')->row();
        if ($q)
            return $q->Brunch_name;
        return false;
    }


    // upload image
    public function uploadImage($imgFile, $image, $dirName, $fileName = null)
    {
        // directory create
        if (!file_exists($dirName)) {
            getcwd() . '/' . mkdir($dirName, 0777, true);
        }
        // upload image
        $name = basename($imgFile[$image]["name"]);
        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
        $fileNewName = str_replace(" ", "_", $fileName) . '_' . uniqid() . '.' . $file_ext;
        $target_file = $dirName . '/' . $fileNewName;
        if (move_uploaded_file($imgFile[$image]["tmp_name"], $target_file)) {
            return $target_file;
        }
    }
}
