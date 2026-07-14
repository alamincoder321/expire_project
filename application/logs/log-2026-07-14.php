<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-07-14 20:04:03 --> Severity: Notice --> Undefined property: stdClass::$productId E:\xampp\htdocs\expire_project\application\controllers\Administrator\Purchase.php 442
ERROR - 2026-07-14 20:04:03 --> Severity: Notice --> Undefined property: stdClass::$productId E:\xampp\htdocs\expire_project\application\controllers\Administrator\Purchase.php 445
ERROR - 2026-07-14 20:04:03 --> Query error: Column 'Product_IDNo' cannot be null - Invalid query: INSERT INTO `tbl_purchasedetails` (`PurchaseMaster_IDNo`, `Product_IDNo`, `exp_date`, `short_date`, `barcode`, `PurchaseDetails_TotalQuantity`, `PurchaseDetails_Rate`, `PurchaseDetails_TotalAmount`, `isFree`, `Status`, `AddBy`, `AddTime`, `PurchaseDetails_branchID`) VALUES (2, NULL, '2026-07-31', '2026-07-14', '2026073100000', '500', '192.00', '96000.00', 'no', 'a', 'Admin', '2026-07-14 20:04:03', '1')
ERROR - 2026-07-14 20:04:22 --> Severity: Notice --> Undefined property: stdClass::$productId E:\xampp\htdocs\expire_project\application\controllers\Administrator\Purchase.php 442
ERROR - 2026-07-14 20:04:22 --> Severity: Notice --> Undefined property: stdClass::$productId E:\xampp\htdocs\expire_project\application\controllers\Administrator\Purchase.php 445
ERROR - 2026-07-14 20:04:22 --> Query error: Column 'Product_IDNo' cannot be null - Invalid query: INSERT INTO `tbl_purchasedetails` (`PurchaseMaster_IDNo`, `Product_IDNo`, `exp_date`, `short_date`, `barcode`, `PurchaseDetails_TotalQuantity`, `PurchaseDetails_Rate`, `PurchaseDetails_TotalAmount`, `isFree`, `Status`, `AddBy`, `AddTime`, `PurchaseDetails_branchID`) VALUES (3, NULL, '2026-07-31', '2026-07-14', '2026073100000', '500', '192.00', '96000.00', 'no', 'a', 'Admin', '2026-07-14 20:04:22', '1')
ERROR - 2026-07-14 20:24:36 --> Query error: Table 'expire_project.tbl_production_products' doesn't exist - Invalid query: 
                select
                ifnull(exp_date, 'N/A') as exp_date,
                ifnull(sum(in_quantity), 0) as in_quantity, 
                ifnull(sum(out_quantity), 0) as out_quantity,
                ifnull(sum(in_quantity), 0) - ifnull(sum(out_quantity), 0) as stock
                from(
                    select
                    'production' as sequence,
                    ppd.exp_date,
                    ifnull(sum(ppd.quantity), 0) in_quantity,
                    0 as out_quantity
                    from tbl_production_products ppd
                    where ppd.product_id = '8477'
                    and ppd.branch_id = '1'
                    group by ppd.exp_date

                    UNION
                    select
                    'purchase' as sequence,
                    pd.exp_date,
                    ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_purchasedetails pd
                    where pd.Product_IDNo = '8477'
                    and pd.Status = 'a'
                    and pd.PurchaseDetails_branchID = '1'
                    group by pd.exp_date
                    
                    UNION
                    select
                    'purchase_return' as sequence,
                    prd.exp_date,
                    0 in_quantity,
                    ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) as out_quantity
                    from tbl_purchasereturndetails prd
                    where prd.PurchaseReturnDetailsProduct_SlNo = '8477'
                    and prd.Status = 'a'
                    and prd.PurchaseReturnDetails_brachid = '1'
                    group by prd.exp_date

                    UNION
                    select
                    'sale' as sequence,
                    sd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as out_quantity
                    from tbl_saledetails sd
                    where sd.Product_IDNo = '8477'
                    and sd.Status = 'a'
                    and sd.SaleDetails_BranchId = '1'
                    group by sd.exp_date
                    
                    UNION
                    select
                    'sale_return' as sequence,
                    srd.exp_date,
                    ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_salereturndetails srd
                    where srd.SaleReturnDetailsProduct_SlNo = '8477'
                    and srd.Status = 'a'
                    and srd.SaleReturnDetails_brunchID = '1'
                    group by srd.exp_date
                    
                    UNION
                    select
                    'damage' as sequence,
                    dd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(dd.DamageDetails_DamageQuantity), 0) as out_quantity
                    from tbl_damagedetails dd
                    join tbl_damage dm on dm.Damage_SlNo = dd.Damage_SlNo
                    where dd.Product_SlNo = '8477'
                    and dd.status = 'a'
                    and dm.Damage_brunchid = '1'
                    group by dd.exp_date
                    
                    UNION
                    select
                    'transfer_in' as sequence,
                    trd.exp_date,
                    ifnull(sum(trd.quantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_to = '1'
                    group by trd.exp_date
                    
                    UNION
                    select
                    'transfer_out' as sequence,
                    trd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(trd.quantity), 0) as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_from = '1'
                    group by trd.exp_date
                    ) as tbl
                    group by exp_date
                    
                    order by exp_date, sequence asc
