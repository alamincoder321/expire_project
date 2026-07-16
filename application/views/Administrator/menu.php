<?php
$userID =  $this->session->userdata('userId');
$CheckSuperAdmin = $this->db->where('UserType', 'm')->where('User_SlNo', $userID)->get('tbl_user')->row();

$CheckAdmin = $this->db->where('UserType', 'a')->where('User_SlNo', $userID)->get('tbl_user')->row();

$userAccessQuery = $this->db->where('user_id', $userID)->get('tbl_user_access');
$access = [];
if ($userAccessQuery->num_rows() != 0) {
	$userAccess = $userAccessQuery->row();
	$access = json_decode($userAccess->access);
}

$panel = $this->session->userdata('panel');
if ($panel == 'dashboard' or $panel == '') {
?>
	<ul class="nav nav-list">
		<li class="active">
			<!-- panel/dashboard -->
			<a href="<?php echo base_url(); ?>">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>panel/SalesPanel">
				<i class="menu-icon fa fa-shopping-cart" style="font-size:23px;"></i>
				<span class="menu-text"> Manage Sales </span>
			</a>
			<b class="arrow"></b>
		</li>

		<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>panel/PurchasePanel">
					<i class="menu-icon fa fa-cart-plus" style="font-size:23px;"></i>
					<span class="menu-text"> Manage Purchase </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<li class="">
			<!--  -->
			<a href="<?php echo base_url(); ?>panel/AccountsPanel">
				<i class="menu-icon fa fa fa-money" style="font-size: 16px;"></i>
				<span class="menu-text"> Manage Accounts </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<!-- panel/HRMPanel -->
			<a href="<?php echo base_url(); ?>panel/HRMPanel">
				<i class="menu-icon fa fa-users"></i>
				<span class="menu-text"> Manage HRM </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<!-- panel/ReportsPanel -->
			<a href="<?php echo base_url(); ?>panel/ReportsPanel">
				<i class="menu-icon fa fa-calendar-check-o"></i>
				<span class="menu-text"> Manage Reports </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>panel/Administration">
				<i class="menu-icon fa fa-university"></i>
				<span class="menu-text"> Administration </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>graph">
				<i class="menu-icon fa fa-bar-chart"></i>
				<span class="menu-text"> Business View </span>
			</a>
			<b class="arrow"></b>
		</li>
	</ul>
<?php } elseif ($panel == 'Administration') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>
			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/Administration" style="background:gray !important;" class="panel_title">
				<span>Administration</span>
			</a>
		</li>

		<?php if (array_search("sms", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sms">
					<i class="menu-icon fa fa-mobile"></i>
					<span class="menu-text"> Send SMS </span>
				</a>
				<b class="arrow"></b>
			</li>
			<li class="">
				<a href="<?php echo base_url(); ?>pages/terms_condition/Terms%20Conditions">
					<i class="menu-icon fa-solid fa-image"></i>
					<span class="menu-text"> Terms & Conditions </span>
				</a>
				<b class="arrow"></b>
			</li>
			<li class="">
				<a href="<?php echo base_url(); ?>pages/return_refund/Return%20Refund">
					<i class="menu-icon fa-solid fa-image"></i>
					<span class="menu-text"> Return & Refund </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("product", $access) > -1
			|| array_search("productlist", $access) > -1
			|| array_search("product_ledger", $access) > -1
			|| isset($CheckSuperAdmin)
			|| isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-product-hunt"></i>
					<span class="menu-text"> Product Info </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Entry
							</a>

							<b class="arrow"></b>
						</li>
					<?php endif; ?>


					<?php if (array_search("productlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>productlist">
								<i class="menu-icon fa fa-caret-right"></i>
								Product List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("product_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<!-- <?php if (array_search("campaign_products", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>campaign_products">
								<i class="menu-icon fa fa-caret-right"></i>
								Campaign Products
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("campaignlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>campaignlist">
								<i class="menu-icon fa fa-caret-right"></i>
								Campaign List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?> -->
				</ul>
			</li>
		<?php endif; ?>
		<?php if (
			array_search("damageEntry", $access) > -1
			|| array_search("damageList", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Damage Info </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("damageEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>damageEntry">
								<i class="menu-icon fa fa-caret-right"></i>
								Damage Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("damageList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>damageList">
								<i class="menu-icon fa fa-caret-right"></i>
								Damage List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>

		<!-- <?php if (
					array_search("product_transfer", $access) > -1
					|| array_search("transfer_list", $access) > -1
					|| array_search("received_list", $access) > -1
					|| isset($CheckSuperAdmin) || isset($CheckAdmin)
				) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-exchange"></i>
					<span class="menu-text"> Product Transfer </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("product_transfer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product_transfer">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Transfer
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
					<?php if (array_search("transfer_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>transfer_list">
								<i class="menu-icon fa fa-caret-right"></i>
								Transfer List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("received_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>received_list">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Received List</span>
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?> -->

		<?php if (
			array_search("customer", $access) > -1
			|| array_search("supplier", $access) > -1
			|| array_search("brunch", $access) > -1
			|| array_search("category", $access) > -1
			|| array_search("unit", $access) > -1
			|| array_search("area", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-cog"></i>
					<span class="menu-text"> Settings </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("customer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>customer">
								<i class="menu-icon fa fa-caret-right"></i>
								Customer Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("supplier", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>supplier">
								<i class="menu-icon fa fa-caret-right"></i>
								Supplier Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("category", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>category">
								<i class="menu-icon fa fa-caret-right"></i>
								Category entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("unit", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>unit">
								<i class="menu-icon fa fa-caret-right"></i>
								Unit Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("area", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>area">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Add Area </span>
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<!-- <?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>branch">
					<i class="menu-icon fa fa-bank"></i>
					<span class="menu-text"> Branch Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?> -->

		<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>companyProfile">
					<i class="menu-icon fa fa-cogs"></i>
					<span class="menu-text"> Company Profile </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>user">
					<i class="menu-icon fa fa-user"></i>
					<span class="menu-text"> User Entry </span>
				</a>
			</li>
		<?php endif; ?>

		<?php if (array_search("database_backup", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>database_backup">
					<i class="menu-icon fa fa-database"></i>
					<span class="menu-text"> Database Backup </span>
				</a>
			</li>
		<?php endif; ?>

	</ul><!-- /.nav-list -->

<?php } elseif ($panel == 'SalesPanel') { ?>
	<ul class="nav nav-list">

		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/SalesPanel" style="background:gray !important;" class="panel_title">
				<span> Manage Sales </span>
			</a>
		</li>

		<?php if (array_search("sales/product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sales/product">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Sales Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>


		<!-- <?php if (array_search("sales/service", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sales/service">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Service Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?> -->

		<?php if (array_search("salesrecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesrecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Sales Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("exchange", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>exchange">
					<i class="menu-icon fa fa-exchange"></i>
					<span class="menu-text"> Sales Exchange </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("exchange_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>exchange_record">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Exchange Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("salesinvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesinvoice">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text"> Sales Invoice </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("salesReturn", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesReturn">
					<i class="menu-icon fa fa-rotate-left"></i>
					<span class="menu-text"> Sale Return </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("returnList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>returnList">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Sale Return Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("special_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>special_report">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Special Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<!-- <?php if (array_search("quotation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Quotation Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("quotation_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation_record">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Quotation Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("quotation_invoice_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation_invoice_report">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text"> Quotation Invoice </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?> -->

		<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>currentStock">
					<i class="menu-icon fa fa-shopping-basket"></i>
					<span class="menu-text"> Stock Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

	</ul>

<?php } elseif ($panel == 'PurchasePanel') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/PurchasePanel" style="background:gray !important;" class="panel_title">
				<span>Manage Purchase</span>
			</a>
		</li>

		<?php if (array_search("purchase_order", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchase_order">
					<i class="menu-icon fa fa-cart-plus"></i>
					<span class="menu-text"> Purchase Order </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseorderRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseorderRecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text">Order Record</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchase", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchase">
					<i class="menu-icon fa fa-cart-plus"></i>
					<span class="menu-text"> Purchase Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseRecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text">Purchase Record</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseInvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseInvoice">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text">Purchase Invoice</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseReturns", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseReturns">
					<i class="menu-icon fa fa-rotate-right"></i>
					<span class="menu-text"> Purchase Return </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("returnsList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>returnsList">
					<i class="menu-icon fa fa-list"></i>
					Pur. Return Record
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("AssetsEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>AssetsEntry">
					<i class="menu-icon fa fa-shopping-cart"></i>
					<span class="menu-text"> Assets Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("assets_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>assets_report">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Assets Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
	</ul>

<?php } elseif ($panel == 'AccountsPanel') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/AccountsPanel" style="background:gray !important;" class="panel_title">
				<span> Manage Account </span>
			</a>
		</li>

		<?php if (array_search("cashTransaction", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cashTransaction">
					<i class="menu-icon fa fa-medkit"></i>
					<span class="menu-text"> Cash Transaction </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("bank_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li>
				<a href="<?php echo base_url(); ?>bank_transactions">
					<i class="menu-icon fa fa-dollar"></i>
					<span class="menu-text"> Bank Transactions </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("customerPaymentPage", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>customerPaymentPage">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Payment Received</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("supplierPayment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>supplierPayment">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Supplier Payment </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cash_view">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text">Cash View</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("investment_transactions", $access) > -1
			|| array_search("investment_transaction_report", $access) > -1
			|| array_search("investment_view", $access) > -1
			|| array_search("investment_ledger", $access) > -1
			|| array_search("investment_account", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Investment </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("investment_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_transactions">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Transection
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_view">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment View
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_account", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_account">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Account
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("account", $access) > -1
			|| array_search("bank_accounts", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li>
				<a href="" class="dropdown-toggle">
					<i class="menu-icon fa fa-bank"></i>
					<span class="menu-text"> Account Head </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("account", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li>
							<a href="<?php echo base_url(); ?>account">
								<i class="menu-icon fa fa-caret-right"></i>
								Transaction Accounts
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
					<?php if (array_search("bank_accounts", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li>
							<a href="<?php echo base_url(); ?>bank_accounts">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Accounts
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("TransactionReport", $access) > -1
			|| array_search("bank_transaction_report", $access) > -1
			|| array_search("cash_ledger", $access) > -1
			|| array_search("bank_ledger", $access) > -1
			|| array_search("cashStatment", $access) > -1
			|| array_search("BalanceSheet", $access) > -1
			|| array_search("balance_sheet", $access) > -1
			|| array_search("day_book", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Reports </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>TransactionReport">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cash_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cash_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cashStatment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cashStatment">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Statement
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("balance_sheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>balance_sheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance Sheet
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("BalanceSheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>BalanceSheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance In Out
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("day_book", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>day_book">
								<i class="menu-icon fa fa-caret-right"></i>
								Day Book
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>


	</ul><!-- /.nav-list -->
<?php } elseif ($panel == 'HRMPanel') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/HRMPanel" style="background:gray !important;" class="panel_title">
				<span>Manage HRM</span>
			</a>
		</li>

		<?php if (array_search("salary_payment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salary_payment">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Salary Payment </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("salary_payment_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salary_payment_report">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Payment Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("employee", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>employee">
					<i class="menu-icon fa fa-user-plus"></i>
					<span class="menu-text"> Employee Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		
		<?php if (array_search("emplists/all", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>emplists/all">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Employee List </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("designation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>designation">
					<i class="menu-icon fa fa-plus-circle"></i>
					<span class="menu-text"> Designation Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("depertment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>depertment">
					<i class="menu-icon fa fa-plus-circle"></i>
					<span class="menu-text"> Department Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("month", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>month">
					<i class="menu-icon fa fa-plus-circle"></i>
					<span class="menu-text"> Month Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
	</ul><!-- /.nav-list -->
<?php } elseif ($panel == 'ReportsPanel') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>panel/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>panel/ReportsPanel" style="background:gray !important;" class="panel_title">
				<span>Reports Manage</span>
			</a>
		</li>

		<?php if (array_search("profitLoss", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>profitLoss">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Profit & Loss Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cash_view">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text">Cash View</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>


		<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>currentStock">
					<i class="menu-icon fa fa-th-list"></i>
					<span class="menu-text"> Stock </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("TransactionReport", $access) > -1
			|| array_search("bank_transaction_report", $access) > -1
			|| array_search("cash_ledger", $access) > -1
			|| array_search("bank_ledger", $access) > -1
			|| array_search("cashStatment", $access) > -1
			|| array_search("BalanceSheet", $access) > -1
			|| array_search("day_book", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Reports </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>TransactionReport">
								<i class="menu-icon fa fa-caret-right"></i>
								CashTransaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cash_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cash_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<!-- <?php if (array_search("cashStatment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cashStatment">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Statement
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("BalanceSheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>BalanceSheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance In Out
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?> -->

					<?php if (array_search("day_book", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>day_book">
								<i class="menu-icon fa fa-caret-right"></i>
								Day Book
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>
	</ul><!-- /.nav-list -->
<?php } ?>