ERROR - 2026-07-14 20:24:44 --> Query error: Table 'expire_project.tbl_production_products' doesn't exist - Invalid query: 
                select
                ifnull(exp_date, 'N/A') as exp_date,
                ifnull(sum(in_quantity), 0) as in_quantity, 
                ifnull(sum(out_quantity), 0) as out_quantity,
                ifnull(sum(in_quantity), 0) - ifnull(sum(out_quantity), 0) as stock
                from(
                    select
                    'production' as sequence,
                    ppd.exp_date,
                    ifnull(sum(ppd.quantity), 0) in_quantity,
                    0 as out_quantity
                    from tbl_production_products ppd
                    where ppd.product_id = '8477'
                    and ppd.branch_id = '1'
                    group by ppd.exp_date

                    UNION
                    select
                    'purchase' as sequence,
                    pd.exp_date,
                    ifnull(sum(pd.PurchaseDetails_TotalQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_purchasedetails pd
                    where pd.Product_IDNo = '8477'
                    and pd.Status = 'a'
                    and pd.PurchaseDetails_branchID = '1'
                    group by pd.exp_date
                    
                    UNION
                    select
                    'purchase_return' as sequence,
                    prd.exp_date,
                    0 in_quantity,
                    ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) as out_quantity
                    from tbl_purchasereturndetails prd
                    where prd.PurchaseReturnDetailsProduct_SlNo = '8477'
                    and prd.Status = 'a'
                    and prd.PurchaseReturnDetails_brachid = '1'
                    group by prd.exp_date

                    UNION
                    select
                    'sale' as sequence,
                    sd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as out_quantity
                    from tbl_saledetails sd
                    where sd.Product_IDNo = '8477'
                    and sd.Status = 'a'
                    and sd.SaleDetails_BranchId = '1'
                    group by sd.exp_date
                    
                    UNION
                    select
                    'sale_return' as sequence,
                    srd.exp_date,
                    ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_salereturndetails srd
                    where srd.SaleReturnDetailsProduct_SlNo = '8477'
                    and srd.Status = 'a'
                    and srd.SaleReturnDetails_brunchID = '1'
                    group by srd.exp_date
                    
                    UNION
                    select
                    'damage' as sequence,
                    dd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(dd.DamageDetails_DamageQuantity), 0) as out_quantity
                    from tbl_damagedetails dd
                    join tbl_damage dm on dm.Damage_SlNo = dd.Damage_SlNo
                    where dd.Product_SlNo = '8477'
                    and dd.status = 'a'
                    and dm.Damage_brunchid = '1'
                    group by dd.exp_date
                    
                    UNION
                    select
                    'transfer_in' as sequence,
                    trd.exp_date,
                    ifnull(sum(trd.quantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_to = '1'
                    group by trd.exp_date
                    
                    UNION
                    select
                    'transfer_out' as sequence,
                    trd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(trd.quantity), 0) as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_from = '1'
                    group by trd.exp_date
                    ) as tbl
                    group by exp_date
                    
                    order by exp_date, sequence asc
ERROR - 2026-07-14 20:25:15 --> Query error: Unknown column 'dd.exp_date' in 'field list' - Invalid query: 
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
                    where pd.Product_IDNo = '8477'
                    and pd.Status = 'a'
                    and pd.PurchaseDetails_branchID = '1'
                    group by pd.exp_date
                    
                    UNION
                    select
                    'purchase_return' as sequence,
                    prd.exp_date,
                    0 in_quantity,
                    ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) as out_quantity
                    from tbl_purchasereturndetails prd
                    where prd.PurchaseReturnDetailsProduct_SlNo = '8477'
                    and prd.Status = 'a'
                    and prd.PurchaseReturnDetails_brachid = '1'
                    group by prd.exp_date

                    UNION
                    select
                    'sale' as sequence,
                    sd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as out_quantity
                    from tbl_saledetails sd
                    where sd.Product_IDNo = '8477'
                    and sd.Status = 'a'
                    and sd.SaleDetails_BranchId = '1'
                    group by sd.exp_date
                    
                    UNION
                    select
                    'sale_return' as sequence,
                    srd.exp_date,
                    ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_salereturndetails srd
                    where srd.SaleReturnDetailsProduct_SlNo = '8477'
                    and srd.Status = 'a'
                    and srd.SaleReturnDetails_brunchID = '1'
                    group by srd.exp_date
                    
                    UNION
                    select
                    'damage' as sequence,
                    dd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(dd.DamageDetails_DamageQuantity), 0) as out_quantity
                    from tbl_damagedetails dd
                    join tbl_damage dm on dm.Damage_SlNo = dd.Damage_SlNo
                    where dd.Product_SlNo = '8477'
                    and dd.status = 'a'
                    and dm.Damage_brunchid = '1'
                    group by dd.exp_date
                    
                    UNION
                    select
                    'transfer_in' as sequence,
                    trd.exp_date,
                    ifnull(sum(trd.quantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_to = '1'
                    group by trd.exp_date
                    
                    UNION
                    select
                    'transfer_out' as sequence,
                    trd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(trd.quantity), 0) as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_from = '1'
                    group by trd.exp_date
                    ) as tbl
                    group by exp_date
                    
                    order by exp_date, sequence asc
ERROR - 2026-07-14 20:25:17 --> Query error: Unknown column 'dd.exp_date' in 'field list' - Invalid query: 
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
                    where pd.Product_IDNo = '8477'
                    and pd.Status = 'a'
                    and pd.PurchaseDetails_branchID = '1'
                    group by pd.exp_date
                    
                    UNION
                    select
                    'purchase_return' as sequence,
                    prd.exp_date,
                    0 in_quantity,
                    ifnull(sum(prd.PurchaseReturnDetails_ReturnQuantity), 0) as out_quantity
                    from tbl_purchasereturndetails prd
                    where prd.PurchaseReturnDetailsProduct_SlNo = '8477'
                    and prd.Status = 'a'
                    and prd.PurchaseReturnDetails_brachid = '1'
                    group by prd.exp_date

                    UNION
                    select
                    'sale' as sequence,
                    sd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(sd.SaleDetails_TotalQuantity), 0) as out_quantity
                    from tbl_saledetails sd
                    where sd.Product_IDNo = '8477'
                    and sd.Status = 'a'
                    and sd.SaleDetails_BranchId = '1'
                    group by sd.exp_date
                    
                    UNION
                    select
                    'sale_return' as sequence,
                    srd.exp_date,
                    ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_salereturndetails srd
                    where srd.SaleReturnDetailsProduct_SlNo = '8477'
                    and srd.Status = 'a'
                    and srd.SaleReturnDetails_brunchID = '1'
                    group by srd.exp_date
                    
                    UNION
                    select
                    'damage' as sequence,
                    dd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(dd.DamageDetails_DamageQuantity), 0) as out_quantity
                    from tbl_damagedetails dd
                    join tbl_damage dm on dm.Damage_SlNo = dd.Damage_SlNo
                    where dd.Product_SlNo = '8477'
                    and dd.status = 'a'
                    and dm.Damage_brunchid = '1'
                    group by dd.exp_date
                    
                    UNION
                    select
                    'transfer_in' as sequence,
                    trd.exp_date,
                    ifnull(sum(trd.quantity), 0) as in_quantity,
                    0 as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_to = '1'
                    group by trd.exp_date
                    
                    UNION
                    select
                    'transfer_out' as sequence,
                    trd.exp_date,
                    0 as in_quantity,
                    ifnull(sum(trd.quantity), 0) as out_quantity
                    from tbl_transferdetails trd
                    join tbl_transfermaster tm on tm.transfer_id = trd.transfer_id
                    where trd.product_id = '8477'
                    and tm.transfer_from = '1'
                    group by trd.exp_date
                    ) as tbl
                    group by exp_date
                    
                    order by exp_date, sequence asc
ERROR - 2026-07-14 21:33:53 --> Severity: Warning --> A non-numeric value encountered E:\xampp\htdocs\expire_project\application\controllers\Login.php 91
ERROR - 2026-07-14 21:33:53 --> Severity: Warning --> A non-numeric value encountered E:\xampp\htdocs\expire_project\application\controllers\Login.php 92
ERROR - 2026-07-14 21:33:53 --> Severity: Warning --> deg2rad() expects parameter 1 to be float, string given E:\xampp\htdocs\expire_project\application\controllers\Login.php 95
